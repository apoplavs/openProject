#!/usr/bin/python
# -*- coding: utf-8 -*-
from analyze.sections import *

import sys

from analyze.judge import Judge

if __name__ == '__main__':
    """
    To run script you need to type 'python analyze.py [judge id] [judge id]' 
    in your terminal
    """
    # arguments = sys.argv[1:]
    #
    # if len(arguments) > 10:
    #     print('You should provide less then ten arguments')
    #     sys.exit()
    # if len(arguments) == 0:
    #     print('USAGE: python analyze.py [judge id] [judge id]')
    #     sys.exit()
    # # перевіряємо щоб всі аргументи були цифри
    # if any(not a.isdigit() for a in arguments):
    #     print('You should provide the valid judge id')
    #     sys.exit()

    arguments = ['1211', '194']
    for judge_id in arguments:

        judge = Judge(judge_id=judge_id)

        civil = Civil(judge=judge)
        civil.analyze_in_time()
        # civil.count()
        civil.save()

        # criminal = Criminal(judge=judge)
        # criminal.count()
        # criminal.save()
        #
        # comm = Commercial(judge=judge)
        # comm.count()
        # comm.save()
        #
        # admin = Admin(judge=judge)
        # admin.count()
        # admin.save()
        #
        # admin_offence = AdminOffence(judge=judge)
        # admin_offence.count()
        # admin_offence.save()
