from guess import guess_category

from db import (
    get_count_result,
    get_appeal_documents,
    get_count_appeal,
    execute_sql_query
)

JUSTICE_KIND = 1


def count_civil(judge_id):
    judge_civil_dict = {}
    result_civil = get_count_result(judge_id, JUSTICE_KIND)

    judge_civil_dict['amount'] = len(result_civil)
    civil_in_appeal = get_count_appeal(result_civil)

    judge_civil_dict['was_appeal'] = len(civil_in_appeal)
    if judge_civil_dict['was_appeal'] < 50:
        return

    for appeal in civil_in_appeal:
        documents = get_appeal_documents(appeal['cause_num'])
        for document in documents:
            doc_text = document['doc_text']
            category = guess_category(['28', '29'], doc_text)
            if category == '28':
                sql_query = ("UPDATE judges_civil_statistic "
                             "SET approved_by_appeal = approved_by_appeal + 1 "
                             "WHERE judge = {judge_id}")
            else:
                sql_query = ("UPDATE judges_civil_statistic "
                             "SET not_approved_by_appeal = not_approved_by_appeal + 1 "
                             "WHERE judge = {judge_id}")
            execute_sql_query(sql_query)
