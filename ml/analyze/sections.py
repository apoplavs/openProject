from judge import Judge
from classifier import guess_category
from config import *
from db import (
    read_from_db,
    write_to_db
)


class Section:
    data_dict = {}

    def __init__(self, judge: Judge, justice_kind, judge_results_table, anticipated_category=None):
        self.judge = judge
        self.justice_kind = justice_kind
        self.anticipated_category = anticipated_category
        self.judge_results_table = judge_results_table

    def _get_all_applications(self):
        """
        All applications examined by judge

        :return:
        """

        sql_query = (f"SELECT  DISTINCT cause_num FROM reg{self.judge.region} "
                     f"WHERE judge={self.judge.id} "
                     f"AND justice_kind={self.justice_kind}")

        applications = read_from_db(sql_query, EDRSR)
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

        sql_query = (f"SELECT DISTINCT cause_num FROM reg{self.judge.region} "
                     f"WHERE court_code={self.judge.court_code} "
                     f"AND cause_num IN ({all_applications})")

        appeals = read_from_db(sql_query, EDRSR)
        return appeals

    def _get_appeal_documents(self, cause_num):
        """
        All documents related to the appeal
        :param cause_num
        :return:
        """
        sql_query = (f"SELECT * FROM reg{self.judge.region} "
                     f"WHERE court_code={self.judge.court_code} "
                     f"AND cause_num='{cause_num}' "
                     f"ORDER BY adjudication_date DESC")

        documents = read_from_db(sql_query, EDRSR)
        return documents

    def count(self):
        raise NotImplementedError

    def save(self):

        self.data_dict['judge'] = self.judge.id
        keys = ','.join(["`" + k + "`" for k in self.data_dict])

        values = list(self.data_dict.values())

        sql_query = (f"REPLACE INTO `{self.judge_results_table}` ({keys}) "
                     f"VALUES ({','.join('%s' for i in range(len(values)))})")

        write_to_db(sql_query, TOECYD,  values)


class Civil(Section):

    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="1",
            anticipated_category=28,
            judge_results_table='judges_civil_statistic'
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)

        civil_in_appeal = self._get_all_appeals(all_applications)
        self.data_dict['was_appeal'] = len(civil_in_appeal)
        self.data_dict['approved_by_appeal'] = 0
        self.data_dict['not_approved_by_appeal'] = 0

        if len(civil_in_appeal) < 50:
            return

        for appeal in civil_in_appeal:
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.anticipated_category
                )
                if category == 28:
                    self.data_dict['approved_by_appeal'] += 1
                elif category == 29:
                    self.data_dict['not_approved_by_appeal'] += 1


class Criminal(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="2",
            anticipated_category=31,
            judge_results_table='judges_criminal_statistic'
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)

        civil_in_appeal = self._get_all_appeals(all_applications)
        self.data_dict['was_appeal'] = len(civil_in_appeal)
        self.data_dict['approved_by_appeal'] = 0
        self.data_dict['not_approved_by_appeal'] = 0

        if len(civil_in_appeal) < 50:
            return

        for appeal in civil_in_appeal:
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.anticipated_category
                )
                if category == 31:
                    self.data_dict['approved_by_appeal'] += 1
                elif category == 32:
                    self.data_dict['not_approved_by_appeal'] += 1


class Commercial(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="3",
            judge_results_table='judges_commercial_statistic'
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)


class Admin(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="4",
            judge_results_table='judges_admin_statistic'
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
            anticipated_category=25
        )

    def count(self):
        all_applications = self._get_all_applications()
        self.data_dict['amount'] = len(all_applications)

        civil_in_appeal = self._get_all_appeals(all_applications)
        self.data_dict['was_appeal'] = len(civil_in_appeal)
        self.data_dict['approved_by_appeal'] = 0
        self.data_dict['not_approved_by_appeal'] = 0

        if len(civil_in_appeal) < 50:
            return

        for appeal in civil_in_appeal:
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.anticipated_category
                )
                if category == 25:
                    self.data_dict['approved_by_appeal'] += 1
                elif category == 26:
                    self.data_dict['not_approved_by_appeal'] += 1



