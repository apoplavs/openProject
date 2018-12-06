ROOT_PATH = '/home/apoplavs/apache/toecyd.local/ml'

PICKLES_PATH = f'{ROOT_PATH}/pickles'

EDRSR = 'edrsr'
TOECYD = 'test_toecyd'

DB_EDRSR = {
               'type': 'mysql',
               'host': '127.0.0.1',
               'dbname': 'edrsr1',
               'charset': 'utf8',
               'user': 'root',
               'pass': '1',
               'port': '3306'
           }

DB_TOECYD = {
               'type': 'mysql',
               'host': '127.0.0.1',
               'dbname': 'toecyd',
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
