#!/usr/bin/python
# -*- coding: utf-8 -*-
import json
import pymysql.cursors
import pprint
import pickle
import nltk
nltk.download('punkt')
nltk.download('averaged_perceptron_tagger')
#nltk.download('averaged_perceptron_tagger_ru')
from nltk.tokenize import word_tokenize


config = json.loads(open('../config.json', 'r').read())

connection = pymysql.connect(host=config['db']['host'],
							 user=config['db']['user'],
							 password=config['db']['pass'],
							 db=config['db']['dbname'],
							 charset=config['db']['charset'],
							 cursorclass=pymysql.cursors.DictCursor)
with connection.cursor() as cursor:
	sql = "SELECT `category`, `doc_text` FROM `ml_datasets`"
	cursor.execute(sql)
	data = cursor.fetchall()
	# for res in result:
	#     print(res['doc_text'])
	#     print('\n');

# list of your documents (each document is a string)
#data = ''

all_words = []


# J - adjective, R - adverb, V - verb
# you can experement with that
allowed_word_types = ["J", "R", "V"]

print('all words')
for title in data:
	# create an array of all words
	#print(title['doc_text'])
	words = word_tokenize(title['doc_text'])

	# add part of speech to each word
	pos = nltk.pos_tag(words)
	for w in pos:
		# w = ( "Word", 'RR')
		if w[1][0] in allowed_word_types:
			# all training words
			all_words.append(w[0].lower())


#save all descriptions with genre names
all_words = nltk.FreqDist(all_words)
#print(all_words)
word_features = [w for (w, c) in all_words.most_common(500)]
print('\n\n')
print(word_features)
print('\n\n')


def find_features(document):
	words = word_tokenize(document)
	features = {}
	for w in word_features:
		features[w] = (w in words)
	return features

#data = [('text':'text', 'group':'1'), ]


#preperaing featuresets for testing (it was specially for my case)
featuresets = [(find_features(title['doc_text']), title['category']) for title in data]
# print(featuresets)
#split into two parts for training and testing
training_set = featuresets[:len(featuresets) / 2]
testing_set = featuresets[len(featuresets) / 2:]


#print('start train')
classifier = nltk.NaiveBayesClassifier.train(training_set)

# check the accuracy
print("Original Naive Bayes Algo accuracy percent:", (nltk.classify.accuracy(classifier, testing_set)) * 100)


#in the end we need to save our classifier to use it later
save_classifier = open("pickles/originalnaivebayes.pickle", "wb")
pickle.dump(classifier, save_classifier)
save_classifier.close()

#classifier.classify()