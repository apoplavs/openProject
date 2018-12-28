import pymysql.cursors
import json

config = json.loads(open('/Users/oleksandrbaranov/unitprojects/openproject/ml/config.json', 'r').read())

edrsr_connection = pymysql.connect(host=config['db_edrsr']['host'],
                                   user=config['db_edrsr']['user'],
                                   password=config['db_edrsr']['pass'],
                                   db=config['db_edrsr']['dbname'],
                                   charset=config['db_edrsr']['charset'],
                                   cursorclass=pymysql.cursors.DictCursor)

toecyd_connection = pymysql.connect(host=config['db_toecyd']['host'],
                                    user=config['db_toecyd']['user'],
                                    password=config['db_toecyd']['pass'],
                                    db=config['db_toecyd']['dbname'],
                                    charset=config['db_toecyd']['charset'],
                                    cursorclass=pymysql.cursors.DictCursor)


def get_edrsr_connection():
    return edrsr_connection


def get_toecyd_connection():
    return toecyd_connection


def get_count_result(judge_id, justice_kind):
    connection = get_edrsr_connection()
    # connection = get_toecyd_connection()

    with connection.cursor() as cursor:
        sql_query = (f"SELECT  DISTINCT cause_num FROM reg10 "
                     f"WHERE judge={judge_id} "
                     f"AND justice_kind={justice_kind}")

        cursor.execute(sql_query)
        result_count = cursor.fetchall()

    return result_count


def get_count_appeal(result):
    """ Amount of applications examined by judge """

    connection = get_edrsr_connection()
    with connection.cursor() as cursor:
        # in order to avoid coding error I add '' to each appeal id
        all_results = ', '.join("'" + num['cause_num'] + "'"for num in result)
        sql_query = ("SELECT DISTINCT cause_num FROM reg10 "
                     "WHERE court_code=1090 "
                     f"AND cause_num IN ({all_results})")

        cursor.execute(sql_query)
        appeals = cursor.fetchall()
    return appeals


def get_appeal_documents(current_cause_num):
    """ Returns all documents of appeal """

    connection = get_edrsr_connection()
    # connection = get_toecyd_connection()

    with connection.cursor() as cursor:

        sql_query = (f"SELECT * FROM reg10 "
                     f"WHERE court_code=1090 "
                     f"AND cause_num='{current_cause_num}' "
                     f"ORDER BY adjudication_date DESC")

        cursor.execute(sql_query)
        documents = cursor.fetchall()

    return documents

def execute_sql_query(sql_query):
    connection = get_edrsr_connection()

    with connection.cursor() as cursor:
        cursor.execute(sql_query)
