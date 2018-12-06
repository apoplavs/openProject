ROOT_PATH = '/Users/oleksandrbaranov/unitprojects/openproject/ml'

PICKLES_PATH = f'{ROOT_PATH}/pickles'

EDRSR = 'edrsr'
TOECYD = 'test_toecyd'

DB_EDRSR = {
               'type': 'mysql',
               'host': 'develop-toecyd.ckbu3cst0zhm.eu-central-1.rds.amazonaws.com',
               'dbname': 'ml_toecyd',
               'charset': 'utf8',
               'user': 'develop',
               'pass': 'openproject',
               'port': '3306'
           }

DB_TOECYD = {
               'type': 'mysql',
               'host': 'develop-toecyd.ckbu3cst0zhm.eu-central-1.rds.amazonaws.com',
               'dbname': 'develop_toecyd',
               'charset': 'utf8',
               'user': 'develop',
               'pass': 'openproject',
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
