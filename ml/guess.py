import os
import pickle

from learn import train_for_guess
from config import PICKLES_PATH
from nltk.tokenize import regexp
def get_classifier(pickle_name):
    classifier_pickle = pickle.load(open(f"{PICKLES_PATH}/{pickle_name}.pickle", "rb"))
    return classifier_pickle


def guess_category(categories, text):
    word_tokenizer = regexp.WhitespaceTokenizer()

    classifier_name = '_'.join(categories)
    if os.path.exists(f"{PICKLES_PATH}/{classifier_name}.pickle"):
        classifier = get_classifier(classifier_name)
        words = set([word.lower() for word in
                          word_tokenizer.tokenize(text)])


        res = classifier.classify(tokenized_text)
        return res
    else:
        train_for_guess(categories)
        return guess_category(categories, text)