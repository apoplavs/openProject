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

    reg_needless = {
        r' \w+ого\W? ': ' ',       r' головуюч\w+:?': ' ',
        r' суд\w*:?': ' ',         r' украї\w+': '',
        r' облас\w+': '',          r' район\w*': '',
        r' держав\w+': '',         r' прокур\w+': '',
        r' ?київ\w*': '',          r' ?києв\w+': '',
        r' годин\w*': '',          r' хвилин\w*': '',
        r' ухвал\w+': '',          r' \w*банк': '',
        r' \w+кої\W? ': ' ',       r' шлюб\w*': '',
        r' \w+ькій\W? ': ' ',      r' тисяч\w*': '',
        r' \w+ький\W? ': ' ',      r' інспектор\w*': '',
        r' дніпро\w+ ': ' ',       r' товариств\w+': '',
        r' орган\w*': '',          r' кредит\w*': '',
        r' громад\w*': '',         r' алімент\w+': '',
        r' \w+дцять': '',
        r' \w+ська\W? ': ' ',      r' страхов\w+': '',
        r' служб\w*': '',          r' кодекс\w*': '',
        r' копійк\w+': ' ',     r' номер\w*': '',
        r' квартир\w+': ''
    }

    needless_words = {
        ' гумвс': '',         ' адвоката': '',
        ' удксу': '',         ' гудкс': '',
        ' першої': '',        ' колегія': '',
        ' захисника': '',     ' доларів': '',
        ' міліції': '',       ' права': '',
        ' кредит': '',        ' складі': '',
        ' договору': '',      ' вимоги': '',
        ' скарга': '',        ' саме': '',
        ' відкритому': '',    ' вбачається': '',
        ' митних': '',        ' умвс': '',
        ' єдрпоу': '',        ' секретаря': '',
        ' бвбв': '',          ' номер': '',
        ' січня': '',         ' лютого': '',
        ' березня': '',       ' квітня': '',
        ' травня': '',        ' червня': '',
        ' липня': '',         ' серпня': '',
        ' вересня': '',       ' жовтня': '',
        ' листопада': '',     ' грудня': '',
        ' прат': '',          ' гривень': '',
        ' одного': '',        ' двох': '',
        ' трьох': '',         ' чотирьох': '',
        ' пяти': '',          ' шести': '',
        ' семи': '',          ' восьми': '',
        " девяти": '',        ' десяти': '',
        ' двадцяти': '',      ' тридцяти': '',
        ' сорока': '',        ' один': '',
        ' два': '',           ' три': '',
        ' чотири': '',        ' пять': '',
        ' шість': '',         ' сім': '',
        ' вісім': '',         ' девять': '',
        ' десять': '',        ' одинадцять': '',
        ' двадцять': '',      ' тридцять': '',
        ' сорок': '',         ' сто ': ' ',
        ' двісті': '',        ' триста': '',
        ' чотириста': '',     ' пятсот': '',
        ' місяць': '',        ' рогу': '',
        ' будинок': '',       ' митниці': '',
        ' підпис': '',        ' модуль': '',
        ' гривні': '',        ' місяці': '',
        ' день': '',          ' дні': '',
        ' коп.': '',          ' год.': '',
        ' грн.': '',          ' вул.': '',
        ' каб.': '',          ' хв.': '',
        ' буд.': '',          ' спец.': '',
        ' мод.': '',          ' удптсу': '',
        ' тін.': '',          ' р.': '',
        ' sмs': '',           ' ноп.': '',
        ' дітей': '',         ' серії': '',
        ' року': ''
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
        # видалення цифр
        text = re.sub(r"\d+", "", text)
        # видалення службових слів(ОСОБА_1 ІНФОРМАЦІЯ_1)
        reg_e = re.compile(r"ОСОБ\w+,?|ІНФОРМА\w+,?|АДРЕС\w+,?|НОМЕ\w+,?", re.UNICODE)
        text = re.sub(reg_e, "", text)
        # приведення до нижнього регістру
        text = text.lower()

        # видаляємо непотрібні слова
        text = self.__del_needless_words(text)

        # виставляємо коректно пунктуацію
        text = self.__set_punctuation(text)

        # повторно 2 рази видаляємо непотрібні слова (так потрібно)
        text = self.__del_needless_words(text)
        text = self.__del_needless_words(text)

        # видаляємо короткі слова і сполучники (теж 2 рази)
        reg_e = re.compile(r" .{1,3} ", re.UNICODE)
        text = re.sub(reg_e, " ", text)
        text = re.sub(reg_e, " ", text)
        # обрізаємо пробіли з початку і кінця речення
        text = text.strip()


        # виставлення через один пробіл
        text = re.sub(' +', ' ', text)
        # print(text + "\n")


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
        text = re.sub(r"(\w)\.(\w)", r"\1. \1", text)
        text = re.sub(r"(\.,|,\.|\.\.)", ".", text)

        # всі знаки пунктуації,  між якими >= 10 символів замінити крапкою
        text = re.sub(r"[,|;|:]+", r".", text)
        # якщо залишились знаки пунктуації, після видалення чисел
        text = re.sub(r"\W+", " ", text)

        return text



    def __del_needless_words(self, text):
        """
        видаляє всі зайві слова
        :param text: str
        :return: str
        """
        # видалення непотрібних слів по словнику reg_exp
        for s, r in self.reg_needless.items():
            reg_e = re.compile(s, re.UNICODE | re.IGNORECASE)
            text = re.sub(reg_e, r, text)

        # видалення непотрібних частовживаних слів
        text = self.multiple_replace(self.needless_words, text)

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
