#!/usr/bin/python
# -*- coding: utf-8 -*-
# coding=utf8
import json
import pymysql.cursors
import os
import pickle
import nltk
import sys
import re
from pprint import pprint

from nltk.tokenize import word_tokenize
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



def getQuery(category):
	if category == 25 or category == 26:
		query = "SELECT `doc_id`, `doc_text` FROM `src_documents` WHERE `justice_kind`=5 AND `instance_code`=2 AND `doc_id` NOT IN (SELECT `doc_id` FROM `ml_datasets` WHERE `category` IN (25, 26, 27))" # адмінправопорушення (апеляція)
	elif category == 28 or category == 29:
		query = "SELECT `doc_id`, `doc_text` FROM `src_documents` WHERE `justice_kind`=1 AND `judgment_code`=5 AND  `instance_code`=2 AND `doc_id` NOT IN (SELECT `doc_id` FROM `ml_datasets` WHERE category IN (28, 29, 30))" # цивільне (апеляція)
	elif category == 31 or category == 32:
		query = "SELECT `doc_id`, `doc_text` FROM `src_documents` WHERE `justice_kind`=2 AND `judgment_code`=5 AND `instance_code`=2 AND `doc_id` NOT IN (SELECT `doc_id` FROM `ml_datasets` WHERE `category` IN (31, 32, 33))" # кримінальне (апеляція)
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

def getClassifier():
	#load our classifier from pickle 
	open_file = open("pickles/28_29_30.pickle", "rb")
	#open_file = open("pickles/originalnaivebayes.pickle", "rb")
	classifier = pickle.load(open_file)
	open_file.close()
	return classifier

def find_features(document):
	words = word_tokenize(document)
	features = {}
	for w in words:
		features[w] = (w in words)
	return features	


def sentiment(text):
	#firstly we find featureset for desired text
	feats = find_features(text)
	classifier = getClassifier()
	res = classifier.prob_classify(feats)
	#print(res.prob(29))
	#sys.exit()
	#run our classifier
	#res = classifier.classify(feats)
	return res	



if __name__ == '__main__':
	"""
	To run script you need to type 'python add_category.py [category] [limit]' 
	in your terminal
	"""

	if not len(sys.argv) == 3:
		print('should by 2 arguments, where first is number of category and second is limit')
		sys.exit()
	category = sys.argv[1]
	limit = sys.argv[2]

	if any(not c.isdigit() for c in [category, limit]):
		print('All arguments should be digits')
		sys.exit()

 
	query = getQuery(int(category))
	documents = getDocuments(query, limit)
	for title in documents:
		reg_exp = re.compile(r"(ЗАСУДИ|ПОСТАНОВИ|ВИРІШИ|УХВАЛИ)\w+:?\n(.+)", re.MULTILINE | re.UNICODE | re.DOTALL)
		resolutive = re.search(reg_exp, title['doc_text'])

		res = sentiment(resolutive.group(2))
		print(str(title['doc_id'])+'    28 = '+str(res.prob(28))+'    29 = '+str(res.prob(29))+'    30 = '+str(res.prob(30)))

	#pprint(documents)
	sys.exit()

	train_data = get_data(input_categories)
	new_classifier = train(train_data)
	dump_classifier(new_classifier, input_categories)
