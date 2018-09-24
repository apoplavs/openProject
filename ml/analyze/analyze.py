import sys

from civil import count_civil
from db import get_toecyd_connection

if __name__ == '__main__':
    """
    To run script you need to type 'python analyze.py [judge id]' 
    in your terminal
    """
    # arguments = sys.argv[1:]
    #
    # if len(arguments) != 1:
    #     print('You should provide the only one argument')
    #     sys.exit()
    #
    # input_judge = arguments[0]
    # if not input_judge.isdigit():
    #     print('You should provide the valid judge id')
    #     sys.exit()

    input_judge = 1211
    toecyd_conn = get_toecyd_connection()

    count_civil(input_judge)