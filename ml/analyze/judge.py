from db import read_from_db
from config import *
import sys

class Judge:

    def __init__(self, judge_id):
        self._check_judge_id(judge_id)
        self.id = judge_id
        self.court_code = str(self._get_court_code())
        self.region = self._count_region()

    def _check_judge_id(self, judge_id):
        sql_query = f'SELECT id from judges WHERE id={judge_id}'
        judge = read_from_db(sql_query, TOECYD)
        if not judge:
            raise Exception(f'Judge with id {judge_id} not found')

    def _get_court_code(self):
        sql_query = f'SELECT court_code FROM judges WHERE id={self.id}'
        res_court_code = read_from_db(sql_query, EDRSR)

        if not res_court_code:
            raise Exception(f'Judge with id: {self.id}'
                            f' not found in the database')

        return res_court_code[0]['court_code']

    def _count_region(self):
        # get the region from court_code
        return self.court_code[:len(self.court_code)-2]

