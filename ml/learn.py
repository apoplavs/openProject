#!/usr/bin/python
# -*- coding: utf-8 -*-
import json
import re

import pymysql.cursors
import os
import pickle
import nltk
import sys

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
        sql = "SELECT `ml_datasets`.`category`, `src_documents`.`doc_text` FROM `ml_datasets` RIGHT JOIN `src_documents` ON `ml_datasets`.`doc_id`=`src_documents`.`doc_id` WHERE `ml_datasets`.`category` IN {}".format(
            str(tuple(categories))
        )
        cursor.execute(sql)
        data = cursor.fetchall()

    # list of your documents (each document is a string)
    return data



def train(clean_data):
    all_words = []
    sent_detector = nltk.data.load('tokenizers/punkt/english.pickle')

    for title in clean_data:
        # create an array of all words
        # print(title['doc_text'])
        #resolutive = re.search(reg_exp, title['doc_text'])
        #words = sent_detector.tokenize(title['doc_text'])
        words = word_tokenize(title['doc_text'])

        # add part of speech to each word
        pos = nltk.pos_tag(words)
        for w in pos:
            # w = ( "Word", 'RR')
            # all training sentences
            all_words.append(w[0].lower())

    # save all descriptions with genre names
    all_words = nltk.FreqDist(all_words)
    print(all_words.most_common(15000))
    sys.exit()
    word_features = [w for (w, c) in all_words.most_common(500)]

    def find_features(document):
        tokenized_words = sent_detector.tokenize(document)
        features = {}
        for w in word_features:
            features[w] = (w in tokenized_words)
        return features

    featuresets = [(find_features(title['doc_text']), title['category']) for
                   title in clean_data]
    # print(featuresets)

    classifier = nltk.NaiveBayesClassifier.train(featuresets)
    #print(classifier)
    #sys.exit()

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

    input_categories = sys.argv[1:]

    if any(not c.isdigit() for c in input_categories):
        print('All arguments should be digits')
        sys.exit()
    elif len(input_categories) > 40 or len(input_categories) < 2:
        print('Wrong number of categories')
        sys.exit()

    train_data = get_data(input_categories)
    validator = Validator('full')
    clean_data = validator.validate_list(train_data)
    new_classifier = train(clean_data)
    dump_classifier(new_classifier, input_categories)
