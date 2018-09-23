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


def write_to_db(sql_query, db_name, values):
    connections = {
        'edrsr':get_edrsr_connection(),
        'toecyd': get_toecyd_connection()
    }

    if not db_name in connections:
        raise Exception('Please provide valid db_name')

    connection = connections[db_name]

    with connection.cursor() as cursor:
        cursor.execute(sql_query, values)

    connection.commit()


def read_from_db(sql_query, db_name):
    connections = {
        'edrsr': get_edrsr_connection(),
        'toecyd': get_toecyd_connection()
    }

    if db_name not in connections:
        raise Exception(f'Please provide valid db_name. '
                        f'Examples :{list(connections.keys())}')

    connection = connections[db_name]

    with connection.cursor() as cursor:
        cursor.execute(sql_query)
        result = cursor.fetchall()

    return result

