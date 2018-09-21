#!/usr/bin/python
# -*- coding: utf-8 -*-
import json
import re

import pymysql.cursors
import os
import pickle
import nltk
import sys
import snowballstemmer

from nltk.tokenize import word_tokenize

from validation import Validator

#nltk.download('punkt')
#nltk.download('averaged_perceptron_tagger')

config = json.loads(open('config.json', 'r').read())

def get_connection():
    connection = pymysql.connect(host=config['db_datasets']['host'],
                                 user=config['db_datasets']['user'],
                                 password=config['db_datasets']['pass'],
                                 db=config['db_datasets']['dbname'],
                                 charset=config['db_datasets']['charset'],
                                 cursorclass=pymysql.cursors.DictCursor)

    return connection


def get_data(categories):
    connection = get_connection()

    with connection.cursor() as cursor:
        sql = "SELECT `ml_datasets`.`category`, `src_documents`.`doc_text` FROM `ml_datasets` INNER JOIN `src_documents` ON `ml_datasets`.`doc_id`=`src_documents`.`doc_id` WHERE `ml_datasets`.`category` IN {}".format(
            str(tuple(categories))
        )
        cursor.execute(sql)
        data = cursor.fetchall()

    # list of your documents (each document is a string)
    return data



def train(clean_data, flag = False):
    all_words = []
    sent_detector = nltk.data.load('tokenizers/punkt/english.pickle')

    for title in clean_data:
        # create an array of all sentences
        words = sent_detector.tokenize(title['doc_text'])
        #words = word_tokenize(title['doc_text'])

        # add part of speech to each word
        pos = nltk.pos_tag(words)
        for w in words:
            # w = ( "Word", 'RR')
            # all training sentences
            if len(w) > 8 :
                all_words.append(w)

    # save all descriptions with genre names
    all_words = nltk.FreqDist(all_words)
    word_features = [w for (w, c) in all_words.most_common(500)]
    if flag == '-w':
        print(all_words.most_common(100))
        sys.exit()

    def find_features(document):
        tokenized_words = sent_detector.tokenize(document)
        features = {}
        for w in word_features:
            features[w] = (w in tokenized_words)
        return features

    featuresets = [(find_features(title['doc_text']), title['category']) for
                   title in clean_data]
    # якщо стоїть флаг -p (подивитись точність)
    if flag == '-p' :
        training_set = featuresets[:int(len(featuresets) / 2)]
        testing_set = featuresets[int(len(featuresets) / 2):]

        classifier = nltk.NaiveBayesClassifier.train(training_set)

        print("Original Naive Bayes Algo accuracy percent:", (nltk.classify.accuracy(classifier, testing_set)) * 100)
        sys.exit()

    classifier = nltk.NaiveBayesClassifier.train(featuresets)


    return classifier


def dump_classifier(classifier, categories):
    file_path = "pickles/{}.pickle".format('_'.join(categories))

    if not os.path.exists('pickles'):
        os.makedirs('pickles')
    save_classifier = open(file_path, "wb")
    try:
        pickle.dump(classifier, save_classifier)
        print('Pickle with classifier was successfully dumped')
    except Exception:
        print('Something went wrong')
    save_classifier.close()


if __name__ == '__main__':
    """
    To run script you need to type 'python learn.py [category] [category]' 
    in your terminal
    """
    # якщо стоять флаги
    # -р подититись accuracy percent
    # -w подивитись найбільш частовживані слова
    if sys.argv[-1:][0] == "-p" or sys.argv[-1:][0] == "-w" :
        input_categories = sys.argv[1:-1]
        flag = sys.argv[-1:][0]
    else:
        input_categories = sys.argv[1:]
        flag = False

    if any(not c.isdigit() for c in input_categories):
        print('All arguments should be digits')
        sys.exit()
    elif len(input_categories) > 4 or len(input_categories) < 2:
        print('Wrong number of categories')
        sys.exit()

    train_data = get_data(input_categories)
    # ['full', 'operative', 'motive', 'introduction']
    validator = Validator('operative')
    clean_data = validator.validate_list(train_data)
    new_classifier = train(clean_data, flag)
    dump_classifier(new_classifier, input_categories)
