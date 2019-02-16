ROOT_PATH = '/home/andriy/apache/t_phpMy/ml'

PICKLES_PATH = f'{ROOT_PATH}/pickles'

EDRSR = 'schema_mysqld'
TOECYD = 'mysqli'

DB_EDRSR = {
               'type': 'mysql',
               'host': '127.0.0.1',
               'dbname': 'schema_mysqld',
               'charset': 'utf8',
               'user': 'root',
               'pass': '1',
               'port': '3306'
           }

DB_TOECYD = {
               'type': 'mysql',
               'host': '127.0.0.1',
               'dbname': 'mysqli',
               'charset': 'utf8',
               'user': 'root',
               'pass': '1',
               'port': '3306'
           }

DB_DATASETS = {
               'type': 'mysql',
               'host': '127.0.0.1',
               'dbname': 'forming_dataset',
               'charset': 'utf8',
               'user': 'root',
               'pass': '1',
               'port': '3306'
           }
