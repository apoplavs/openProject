#!/usr/bin/python
# -*- coding: utf-8 -*-

import pickle
import nltk
import sys

from lib.validation import Validator
from lib.config import *


alias_category = {
    1: 1, 22: 6,
    2: 1, 23: 6,
    3: 1, 24: 6,
    4: 1, 25: 7,
    5: 2, 26: 7,
    6: 2, 27: 7,
    7: 2, 28: 8,
    8: 3, 29: 8,
    9: 3, 30: 8,
    10: 3, 31: 9,
    11: 3, 32: 9,
    12: 3, 33: 9,
    13: 4,
    14: 4,
    15: 4,
    16: 4,
    17: 5,
    18: 5,
    19: 5,
    20: 5,
    21: 5,
}

map_categories = {
    # адмінправопорушення типи кінцевих рішень
    2: {'pickle1': '5_6.pickle',
        'pickle2': '5_6_7.pickle',
        'part_text': 'operative',
        'categories': [5, 6],
        'other': 7},
    # цивільне своєчасність
    3: {'pickle1': '8_9_10_11.pickle',
        'pickle2': '8_9_10_11_12.pickle',
        'part_text': 'operative',
        'categories': [8, 9, 10, 11],
        'other': 12},
    # цивільне типи кінцевих рішень
    4: {'pickle1': '13_14_15.pickle',
        'pickle2': None,
        'part_text': 'operative',
        'categories': [13, 14, 15],
        'other': None},
    # кримінальне своєчасність
    5: {'pickle1': '17_18_19_20.pickle',
        'pickle2': '17_18_19_20_21.pickle',
        'part_text': 'operative',
        'categories': [17, 18, 19, 20],
        'other': 21},
    # кримінальне типи кінцевих рішень
    6: {'pickle1': '22_23.pickle',
        'pickle2': None,
        'part_text': 'operative',
        'categories': [22, 23],
        'other': None},
    # адмінправопорушення апеляція
    7: {'pickle1': '25_26.pickle',
        'pickle2': '25_26_27.pickle',
        'part_text': 'operative',
        'categories': [25, 26],
        'other': 27},
    # цивільне апеляція
    8: {'pickle1': '28_29.pickle',
        'pickle2': '28_29_30.pickle',
        'part_text': 'operative',
        'categories': [28, 29],
        'other': 30},
    # кримінальне апеляція
    9: {'pickle1': '31_32.pickle',
        'pickle2': '31_32_33.pickle',
        'part_text': 'operative',
        'categories': [31, 32],
        'other': 33}

}


def get_classifier(file):
    if file is None:
        return None
    try:
        open_file = open(f'{PICKLES_PATH}/' + file, "rb")
    except Exception:
        print('Exception: file "' + f'{PICKLES_PATH}/' + file + '" not found')
        sys.exit(2)
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


def get_probably(text, classifier, categories):
    # firstly we find featureset for desired text
    feats = find_features(text)

    res = classifier.prob_classify(feats)

    prob_category = {}
    for cat in categories:
        prob_category.update({cat: (res.prob(cat) * 100)})

    return prob_category


def find_category(prob1, prob2, other):
    category = 0
    max = 0
    average = {}

    for k, v in prob1.items():
        if max < v:
            max = v
    # якщо всі категорії < 75% значить це other
    if max < 75:
        return other

    max = 0
    for k, v in prob2.items():
        if k == other and v > 40:
            return other
        if max < v:
            max = v
        # отримуємо середній відсоток з 2 prob для поточної категорії
        if not (k == other):
            average.update({k: (prob1[k] + v) / 2})

    if max < 70:
        return other
    max = 0

    # визначаємо в якої категорії вищий середній відсоток
    for k, v in average.items():
        if max < v:
            max = v
            category = k
    return category


def guess_category(text, anticipated_category):
    """
    Визначити категорію документу
    :param text: текст документу
    :param anticipated_category: одна з можливих категорій, до якої він може відноситися
    :return: int  категорія документу
    """
    if len(text) < 10 or (anticipated_category not in alias_category):
        return 0
    # отримуємо налаштування для даної категорії
    prop = map_categories[alias_category[anticipated_category]]
    # отримуємо класифікатори з pickle
    classifier1 = get_classifier(prop['pickle1'])
    classifier2 = get_classifier(prop['pickle2'])

    # ['full', 'operative', 'motive', 'introduction']
    validator = Validator(prop['part_text'])
    clean_text = validator.validate_text(text)

    # якщо немає other просто визначаємо категорію
    if classifier2 is None:
        return classifier1.classify(find_features(clean_text))

    # отримуємо probably для першого класифікатора
    prob1 = get_probably(clean_text, classifier1, prop['categories'])
    # додаємо категрію other в перелік ймовірних
    all_categories = prop['categories']
    all_categories.append(prop['other'])
    # отримуємо probably для другого класифікатора
    prob2 = get_probably(clean_text, classifier2, all_categories)

    return find_category(prob1, prob2, prop['other'])
