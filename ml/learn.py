#!/usr/bin/python
# -*- coding: utf-8 -*-
import json
import pymysql.cursors
import pprint
import pickle
import nltk
import sys

from nltk.tokenize import word_tokenize

config = json.loads(open('config.json', 'r').read())


def get_connection():
    connection = pymysql.connect(host=config['db_edrsr']['host'],
                                 user=config['db_edrsr']['user'],
                                 password=config['db_edrsr']['pass'],
                                 db=config['db_edrsr']['dbname'],
                                 charset=config['db_edrsr']['charset'],
                                 cursorclass=pymysql.cursors.DictCursor)

    return connection


def get_data(categories):
    connection = get_connection()

    with connection.cursor() as cursor:
        sql = "SELECT `category`, `doc_text` FROM `ml_datasets` WHERE category IN {}".format(
            str(tuple(categories))
        )
        cursor.execute(sql)
        data = cursor.fetchall()

    # list of your documents (each document is a string)
    return data


def train(clean_data):
    all_words = []
    # J - adjective, R - adverb, V - verb
    # you can experement with that
    allowed_word_types = ["J", "R", "V"]

    print('all words')
    for title in clean_data:
        # create an array of all words
        # print(title['doc_text'])
        words = word_tokenize(title['doc_text'])

        # add part of speech to each word
        pos = nltk.pos_tag(words)
        for w in pos:
            # w = ( "Word", 'RR')
            if w[1][0] in allowed_word_types:
                # all training words
                all_words.append(w[0].lower())

    # save all descriptions with genre names
    all_words = nltk.FreqDist(all_words)
    # print(all_words)
    word_features = [w for (w, c) in all_words.most_common(500)]

    def find_features(document):
        words = word_tokenize(document)
        features = {}
        for w in word_features:
            features[w] = (w in words)
        return features

    # data = [('text':'text', 'group':'1'), ]

    # preperaing featuresets for testing (it was specially for my case)
    featuresets = [(find_features(title['doc_text']), title['category']) for
                   title in clean_data]

    classifier = nltk.NaiveBayesClassifier.train(featuresets)

    return classifier


def dump_classifier(classifier, categories):
    file_path = "pickles/{}.pickle".format('_'.join(categories))

    save_classifier = open(file_path, "wb")
    pickle.dump(classifier, save_classifier)
    save_classifier.close()


if __name__ == '__main__':
    input_categories = sys.argv[1:]

    if any(not c.isdigit() for c in input_categories):
        print('All arguments should be digits')
        sys.exit()
    elif len(input_categories) > 4 or len(input_categories) < 2:
        print('Wrong number of categories')
        sys.exit()

    train_data = get_data(input_categories)
    new_classifier = train(train_data)
    # classifier.classify()
    dump_classifier(new_classifier, input_categories)
