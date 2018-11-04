import pymysql.cursors
from lib.config import *

edrsr_connection = pymysql.connect(host=DB_EDRSR['host'],
                                   user=DB_EDRSR['user'],
                                   password=DB_EDRSR['pass'],
                                   db=DB_EDRSR['dbname'],
                                   charset=DB_EDRSR['charset'],
                                   cursorclass=pymysql.cursors.DictCursor)

toecyd_connection = pymysql.connect(host=DB_TOECYD['host'],
                                    user=DB_TOECYD['user'],
                                    password=DB_TOECYD['pass'],
                                    db=DB_TOECYD['dbname'],
                                    charset=DB_TOECYD['charset'],
                                    cursorclass=pymysql.cursors.DictCursor)


class DB:
    def __init__(self, db_name):

        if db_name not in (EDRSR, TOECYD):
            raise Exception('Please provide valid db_name')

        if db_name == EDRSR:
            self.connection = edrsr_connection
        elif db_name == TOECYD:
            self.connection = toecyd_connection

    def write(self, sql_query, values):

        with self.connection.cursor() as cursor:
            cursor.execute(sql_query, values)

        self.connection.commit()

    def read(self, sql_query):
        with self.connection.cursor() as cursor:
            cursor.execute(sql_query)
            result = cursor.fetchall()

        return result
