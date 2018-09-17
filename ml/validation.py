# -*- coding: utf-8 -*-
import re
import sys


class Validator():
    """Проводить валідацію тексту:
    видалення непотрібних слів
    виставлення знаків пунктуації
    приведення до коректноко машоночитабельного вигляду"""

    # словник регулярок для коректного виставлення пунктуації
    punctuation_replacement = {
        r'(\w+) а (\w)': r'\1, а \2',
        r'(\w+) аж (\w)': r'\1, аж \2',
        r'(\w+) але (\w)': r'\1, але \2',
        r'(\w+) або (\w)': r'\1, або \2',
        r'(\w+) ба (\w)': r'\1, ба \2',
        r'(\w+) бо (\w)': r'\1, бо \2',
        r'(\w+) та (\w)': r'\1, та \2',
        r'(\w+) то (\w)': r'\1, то \2',
        r'(\w+) то й (\w)': r'\1, то й \2',
        r'(\w+) тобто (\w)': r'\1, тобто \2',
        r'(\w+) проте (\w)': r'\1, проте \2',
        r'(\w+) чи (\w)': r'\1, чи \2',
        r'(\w+) ні (\w)': r'\1, ні \2',
        r'(\w+) хоч (\w)': r'\1, хоч \2',
        r'(\w+) як (\w)': r'\1, як \2',
        r'(\w+) що (\w)': r'\1, що \2'
    }

    needless_punctuation = {
        '"': '',   "'": '',   '’': '',   '`': '',   '(': '',   '“': '',
        ')': '',   '[': '',   ']': '',   '<': '',   '>': '',   '”': '',
        '-': '',   '–': '',   '‒': '',   '—―': '',  '«': '',   '»': '',
        '\\': '',  '/': '',   '&': '',   '@': '',   '^': '',   '_': '',
        '¶': '',   '№': '',   '#': '',   '%': '',   '~': '',   '‘': '',
        '*': '',   '=': '',   '+': '',   '$': ''
    }


    # регулярки для обрізання частини тексту
    # todo доробити для вступної і мотивувальної
    reg_introduction = re.compile(r"(ЗАСУДИ|ПОСТАНОВИ|ВИРІШИ|УХВАЛИ)\w+:?\n(.+)", re.MULTILINE | re.UNICODE | re.DOTALL)
    reg_motive = re.compile(r"(ЗАСУДИ|ПОСТАНОВИ|ВИРІШИ|УХВАЛИ)\w+:?\n(.+)", re.MULTILINE | re.UNICODE | re.DOTALL)
    reg_operative = re.compile(r"(ЗАСУДИ|ПОСТАНОВИ|ВИРІШИ|УХВАЛИ)\w+:?\n(.+)", re.MULTILINE | re.UNICODE | re.DOTALL)
    # вказує яку частину тексту потрібно вирізати
    part = 'full'

    def __init__(self, part='full'):
        self.part = part


    def validate_list(self, list):

        for i in range(len(list)):
            list[i]['doc_text'] = self.validate_text(list[i]['doc_text'])

        return list


    def validate_text(self, text):
        """
        валідує текст
        :param text: str
        :return: str
        """
        if not(self.part == 'full'):
            text = self.__cut_part(text)

        # виставлення всіх слів через один пробіл в тому числі замість \n
        text = re.sub('\s+', ' ', text)
        text = self.__set_punctuation(text)


        # виставлення через один пробіл
        text = re.sub(' +', ' ', text)
        print(text)


        return text



    def __set_punctuation(self, text):
        """
        виставляє пунктуацію в тексті, відповідно до правил Української мови
        видаляє непотрібну пунктуацію
        :param text: str
        :return: str
        """
        for s, r in self.punctuation_replacement.items():
            reg_e = re.compile(s, re.UNICODE)
            text = re.sub(reg_e, r, text)
        text = self.multiple_replace(self.needless_punctuation, text)

        return text





    def multiple_replace(self, dic, text):
        pattern = "|".join(map(re.escape, dic.keys()))
        return re.sub(pattern, lambda m: dic[m.group()], text)






    def __cut_part(self, text):
        """
        вирізає з тексту потрібну частину
        Вступну, мотивувальну або резолютивну
        :param text: str
        :return: str
        """
        if self.part == 'introduction':
            part_text = re.search(self.reg_introduction, text)
        elif self.part == 'motive':
            part_text = re.search(self.reg_motive, text)
        elif self.part == 'operative':
            part_text = re.search(self.reg_operative, text)
        elif self.part == 'full':
            return text
        else:
            print('Invalid name of part in cutPart')
            sys.exit()
        return part_text.group(2)
