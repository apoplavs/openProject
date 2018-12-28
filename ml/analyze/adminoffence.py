from analyze.sections import Section
from analyze.classifier import guess_category


class AdminOffence(Section):
    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="5",
            judge_results_table='judges_adminoffence_statistic',
            anticipated_category=25,
            judgment_codes=[5, 2]
        )
        # налаштування і параметри для даної категорії
        self.numbers_data = {
            'interval': 15,
            'stop_judgment_code': 2,
            'negative_judgment': 5,
            'positive_judgment': 6
        }

        # типи рішень
        self.data_dict['positive_judgment'] = 0
        self.data_dict['negative_judgment'] = 0

        # отримуємо всі справи даного судді
        self.all_applications = self._get_application_documents(self._prepare_applications)

    def count_appeal(self):

        self.data_dict['amount'] = len(self.all_applications)

        # якщо справ немає, або дуже мало - повертаємось
        if self.data_dict['amount'] < 100:
            return

        civil_in_appeal = self._get_all_appeals(self.all_applications)
        self.data_dict['was_appeal'] = len(civil_in_appeal)
        self.data_dict['approved_by_appeal'] = 0
        self.data_dict['not_approved_by_appeal'] = 0

        if len(civil_in_appeal) < 30:
            return

        for appeal in civil_in_appeal:
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                # якщо апеляція винесла ухвалу - точно вистояло, переходимо до наступної справи
                if document['judgment_code'] == 5:
                    self.data_dict['approved_by_appeal'] += 1
                    break
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.anticipated_category
                )
                if category == 25:
                    self.data_dict['approved_by_appeal'] += 1
                    break
                elif category == 26:
                    self.data_dict['not_approved_by_appeal'] += 1
                    break

    def analyze_in_time(self):
        """рахування скільки часу в середньому суддя витрачає на розгляд справи
        а також скільки справ він розгляну з порушенням строків"""

        autoassigned_cases = self._get_autoasigned_cases(
            list(self.all_applications),
            self._prepare_autoassigned_cases)

        for app_k, app_documents in self.all_applications.items():
            date_dict = {}
            pause_days = 0

            for document in app_documents:

                # отримуємо дату початку справи
                date_dict['start_adj_date'] = autoassigned_cases.get(
                    document['cause_num'])

                if document['judgment_code'] == self.numbers_data.get(
                        'stop_judgment_code'):
                    date_dict['end_adj_date'] = document[
                        'adjudication_date']
                    self.count_decisions_types(document=document)

                    self._count_days_on_time(date_dict, pause_days)
                    break

    def count_decisions_types(self, document):
        """Рахує скільки обвинувальних і виправдувальних постанов виніс суддя"""

        # дізнаємось тип кінцевого документа
        category = guess_category(text=document['doc_text'],
                                  anticipated_category=self.numbers_data['positive_judgment'])

        # якщо це випавдувальна постанова
        if (category == self.numbers_data['positive_judgment']):
            self.data_dict['positive_judgment'] += 1

        # якщо це обвинувальна постанова
        elif (category == self.numbers_data['negative_judgment']):
            self.data_dict['negative_judgment'] += 1
