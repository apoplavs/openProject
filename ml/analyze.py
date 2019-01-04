#!/usr/bin/python
# -*- coding: utf-8 -*-

from analyze.sections import *
from analyze.civil import Civil
from analyze.criminal import Criminal
from analyze.adminoffence import AdminOffence

import sys

from analyze.judge import Judge

if __name__ == '__main__':
    """
    To run script you need to type 'python analyze.py [judge id] [judge id]' 
    in your terminal
    """
    arguments = sys.argv[1:]
    
    if len(arguments) > 100:
        print('You should provide less then 100 arguments')
        sys.exit()
    if len(arguments) == 0:
        print('USAGE: python analyze.py [judge id] [judge id]')
        sys.exit()
    # перевіряємо щоб всі аргументи були цифри
    if any(not a.isdigit() for a in arguments):
        print('You should provide the valid judge id')
        sys.exit()

    for judge_id in arguments:

        judge = Judge(judge_id=judge_id)

        print('\n\n\nJudge ID = ' + str(judge_id))
        print('\nCivil')
        civil = Civil(judge=judge)
        civil.count_appeal()
        civil.analyze_in_time()
        civil.save()

        print('\nCriminal')
        criminal = Criminal(judge=judge)
        criminal.count_appeal()
        criminal.analyze_in_time()
        criminal.save()

        print('\nAdminoffence')
        admin_offence = AdminOffence(judge=judge)
        admin_offence.count_appeal()
        admin_offence.analyze_in_time()
        admin_offence.save()

        print('\nAdmin')
        admin = Admin(judge=judge)
        admin.count_appeal()
        admin.save()

        print('\nCommercial')
        commercial = Commercial(judge=judge)
        commercial.count_appeal()
        commercial.save()
