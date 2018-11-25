#!/usr/bin/python
# -*- coding: utf-8 -*-
import pymysql.cursors
import os
import pickle
import nltk
import sys
import re
from lib.validation import Validator
from lib.config import *
from nltk.tokenize import word_tokenize

# nltk.download('punkt')
# nltk.download('averaged_perceptron_tagger')


def get_connection():
    connection = pymysql.connect(host=DB_DATASETS['host'],
                                 user=DB_DATASETS['user'],
                                 password=DB_DATASETS['pass'],
                                 db=DB_DATASETS['dbname'],
                                 charset=DB_DATASETS['charset'],
                                 cursorclass=pymysql.cursors.DictCursor)

    return connection


def getQuery(category):
    if category == 5 or category == 6 or category == 7:
        query = "SELECT `doc_id`, `doc_text` FROM `src_documents` WHERE `justice_kind`=5 AND `judgment_code`=2 AND `instance_code`=3 AND `doc_id` NOT IN (SELECT `doc_id` FROM `ml_datasets` WHERE `category` IN (5, 6, 7))"  # адмінправопорушення 2
    elif category > 7 and category < 13:
        query = "SELECT `doc_id`, `doc_text` FROM `src_documents` WHERE `justice_kind`=1 AND `judgment_code`=5 AND  `instance_code`=3 AND `doc_id` NOT IN (SELECT `doc_id` FROM `ml_datasets` WHERE `category` IN (8, 9, 10, 11, 12))"  # цивільне 1
    elif category == 13 or category == 14 or category == 15:
        query = "SELECT `doc_id`, `doc_text` FROM `src_documents` WHERE `justice_kind`=1 AND `judgment_code`=3 AND `instance_code`=3 AND `doc_id` NOT IN (SELECT `doc_id` FROM `ml_datasets` WHERE `category` IN (13, 14, 15, 16))"  # цивільне 2
    elif category > 16 and category < 22:
        query = "SELECT `doc_id`, `doc_text` FROM `src_documents` WHERE `justice_kind`=2 AND `judgment_code`=5 AND `instance_code`=3 AND `doc_id` NOT IN (SELECT `doc_id` FROM `ml_datasets` WHERE `category` IN (17, 18, 19, 20, 21))"  # кримінальне 1
    elif category == 22 or category == 23:
        query = "SELECT `doc_id`, `doc_text` FROM `src_documents` WHERE `justice_kind`=2 AND `judgment_code`=1 AND `instance_code`=3 AND `doc_id` NOT IN (SELECT `doc_id` FROM `ml_datasets` WHERE `category` IN (22, 23, 24))"  # кримінальне 2
    else:
        print('category not implemented')
        sys.exit()

    return query


def getDocuments(query, limit):
    connection = get_connection()

    with connection.cursor() as cursor:
        sql = query + " LIMIT " + str(limit)
        cursor.execute(sql)
        data = cursor.fetchall()

    # list of your documents (each document is a string)
    return data


def putDocument(connection, doc_id, category):
    with connection.cursor() as cursor:
        sql = "INSERT INTO `ml_datasets` (`doc_id`, `category`, `doc_text`, `by_user`) VALUES (%s, %s, %s, %s)"
        cursor.execute(sql, (
        str(doc_id), str(category), 'немає, оскільки додано з ML', '100'))


def getClassifier1():
    # load our classifier from pickle
    open_file = open("pickles/28_29.pickle", "rb")
    classifier = pickle.load(open_file)
    open_file.close()
    return classifier


def getClassifier2():
    # load our classifier from pickle
    open_file = open("pickles/28_29_30.pickle", "rb")
    classifier = pickle.load(open_file)
    open_file.close()
    return classifier


def find_features(document):
    sent_detector = nltk.data.load('tokenizers/punkt/english.pickle')
    tokenized_words = sent_detector.tokenize(document)
    features = {}
    for w in tokenized_words:
        features[w] = (w in tokenized_words)
    return features


def sentiment(text):
    # firstly we find featureset for desired text
    feats = find_features(text)
    classifier1 = getClassifier1()
    classifier2 = getClassifier2()

    res1 = classifier1.prob_classify(feats)
    res2 = classifier2.prob_classify(feats)
    # if (res2.prob(30) * 100) > 70 :
    # 	print(str(title['doc_id']) + ",")
    # print(str(title['doc_id'])+'    25 = '+str(int(res1.prob(28) * 100))+'    26 = '+str(int(res1.prob(29) * 100))+' |  25 = '+str(int(res2.prob(28) * 100))+'    26 = '+str(int(res2.prob(29) * 100))+'    27 = '+str(int(res2.prob(30) * 100)))
    if (res1.prob(28) * 100) > 90 and (res2.prob(28) * 100) > 80:
        return 28
    elif (res1.prob(29) * 100) > 87 and (res2.prob(29) * 100) > 80:
        return 29
    elif ((res1.prob(29) * 100) < 75 and (res1.prob(28) * 100) < 75 and (
            res2.prob(29) * 100) < 60 and (res2.prob(28) * 100) < 60) or (
            (res2.prob(30) * 100) > 40):
        return 30
    print(str(title['doc_id']) + '    25 = ' + str(
        int(res1.prob(28) * 100)) + '    26 = ' + str(
        int(res1.prob(29) * 100)) + ' |  25 = ' + str(
        int(res2.prob(28) * 100)) + '    26 = ' + str(
        int(res2.prob(29) * 100)) + '    27 = ' + str(int(res2.prob(30) * 100)))
    return 0


if __name__ == '__main__':
    """
    To run script you need to type 'python add_category.py [category] [limit]' 
    in your terminal
    """

    if not len(sys.argv) == 3:
        print(
            'should by 2 arguments, where first is number of category and second is limit')
        sys.exit()
    category = sys.argv[1]
    limit = sys.argv[2]

    if any(not c.isdigit() for c in [category, limit]):
        print('All arguments should be digits')
        sys.exit()

    query = getQuery(int(category))
    documents = getDocuments(query, limit)
    validator = Validator('operative')
    clean_data = validator.validate_list(documents)
    connection = get_connection()
    for title in clean_data:
        category = sentiment(title['doc_text'])
        if (not category == 0) and category == 29:
            pass
            #putDocument(connection, title['doc_id'], category)
    connection.commit()
    connection.close()
    # print(str(title['doc_id'])+'   category = ' + str(category))
    # print(str(title['doc_id'])+'    25 = '+str(int(res.prob(28) * 100))+'    26 = '+str(int(res.prob(29) * 100))+'    30 = '+str(int(res.prob(30) * 100)))

    # pprint(documents)
    sys.exit()

    train_data = get_data(input_categories)
    new_classifier = train(train_data)
    dump_classifier(new_classifier, input_categories)
