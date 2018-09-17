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

common_words = {
    u'суду': False,
    u'року': False,
    u'області': False,
    u'справі': False,
    u'розгляду': False,
    u'районного': False,
    u'протягом': False,
    u'бути': False,
    u'ухвала': False,
    u'може': False,
    u'днів': False,
    u'апеляційного': False,
    u'україни': False,
    u'рішення': False,
    u'скаргу': False,
    u'апеляційну': False,
    u'провадження': False,
    u'проголошення': False,
    u'залишити': True,
    u'оскарженню': False,
    u'законної': False,
    u'сили': False,
    u'стягнення': True,
    u'підлягає': False,
    u'призначити': True,
    u'оскаржена': True,
    u'строк': False,
    u'задовольнити': True,
    u'скарги': False,
    u'набирає': False,
    u'ухвалу': False,
    u'апеляційної': False,
    u'засідання': True,
    u'правопорушення': True,
    u'справу': False,
    u'ухвали': False,
    u'через': False,
    u'судді': False,
    u'судового': False,
    u'товариства': False,
    u'купап': False,
    u'порядку': True,
    u'позовом': True,
    u'моменту': True,
    u'києва': False,
    u'копії': False,
    u'акціонерного': False,
    u'справи': False,
    u'постанова': False,
    u'його': False,
    u'участь': True,
    u'змін': True,
    u'судове': False,
    u'публічного': False,
    u'отримання': False,
    u'скарга': True,
    u'розмірі': True,
    u'приміщенні': True,
    u'постанову': False,
    u'передбаченого': True,
    u'заяву': True,
    u'апеляційна': False,
    u'банк': False,
    u'адміністративного': True,
    u'після': True,
    u'стягнути': True,
    u'міськрайонного': False,
    u'особи': True,
    u'кримінальних': True,
    u'відкрити': True,
    u'районний': False,
    u'користь': True,
    u'копію': False,
    u'інстанції': False,
    u'визнати': True,
    u'справ': False,
    u'подання': True,
    u'адміністративне': False,
    u'управління': False,
    u'цивільних': False,
    u'спеціалізованого': False,
    u'вищого': False,
    u'шляхом': False,
    u'направити': False,
    u'касаційному': False,
    u'відповідальності': True,
    u'задоволення': True,
    u'адміністративної': False,
    u'апеляційне': False,
    u'викликати': True,
    u'заборгованості': False,
    u'строку': True,
    u'подачі': True,
    u'визнання': True,
    u'грудня': False,
    u'повернути': True,
    u'вчиненні': True,
    u'заяви': True,
    u'держави': False,
    u'беруть': True,
    u'осіб': True,
    u'двадцяти': True,
    u'десяти': True,
    u'скаргою': True,
    u'скасувати': True,
    u'відносно': True,
    u'якщо': False,
    u'разі': False,
    u'засіданні': True,
    u'судовий': False,
    u'постанови': True,
    u'апеляційною': True,
    u'штрафу': True,
    u'надати': True,
    u'відмовити': True,
    u'лютого': False,
    u'право': True,
    u'щодо': True,
    u'району': False,
    u'вигляді': True,
    u'заявою': True,
    u'виконання': True,
    u'судовому': True,
    u'винесення': True,
    u'подається': True,
    u'клопотання': True,
    u'покарання': True,
    u'гривень': False,
    u'виді': True,
    u'квітня': False,
    u'винним': True,
    u'першої': True,
    u'березня': False,
    u'дніпропетровської': False,
    u'який': True,
    u'державної': False,
    u'апеляційному': True,
    u'власності': True,
    u'адресою': False,
    u'позов': True,
    u'приватбанк': False,
    u'вересня': False,
    u'ради': False,
    u'надіслати': False,
    u'сумі': False,
    u'збір': False,
    u'якою': True,
    u'міського': False,
    u'представника': True,
    u'січня': False,
    u'відхилити': True,
    u'листопада': False,
    u'повідомити': True,
    u'набрання': True,
    u'було': True,
    u'прокурора': False,
    u'роз': False,
    u'подана': True,
    u'судом': True,
    u'остаточною': True,
    u'притягнення': True,
    u'права': True,
    u'оскарження': True,
    u'миколаївської': False,
    u'позову': True,
    u'відділу': False,
    u'червня': False,
    u'слідчого': False,
    u'служби': False,
    u'хмельницької': False,
    u'задоволенні': True,
    u'частині': True,
    u'закрити': True,
    u'заперечення': True,
    u'яснити': True,
    u'жовтня': False,
    u'юстиції': False,
    u'цивільній': True,
    u'закінчити': True,
    u'розірвання': True,
    u'позовну': True,
    u'подати': True,
    u'відповідача': True,
    u'розгляд': True,
    u'травня': False,
    u'серпня': False,
    u'липня': False,
    u'позбавлення': True,
    u'вимог': True,
    u'особа': True,
    u'шлюбу': True,
    u'кримінального': True,
    u'нього': True,
    u'договором': True,
    u'сторони': True,
    u'участі': True,
    u'підставі': True,
    u'вінницької': False,
    u'негайно': True,
    u'зобов': False,
    u'позивача': True,
    u'збору': True,
    u'даній': True,
    u'комерційний': True,
    u'встановити': True,
    u'проведення': True,
    u'інтересах': True,
    u'суді': False,
    u'чернівецької': False,
    u'особам': True,
    u'усунення': True,
    u'особі': True,
    u'відповідальністю': True,
    u'накласти': True,
    u'закінчення': True,
    u'були': True,
    u'частково': True,
    u'обмеженою': True,
    u'хвилин': False,
    u'полтавської': False,
    u'безпосередньо': True,
    u'дніпропетровська': False,
    u'волинської': False,
    u'питання': True,
    u'номер': False,
    u'язку': False,
    u'місяців': False,
    u'сумської': False,
    u'постановлено': True,
    u'докази': True,
    u'тернопільської': False,
    u'вирок': True,
    u'оскаржує': True,
    u'відповідачу': True,
    u'кредитним': False,
    u'місце': True,
    u'державного': False,
    u'руху': True,
    u'згідно': True,
    u'міста': False,
    u'позовної': True,
    u'поновити': True,
    u'брали': True,
    u'виконавчої': True,
    u'обвинуваченого': True,
    u'договору': True,
    u'неподаною': True,
    u'одеської': False,
    u'можуть': True,
    u'міської': False,
    u'строком': True,
    u'волі': True,
    u'трьох': True,
    u'йому': True,
    u'недоліків': True,
    u'подано': True,
    u'документів': True,
    u'виконавчого': True,
    u'буде': True,
    u'міськрайонний': False,
    u'підприємства': False,
    u'суми': False,
    u'чернігівської': False,
    u'відновити': True,
    u'заперечень': True,
    u'запорізької': True,
    u'вирішення': True,
    u'годину': False,
    u'касаційної': False,
    u'відповідно': True,
    u'позивачу': True,
    u'дохід': False,
    u'наказу': True,
    u'третя': True,
    u'шевченківського': False,
    u'сільської': False,
    u'оскаржене': True,
    u'зміни': True,
    u'присутні': True,
    u'проти': True,
    u'підготовчі': True,
    u'реєстрації': True,
    u'шкоди': False,
    u'поновлення': True,
    u'умвс': False,
    u'вартою': True,
    u'сторонам': True,
    u'доходів': True,
    u'кримінальному': False,
    u'поліції': False,
    u'складу': False,
    u'заочне': False,
    u'встановлення': True,
    u'аліментів': True,
    u'відкритому': True,
    u'доручити': True,
    u'провадженні': True,
    u'майна': True,
    u'цивільну': True,
    u'видачу': True,
    u'відшкодування': True,
    u'херсонської': False,
    u'годин': False,
    u'матеріали': False,
    u'прокуратури': False,
    u'годині': False,
    u'експертизи': False,
    u'підготовку': True,
    u'відкриття': True,
    u'захисника': True,
    u'засудженого': True,
    u'донецької': False,
    u'цивільного': True,
    u'письмові': True,
    u'звільнити': True,
    u'шлюб': True,
    u'саме': True,
    u'оголошення': True,
    u'районі': False,
    u'відсутністю': True,
    u'недійсним': True,
    u'цього': True,
    u'зареєстрований': True,
    u'компанія': False,
    u'доданих': True,
    u'якими': True,
    u'вчинення': True,
    u'вони': True,
    u'апеляційним': True,
    u'громадян': True,
    u'ідентифікаційний': False,
    u'залі': False,
    u'земельну': True,
    u'робіт': True,
    u'утримання': True,
    u'язати': False,
    u'хмельницького': False,
    u'оскаржено': True,
    u'вважати': True,
    u'прийняти': True,
    u'правил': True,
    u'відбування': True,
    u'коштів': True,
    u'повернення': True,
    u'вимоги': True,
    u'частини': True,
    u'луцького': False,
    u'запропонувати': True,
    u'площею': False,
    u'громадських': True,
    u'призначення': True,
    u'розірвати': True,
    u'накладення': True,
    u'скасування': True,
    u'визнано': True,
    u'київської': False,
    u'житомирської': False,
    u'будинку': False,
    u'даної': True,
    u'кодексу': False,
    u'мінімумів': True,
    u'якої': True,
    u'дніпровського': False,
    u'одеси': False,
    u'миколаєва': False,
    u'обгрунтовуються': True,
    u'учасників': True,
    u'зупинити': True,
    u'передати': True,
    u'звернення': True,
    u'пред': False,
    u'копійок': False,
    u'користування': True,
    u'попереднього': True,
    u'позовною': True,
    u'апелянту': True,
    u'актів': True,
    u'денний': True,
    u'передбачених': True,
    u'якого': True,
    u'солом': True,
    u'запис': True,
    u'комунального': True,
    u'суті': True,
    u'становить': True,
    u'особою': True,
    u'застосувати': True,
    u'стану': True,
    u'приватного': False,
    u'головного': True,
    u'проживання': False,
    u'керування': True,
    u'років': False,
    u'майно': False,
    u'ділянку': True,
    u'залишення': True,
    u'фонду': False,
    u'посилання': True,
    u'змінити': True,
    u'розгляді': True,
    u'кпап': False,
    u'роки': False,
    u'остаточна': True,
    u'діях': False,
    u'представником': False,
    u'дельта': False,
    u'адміністративні': False,
    u'судових': False,
    u'відкритті': True,
    u'заборгованість': False,
    u'приводу': True,
    u'подала': True,
    u'боргу': False,
    u'банку': False,
    u'луганської': False,
    u'факту': True,
    u'харківської': False,
    u'скоєнні': True,
    u'дитини': False,
    u'факт': False,
    u'рахунок': False,
    u'інспекції': False,
    u'невиконання': True,
    u'заявнику': True,
    u'один': False,
    u'подані': True,
    u'актовий': False,
    u'перегляд': True,
    u'янського': False,
    u'моральної': True,
    u'відмову': True,
    u'вироком': True,
    u'початку': True,
    u'тернопільського': False,
    u'законом': False,
    u'постановлення': True,
    u'тримання': True,
    u'жителя': False,
    u'язані': False,
    u'києві': False,
    u'продовження': True,
    u'явлення': False,
    u'заочного': False,
    u'апеляційний': False,
    u'злочину': True,
    u'новий': False,
    u'державний': False,
    u'начальника': False,
    u'процесу': True,
    u'проте': False,
    u'притягнуто': True,
    u'задоволити': True,
    u'витрати': True,
    u'чернівці': False,
    u'копіями': False,
    u'забезпечення': False,
    u'неоподатковуваних': False,
    u'провадженню': False,
    u'прав': False,
    u'черкаської': False,
    u'вручення': True,
    u'підозрюваного': True,
    u'часу': False,
    u'арешту': False,
    u'виконавця': True,
    u'адміністрації': True,
    u'підготовче': True,
    u'язання': False,
    u'старшого': True,
    u'заміну': True,
    u'повернута': True,
    u'гунп': False,
    u'засобами': True,
    u'смерті': False,
    u'ділянки': False,
    u'жовтневого': False,
    u'захід': True,
    u'засідань': True,
    u'транспортними': True,
    u'протязі': True,
    u'примирення': True,
    u'сторін': True,
    u'справах': True,
    u'закінченням': True,
    u'винною': True,
    u'спадкування': True,
    u'справа': False
}


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


