from lib.db import DB
from lib.config import *


class Judge:

    def __init__(self, judge_id):
        self.id = judge_id
        self.court_code = str(self._get_court_code())
        self.region = self._count_region()

    def _get_court_code(self):
        sql_query = f'SELECT court_code FROM judges WHERE id={self.id}'
        edrsr = DB(db_name=EDRSR)
        res_court_code = edrsr.read(sql_query)

        if not res_court_code:
            raise Exception(f'Judge with id: {self.id}'
                            f' not found in the database')

        return res_court_code[0]['court_code']

    def _count_region(self):
        # get the region from court_code
        return self.court_code[:len(self.court_code)-2]

    def decrease_cases_on_time(self):
        pass

    def increase_cases_on_time(self):
        pass
