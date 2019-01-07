#!/usr/bin/python
# -*- coding: utf-8 -*-

import pickle
import re

import nltk
import sys

from lib.validation import Validator
from lib.config import *


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


alias_category = {
    5: 2, 22: 6,
    6: 2, 23: 6,
    7: 2, 24: 6,
    8: 3, 25: 7,
    9: 3, 26: 7,
    10: 3, 27: 7,
    11: 3, 28: 8,
    12: 3, 29: 8,
    13: 4, 30: 8,
    14: 4, 31: 9,
    15: 4, 32: 9,
    16: 4, 33: 9,
    17: 5,
    18: 5,
    19: 5,
    20: 5,
    21: 5,
}

map_categories = {
    # адмінправопорушення типи кінцевих рішень
    2: {'pickle1': get_classifier('5_6.pickle'),
        'pickle2': get_classifier('5_6_7.pickle'),
        'part_text': 'operative',
        'categories': [5, 6],
        'other': 7,
        'regexp': None},
    # цивільне своєчасність
    3: {'pickle1': get_classifier('8_9_10_11.pickle'),
        'pickle2': get_classifier('8_9_10_11_12.pickle'),
        'part_text': 'operative',
        'categories': [8, 9, 10, 11],
        'other': 12,
        'regexp':  {
            8: [re.compile(r".*призначити підготовче судове засідання.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*призначити.{1,20}судовий розгляд.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*відкрити.{1,20}провадження.+", re.M | re.U | re.S | re.I)
                 ],
            9: [re.compile(r".*провадження (по|у).+справ\w зупинити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*зупинити провадження.{2,20}справі.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*провадження.{2,10}справі.+зупинити.+", re.M | re.U | re.S | re.I)
                 ],
            10: [re.compile(r".*відновити провадження.{2,20}справі.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*провадження.{2,10}справі.+відновити.+", re.M | re.U | re.S | re.I)
                 ],
            11: [re.compile(r".*позов.+залишити без розгляду.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*залишити позов.+без розгляду.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*заяв.+залишити без розгляду.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*залишити заяв.+без розгляду.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*залишити без розгляду.{1,20}заяв.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*провадження.+закрити.+[ув] зв’?язку і?з.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*визнати.+мирову угоду.+", re.M | re.U | re.S | re.I)
                 ]
            }
        },
    # цивільне типи кінцевих рішень
    4: {'pickle1': get_classifier('13_14_15.pickle'),
        'pickle2': None,
        'part_text': 'operative',
        'categories': [13, 14, 15],
        'other': None,
        'regexp': {
            13: [re.compile(r".*задовольнити позов\s[^ч][^а][^с][^т][^к][^о][^в].+", re.M | re.U | re.S | re.I),
                 re.compile(r".*позов.+задовольнити\s[^ч][^а][^с][^т][^к][^о][^в].+", re.M | re.U | re.S | re.I),
                 re.compile(r".*позов.+задоволити\s[^ч][^а][^с][^т][^к][^о][^в].+", re.M | re.U | re.S | re.I),
                 re.compile(r".*позов.+задовільнити\s[^ч][^а][^с][^т][^к][^о][^в].+", re.M | re.U | re.S | re.I),
                 re.compile(r".*заяв.+ задовольнити\s[^ч][^а][^с][^т][^к][^о][^в].+", re.M | re.U | re.S | re.I),
                 re.compile(r".*заяв.+ задоволити\s[^ч][^а][^с][^т][^к][^о][^в].+", re.M | re.U | re.S | re.I),
                 re.compile(r".*заяв.+ задовільнити\s[^ч][^а][^с][^т][^к][^о][^в].+", re.M | re.U | re.S | re.I),
                 re.compile(r".*розірвати шлюб.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*шлюб.+розірвати.+", re.M | re.U | re.S | re.I)
                 ],
            14: [re.compile(r".*задовольнити частково.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*задоволити частково.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*задовільнити частково.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*решт.{2,5}позовних вимог.{0,3}відмовити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*інших позовних вимог.{0,3}відмовити.+", re.M | re.U | re.S | re.I)
                 ],
            15: [re.compile(r".*задоволенні.+позов.+відмовити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*позов.+відмовити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*відмовити.+задоволенні.+позов.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*відмовити.+повному обсязі.+", re.M | re.U | re.S | re.I)
                 ]
            }
        },
    # кримінальне своєчасність
    5: {'pickle1': get_classifier('17_18_19_20.pickle'),
        'pickle2': get_classifier('17_18_19_20_21.pickle'),
        'part_text': 'operative',
        'categories': [17, 18, 19, 20],
        'other': 21,
        'regexp': {
            17: [re.compile(r".*призначити підготовче судове засідання.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*призначити.{1,20}судовий розгляд.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*відкрити.{1,20}провадження.+", re.M | re.U | re.S | re.I)
                 ],
            18: [re.compile(r".*провадження (по|у).+справ\w зупинити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*(кримінальне|судове) провадження.+зупинити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*зупинити (кримінальне|судове) провадження.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*провадження [ув] кримінальному провадженні зупинити.+", re.M | re.U | re.S | re.I)
                 ],
            19: [re.compile(r".*поновити.+(судове|кримінальне).+провадження.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*відновити.+(судове|кримінальне).+провадження.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*поновити.+розгляд кримінального провадження.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*відновити.+розгляд кримінального провадження.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*(судове|кримінальне).+провадження.+поновити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*(судове|кримінальне).+провадження.+відновити.+", re.M | re.U | re.S | re.I)
                 ],
            20: [re.compile(r".*провадження.+закрити.+[ув] зв’?язку і?з.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*застосувати.+примусові заходи.{5,20}характеру.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*кримінальне провадження.+закрити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*закрити кримінальне провадження.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*звільнити від.+відповідальності.+провадження.+закрити.+", re.M | re.U | re.S | re.I),
                 ]
            }
        },
    # кримінальне типи кінцевих рішень
    6: {'pickle1': get_classifier('22_23.pickle'),
        'pickle2': None,
        'part_text': 'operative',
        'categories': [22, 23],
        'other': None,
        'regexp': {
            22: [re.compile(r".*визнати.+винуват(им|ою).+призначити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*визнати.+винн(им|ою).+призначити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*затвердити.+угоду про визнання винуватості.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*угоду про визнання винуватості.+затвердити.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*затвердити.+угоду про примирення.+", re.M | re.U | re.S | re.I),
                 re.compile(r".*угоду про примирення.+затвердити.+", re.M | re.U | re.S | re.I)
                 ],
            23: [re.compile(r".*визнати.{1,50}не ?винуват(им|ою) .+", re.M | re.U | re.S | re.I),
                 re.compile(r".*визнати.{1,50}не ?винн(им|ою) .+", re.M | re.U | re.S | re.I),
                 re.compile(r".*виправдати .+ [ув] зв’?язку.+", re.M | re.U | re.S | re.I),
                 ]
            }
        },
    # адмінправопорушення апеляція
    7: {'pickle1': get_classifier('25_26.pickle'),
        'pickle2': get_classifier('25_26_27.pickle'),
        'part_text': 'operative',
        'categories': [25, 26],
        'other': 27,
        'regexp': None},
    # цивільне апеляція
    8: {'pickle1': get_classifier('28_29.pickle'),
        'pickle2': get_classifier('28_29_30.pickle'),
        'part_text': 'operative',
        'categories': [28, 29],
        'other': 30,
        'regexp': None},
    # кримінальне апеляція
    9: {'pickle1': get_classifier('31_32.pickle'),
        'pickle2': get_classifier('31_32_33.pickle'),
        'part_text': 'operative',
        'categories': [31, 32],
        'other': 33,
        'regexp': None}

}

def guess_by_regexp(regexp, text):

    # якщо регулярних виразів немає для цієї категорії
    if regexp == None :
        return 0

    # видалення знаків пунктуації щоб не заважали
    text = re.sub(r"[\.,\n] ?", " ", text)

    # проходження по регулярках, і пошук відповідностей
    for key, value in regexp.items() :
        for r in value :
            if re.match(r, text) is not None:
                return key
    return 0



sent_detector = nltk.data.load('tokenizers/punkt/english.pickle')


def find_features(document):
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

    # ['full', 'operative', 'motive', 'introduction']
    validator = Validator(prop['part_text'])

    # пробуємо обрізати потрібну частину тексту
    clean_text = validator.cut_part(text)
    if clean_text == None:
        return 0

    # пробуємо визначити категорію за регулярками
    category = guess_by_regexp(regexp=prop['regexp'], text=clean_text)

    if category != 0:
        return category

    # отримуємо класифікатори з pickle
    classifier1 = prop['pickle1']
    classifier2 = prop['pickle2']

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