def valid_world(world):
    if len(world) < 4:
        return False
    if world in common_words:
        return common_words[world]
    return True


def train(clean_data):
    all_words = []
    sent_detector = nltk.data.load('tokenizers/punkt/english.pickle')
    reg_exp = re.compile(r"(ЗАСУДИ|ПОСТАНОВИ|ВИРІШИ|УХВАЛИ)\w+:?\n(.+)", re.MULTILINE | re.UNICODE | re.DOTALL)

    for title in clean_data:
        # create an array of all words
        # print(title['doc_text'])
        #resolutive = re.search(reg_exp, title['doc_text'])
        #words = sent_detector.tokenize(resolutive.group(2))
        words = word_tokenize(title['doc_text'])

        # add part of speech to each word
        pos = nltk.pos_tag(words)
        for w in pos:
            # w = ( "Word", 'RR')
            # all training words
            if valid_world(w[0]) == True:
                all_words.append(w[0].lower())

    # save all descriptions with genre names
    all_words = nltk.FreqDist(all_words)
    print(all_words.most_common(500))
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
    validator = Validator('operative')
    clean_data = validator.validate_list(train_data)
    sys.exit()

    for da in clean_data:
        print(da)
    sys.exit()
    new_classifier = train(clean_data)
    dump_classifier(new_classifier, input_categories)
