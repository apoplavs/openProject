from analyze.judge import Judge
from analyze.classifier import guess_category
from lib.config import *
from lib.db import DB


class Section:
    data_dict = {}

    def __init__(self, judge: Judge, justice_kind, judge_results_table, judgment_codes=None, anticipated_category=None):
        self.judge = judge
        self.justice_kind = justice_kind
        self.anticipated_category = anticipated_category
        self.judge_results_table = judge_results_table
        self.judgement_codes = judgment_codes
        self.data_dict = {}

    def _get_all_applications(self):
        """
        All applications examined by judge

        :return:
        """

        sql_query = (f"SELECT  DISTINCT cause_num FROM reg{self.judge.region} "
                     f"WHERE judge={self.judge.id} "
                     f"AND justice_kind={self.justice_kind}")
        edrsr = DB(db_name=EDRSR)
        applications = edrsr.read(sql_query)
        return applications

    def _get_all_appeals(self, all_applications):
        """
        All appeals of judge

        :param all_applications list
        :return:
        """

        # in order to avoid coding error add '' to each appeal cause_num
        all_applications = ', '.join(
            "'" + num['cause_num'] + "'" for num in all_applications
        )
        j_codes = ', '.join(
            str(code)  for code in self.judgement_codes
        )

        sql_query = (f"SELECT DISTINCT cause_num FROM reg{self.judge.region} "
                     f"WHERE court_code={self.judge.region + '90'} "
                     f"AND judgment_code IN ({j_codes}) "
                     f"AND cause_num IN ({all_applications})")
        edrsr = DB(db_name=EDRSR)
        appeals = edrsr.read(sql_query)
        return appeals

    def _get_appeal_documents(self, cause_num):
        """
        All documents related to the appeal
        :param cause_num
        :return:
        """
        j_codes = ', '.join(
            str(code)  for code in self.judgement_codes
        )

        sql_query = (f"SELECT * FROM reg{self.judge.region} "
                     f"WHERE court_code={self.judge.region + '90'} "
                     f"AND cause_num='{cause_num}' "
                     f"AND judgment_code IN ({j_codes}) "
                     f"ORDER BY adjudication_date DESC")
        edrsr = DB(db_name=EDRSR)
        documents = edrsr.read(sql_query)
        return documents

    def count(self):
        raise NotImplementedError

    def save(self):

        self.data_dict['judge'] = self.judge.id
        keys = ','.join(["`" + k + "`" for k in self.data_dict])

        values = list(self.data_dict.values())

        sql_query = (f"REPLACE INTO `{self.judge_results_table}` ({keys}) "
                     f"VALUES ({','.join('%s' for i in range(len(values)))})")
        toecyd = DB(db_name=TOECYD)
        toecyd.write(sql_query, values)


class Civil(Section):

    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="1",
            anticipated_category=28,
            judge_results_table='judges_civil_statistic',
            judgment_codes=[3, 5]
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)

        # якщо справ немає - повертаємось
        if self.data_dict['amount'] == 0 :
            return

        civil_in_appeal = self._get_all_appeals(all_applications)
        self.data_dict['was_appeal'] = len(civil_in_appeal)
        self.data_dict['approved_by_appeal'] = 0
        self.data_dict['not_approved_by_appeal'] = 0

        if len(civil_in_appeal) < 30:
            return

        for appeal in civil_in_appeal:
            # отримуємо всі документи апеляції по справі
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                # якщо апеляція винесла рішення - точно не вистояло, переходимо до наступної справи
                if document['judgment_code'] == 3 :
                    self.data_dict['not_approved_by_appeal'] += 1
                    break
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.anticipated_category
                )
                if category == 28:
                    self.data_dict['approved_by_appeal'] += 1
                    break
                elif category == 29:
                    self.data_dict['not_approved_by_appeal'] += 1
                    break


class Criminal(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="2",
            anticipated_category=31,
            judge_results_table='judges_criminal_statistic',
            judgment_codes=[5, 1]
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)

        # якщо справ немає - повертаємось
        if self.data_dict['amount'] == 0:
            return

        civil_in_appeal = self._get_all_appeals(all_applications)
        self.data_dict['was_appeal'] = len(civil_in_appeal)
        self.data_dict['approved_by_appeal'] = 0
        self.data_dict['not_approved_by_appeal'] = 0

        if len(civil_in_appeal) < 30:
            return

        for appeal in civil_in_appeal:
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                # якщо апеляція винесла вирок - точно не вистояло, переходимо до наступної справи
                if document['judgment_code'] == 1:
                    self.data_dict['not_approved_by_appeal'] += 1
                    break
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.anticipated_category
                )
                if category == 31:
                    self.data_dict['approved_by_appeal'] += 1
                    break
                elif category == 32:
                    self.data_dict['not_approved_by_appeal'] += 1
                    break


class Commercial(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="3",
            judge_results_table='judges_commercial_statistic',
            judgment_codes=[3, 5]
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)


class Admin(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="4",
            judge_results_table='judges_admin_statistic',
            judgment_codes=[3, 5]
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)


class AdminOffence(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="5",
            judge_results_table='judges_adminoffence_statistic',
            anticipated_category=25,
            judgment_codes=[5, 2]
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)

        # якщо справ немає - повертаємось
        if self.data_dict['amount'] == 0:
            return

        civil_in_appeal = self._get_all_appeals(all_applications)
        self.data_dict['was_appeal'] = len(civil_in_appeal)
        self.data_dict['approved_by_appeal'] = 0
        self.data_dict['not_approved_by_appeal'] = 0

        if len(civil_in_appeal) < 30:
            return

        for appeal in civil_in_appeal:
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                # якщо апеляція винесла ухвалу - точно вистояло, переходимо до наступної справи
                if document['judgment_code'] == 5:
                    self.data_dict['approved_by_appeal'] += 1
                    break
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.anticipated_category
                )
                if category == 25:
                    self.data_dict['approved_by_appeal'] += 1
                    break
                elif category == 26:
                    self.data_dict['not_approved_by_appeal'] += 1
                    break
