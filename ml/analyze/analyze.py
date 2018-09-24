from sections import *

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

    input_judge = '1211'

    judge = Judge(judge_id=input_judge)

    civil = Civil(judge=judge)
    civil.count()
    civil.save()

    criminal = Criminal(judge=judge)
    criminal.count()
    criminal.save()

    comm = Commercial(judge=judge)
    comm.count()
    comm.save()

    admin = Admin(judge=judge)
    admin.count()
    admin.save()

    admin_offence = AdminOffence(judge=judge)
    admin_offence.count()
    admin_offence.save()
