from analyze.judge import Judge
from analyze.classifier import guess_category
from lib.config import *
from lib.db import DB
from datetime import date


class Section:
    data_dict = {}

    def __init__(self, judge: Judge, justice_kind, judge_results_table,
                 judgment_codes=None, anticipated_category=None):
        self.judge = judge
        self.justice_kind = justice_kind
        self.anticipated_category = anticipated_category
        self.judge_results_table = judge_results_table
        self.judgement_codes = judgment_codes
        self.data_dict = {}
        self.numbers_data = {}

    def _get_all_applications(self) -> list:
        """
        All applications examined by judge

        :return:
        """

        sql_query = (f"SELECT  DISTINCT cause_num, adjudication_date FROM reg{self.judge.region} "
                     f"WHERE judge={self.judge.id} "
                     f"AND justice_kind={self.justice_kind} "
                     f"ORDER BY adjudication_date ASC")
        edrsr = DB(db_name=EDRSR)
        applications = edrsr.read(sql_query)
        return applications

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
            "'" + num['cause_num'] + "'" for num in all_applications
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
        from datetime import datetime
        start_time = datetime.now()

        self.data_dict['cases_on_time'] = 0
        self.data_dict['cases_not_on_time'] = 0

        all_applications = self._get_application_documents(_prepare_applications)

        autoassigned_cases = self._get_autoasigned_cases(
            list(all_applications),
            _prepare_autoassigned_cases)

        if isinstance(self, Civil) or isinstance(self, Criminal):
            self._analyze_in_time_detailed(all_applications, autoassigned_cases)
        elif isinstance(self, AdminOffence):
            self._analyze_in_time_small(all_applications, autoassigned_cases)

        print(f"Days on time:{self.data_dict['cases_on_time']}")
        print(f"Days not on time:{self.data_dict['cases_not_on_time']}")
        print(f"Time :{datetime.now() - start_time}")

    def _analyze_in_time_small(self, all_applications, autoassigned_cases):

        for app_k, app_documents in all_applications.items():
            date_dict = {}
            pause_days = 0
            for document in app_documents:

                date_dict['start_adj_date'] = autoassigned_cases.get(
                    document['cause_num'])

                if document['judgment_code'] == self.numbers_data.get(
                        'stop_judgment_code'):

                    date_dict['end_adj_date'] = document[
                        'adjudication_date']

                    self._count_days_on_time(date_dict, pause_days)
                    break

    def _analyze_in_time_detailed(self, all_applications, autoassigned_cases):

        for app_k, app_documents in all_applications.items():
            date_dict = {}
            pause_days = 0
            for document in app_documents:
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.numbers_data['start_document']
                )
                # якщо зустрівся документ про початок спрви, і раніше ніякого
                # документу про початк не було
                if self.numbers_data.get('start_document') and (
                        category == self.numbers_data['start_document']
                        and not date_dict.get('start_adj_date')):
                    date_dict['start_adj_date'] = document['adjudication_date']
                elif category == self.numbers_data['pause_document']:
                    pause_time = document['adjudication_date']
                # якщо зустрівся документ про відновлення справи, і перед тим
                # був документ про зупинення
                    if (category == self.numbers_data['stop_document']
                            and pause_time):
                        resume_time = document['adjudication_date']
                        pause_days += (resume_time - pause_time).days

                # якщо зустрілась ухвала про закінчення, або кінцеве рішення
                # по справі
                elif (category == self.numbers_data['end_document']
                      or document['judgment_code'] ==
                      self.numbers_data['stop_judgment_code']):
                    date_dict['end_adj_date'] = document['adjudication_date']

                    # if we dont have start date - get it from
                    # autoassigned_cases table
                    if not date_dict.get('start_adj_date'):
                        date_dict['start_adj_date'] = autoassigned_cases.get(
                            document['cause_num'])

                    self._count_days_on_time(date_dict, pause_days)
                    break

    def _count_days_on_time(self, date_dict, pause_days):
        if isinstance(date_dict['start_adj_date'], date):
            interval = (date_dict['end_adj_date'] -
                        date_dict['start_adj_date']
                        ).days - pause_days

            if interval <= self.numbers_data['interval']:
                self.data_dict['cases_on_time'] += 1
            else:
                self.data_dict['cases_not_on_time'] += 1

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


def _prepare_applications(applications):
    final_dict = {}
    for app in applications:
        cause_num = app['cause_num']
        if cause_num not in final_dict:
            final_dict[cause_num] = []

        final_dict[cause_num].append(app)

    for final_app_k in final_dict:
        final_dict[final_app_k].sort(key=lambda r: r['adjudication_date'])

    print(f'Number of applications:{len(final_dict)}')
    return final_dict


def _prepare_autoassigned_cases(autoassigned_cases):
    final_dict = {}

    for app in autoassigned_cases:
        final_dict[app['number']] = app['date_composition']

    return final_dict


class Civil(Section):

    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="1",
            anticipated_category=28,
            judge_results_table='judges_civil_statistic',
            judgment_codes=[3, 5]
        )
        self.numbers_data = {
            'start_document': 8,
            'pause_document': 9,
            'stop_document': 10,
            'end_document': 11,
            'interval': 75,
            'stop_judgment_code': 3
        }

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
            # отримуємо всі документи апеляції по справі
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                # якщо апеляція винесла рішення - точно не вистояло, переходимо до наступної справи
                if document['judgment_code'] == 3:
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
        self.numbers_data = {
            'start_document': 17,
            'pause_document': 18,
            'stop_document': 19,
            'end_document': 20,
            'interval': 183,
            'stop_judgment_code': 5
        }

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
                # якщо апеляція винесла вирок - точно не вистояло,
                # переходимо до наступної справи
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
        self.numbers_data = {
            'interval': 15,
            'stop_judgment_code': 2
        }

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
