from analyze.judge import Judge
from analyze.classifier import guess_category
from lib.config import *
from lib.db import DB
from datetime import date, datetime


class Section:
    data_dict = {}

    def __init__(self, judge: Judge, justice_kind, judge_results_table,
                 judgment_codes=None, anticipated_category=None):

        self.start_time = datetime.now()

        self.judge = judge
        self.justice_kind = justice_kind
        self.anticipated_category = anticipated_category
        self.judge_results_table = judge_results_table
        self.judgement_codes = judgment_codes
        self.data_dict = {}
        self.numbers_data = {}

        # своєчасність
        self.data_dict['average_duration'] = 0
        self.data_dict['cases_on_time'] = 0
        self.data_dict['cases_not_on_time'] = 0


    def _get_application_documents(self, prepare_func=None):
        """
        All documents related to the applications
        :param cause_num
        :return:
        """
        j_codes = ', '.join(
            str(code) for code in self.judgement_codes
        )
        sql_query = (f"SELECT * FROM reg{self.judge.region} "
                     f"WHERE judge={self.judge.id} "
                     f"AND court_code={self.judge.court_code} "
                     f"AND justice_kind={self.justice_kind} "
                     f"AND judgment_code IN ({j_codes}) "
                     )
        edrsr = DB(db_name=EDRSR)
        documents = edrsr.read(sql_query)

        if prepare_func:
            return prepare_func(documents)
        return documents

    def _get_all_appeals(self, all_applications)  -> list:
        """
        All appeals of judge

        :param all_applications list
        :return:
        """

        # in order to avoid coding error add '' to each appeal cause_num
        all_applications = ', '.join(
            "'" + k + "'" for k, v in all_applications.items()
        )
        j_codes = ', '.join(
            str(code) for code in self.judgement_codes
        )

        sql_query = (f"SELECT DISTINCT cause_num FROM reg{self.judge.region} "
                     f"WHERE court_code={self.judge.region + '90'} "
                     f"AND judgment_code IN ({j_codes}) "
                     f"AND cause_num IN ({all_applications})")
        edrsr = DB(db_name=EDRSR)
        appeals = edrsr.read(sql_query)
        return appeals

    def _get_appeal_documents(self, cause_num) -> list:
        """
        All documents related to the appeal
        :param cause_num
        :return:
        """
        j_codes = ', '.join(
            str(code) for code in self.judgement_codes
        )

        sql_query = (f"SELECT * FROM reg{self.judge.region} "
                     f"WHERE court_code={self.judge.region + '90'} "
                     f"AND cause_num='{cause_num}' "
                     f"AND judgment_code IN ({j_codes}) "
                     f"ORDER BY adjudication_date DESC")
        edrsr = DB(db_name=EDRSR)
        documents = edrsr.read(sql_query)
        return documents

    def _get_autoasigned_cases(self, cause_nums, prepare_func=None):
        all_applications = ', '.join(
            "'" + num + "'" for num in cause_nums
        )
        sql_query = (f"SELECT * FROM auto_assigned_cases "
                     f"WHERE court = {self.judge.court_code} "
                     f"AND judge = {self.judge.id} "
                     f"AND number IN ({all_applications})"
                     )

        toecyd = DB(db_name=TOECYD)
        autoasigned_cases = toecyd.read(sql_query)

        if prepare_func:
            return prepare_func(autoasigned_cases)
        return autoasigned_cases

    def analyze_in_time(self):
        """рахування скільки часу суддя розглядає справи"""
        raise NotImplementedError


    def _count_days_on_time(self, date_dict, pause_days):

        if isinstance(date_dict['start_adj_date'], date):
            interval = (date_dict['end_adj_date'] -
                        date_dict['start_adj_date']
                        ).days - pause_days
            if interval < 0:
                interval *= -1
            elif interval == 0:
                interval = 1    

            # рахуємо середню тривалість розгляду справи
            self.data_dict['average_duration'] *= (self.data_dict['cases_on_time'] + self.data_dict['cases_not_on_time'])

            if interval <= self.numbers_data['interval']:
                self.data_dict['cases_on_time'] += 1
            else:
                self.data_dict['cases_not_on_time'] += 1

            if interval < 1:
                interval = 1
            # рахуємо середню тривалість розгляду справи
            self.data_dict['average_duration'] += interval
            self.data_dict['average_duration'] = self.data_dict['average_duration'] / (self.data_dict['cases_on_time'] + self.data_dict['cases_not_on_time'])


    def count_appeal(self):
        raise NotImplementedError

    def save(self):

        self.data_dict['average_duration'] = int(round(self.data_dict['average_duration']))

        self.data_dict['judge'] = self.judge.id
        keys = ','.join(["`" + k + "`" for k in self.data_dict])

        values = list(self.data_dict.values())

        sql_query = (f"REPLACE INTO `{self.judge_results_table}` ({keys}) "
                     f"VALUES ({','.join('%s' for i in range(len(values)))})")
        toecyd = DB(db_name=TOECYD)
        toecyd.write(sql_query, values)

        print(f"Number of applications:{self.data_dict['amount']}")
        if 'cases_on_time' in self.data_dict.keys():
            print(f"Cases on time:{self.data_dict['cases_on_time']}")
        if 'cases_not_on_time' in self.data_dict.keys():    
            print(f"Cases not on time:{self.data_dict['cases_not_on_time']}")
        if 'average_duration' in self.data_dict.keys():
            print(f"Cases average duration:{self.data_dict['average_duration']}")

        if 'positive_judgment' in self.data_dict.keys():
            print(f"Positive judgment:{self.data_dict['positive_judgment']}")
        if 'negative_judgment' in self.data_dict.keys():
            print(f"Negative judgment:{self.data_dict['negative_judgment']}")
        if 'other_judgment' in self.data_dict.keys():
            print(f"Other judgment:{self.data_dict['other_judgment']}")

        if 'was_appeal' in self.data_dict.keys():
            print(f"Was appeal:{self.data_dict['was_appeal']}")

        if ('approved_by_appeal' in self.data_dict.keys()) and \
                (('not_approved_by_appeal' in self.data_dict.keys())):

            print(f"Approved_by_appeal:{self.data_dict['approved_by_appeal']}")
            print(f"NOT approved_by_appeal:{self.data_dict['not_approved_by_appeal']}")
        print(f"Time :{datetime.now() - self.start_time}")




    def _prepare_applications(self, applications):
        final_dict = {}
        for app in applications:
            cause_num = app['cause_num']
            if cause_num not in final_dict:
                final_dict[cause_num] = []

            final_dict[cause_num].append(app)

        for final_app_k in final_dict:
            final_dict[final_app_k].sort(key=lambda r: r['adjudication_date'])


        return final_dict


    def _prepare_autoassigned_cases(self, autoassigned_cases):
        final_dict = {}

        for app in autoassigned_cases:
            final_dict[app['number']] = app['date_composition']

        return final_dict







class Commercial(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="3",
            judge_results_table='judges_commercial_statistic',
            judgment_codes=[3, 5]
        )

        # отримуємо всі справи даного судді
        self.all_applications = self._get_application_documents(self._prepare_applications)

    def count_appeal(self):
        self.data_dict['amount'] = len(self.all_applications)





class Admin(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="4",
            judge_results_table='judges_admin_statistic',
            judgment_codes=[3, 5]
        )

        # отримуємо всі справи даного судді
        self.all_applications = self._get_application_documents(self._prepare_applications)

    def count_appeal(self):
        self.data_dict['amount'] = len(self.all_applications)

