from analyze.sections import Section
from analyze.classifier import guess_category


class Civil(Section):

    def __init__(self, judge):
        super().__init__(
            judge=judge,
            justice_kind="1",
            anticipated_category=28,
            judge_results_table='judges_civil_statistic',
            judgment_codes=[3, 5]
        )
        # налаштування і параметри для даної категорії
        self.numbers_data = {
            'start_document': 8,
            'pause_document': 9,
            'stop_document': 10,
            'end_document': 11,
            'interval': 75,
            'stop_judgment_code': 3,
            'positive_judgment': 13,
            'other_judgment': 14,
            'negative_judgment': 15


        }

        # типи рішень
        self.data_dict['positive_judgment'] = 0
        self.data_dict['negative_judgment'] = 0
        self.data_dict['other_judgment'] = 0

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
            # отримуємо всі документи апеляції по справі
            documents = self._get_appeal_documents(appeal['cause_num'])
            for document in documents:
                # якщо апеляція винесла рішення - точно не вистояло, переходимо до наступної справи
                if document['judgment_code'] == 3:
                    self.data_dict['not_approved_by_appeal'] += 1
                    break
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.anticipated_category
                )
                if category == 28:
                    self.data_dict['approved_by_appeal'] += 1
                    break
                elif category == 29:
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
                doc_text = document['doc_text']
                category = guess_category(
                    text=doc_text,
                    anticipated_category=self.numbers_data['start_document']
                )
                # якщо зустрівся документ про початок справи, і раніше ніякого
                # документу про початок не було
                if self.numbers_data.get('start_document') and (
                        category == self.numbers_data['start_document']
                        and not date_dict.get('start_adj_date')):
                    date_dict['start_adj_date'] = document['adjudication_date']

                elif category == self.numbers_data['pause_document']:
                    pause_time = document['adjudication_date']
                    # якщо зустрівся документ про відновлення справи, і перед тим
                    # був документ про зупинення
                    if (category == self.numbers_data['stop_document']
                            and pause_time):
                        resume_time = document['adjudication_date']
                        pause_days += (resume_time - pause_time).days

                # якщо зустрілась ухвала про закінчення, або кінцеве рішення
                # по справі
                elif (category == self.numbers_data['end_document']
                      or document['judgment_code'] ==
                      self.numbers_data['stop_judgment_code']):
                    date_dict['end_adj_date'] = document['adjudication_date']
                    self.count_decisions_types(is_uhvala=category, document=document)

                    # if we dont have start date - get it from
                    # autoassigned_cases table
                    if not date_dict.get('start_adj_date'):
                        date_dict['start_adj_date'] = autoassigned_cases.get(
                            document['cause_num'])

                    self._count_days_on_time(date_dict, pause_days)
                    break


    def count_decisions_types(self, is_uhvala, document):
        """Рахує скільки рішень прийняв суддя"""

        # дізнаємось тип кінцевого документа
        category = guess_category(text=document['doc_text'],
                    anticipated_category=self.numbers_data['positive_judgment'])

        # якщо задоволено вимоги позивача
        if (category == self.numbers_data['positive_judgment']):
            self.data_dict['positive_judgment'] += 1

        # якщо справу вирішено іншим чином
        elif ((is_uhvala == self.numbers_data['end_document']) or
              (category == self.numbers_data['other_judgment'])):
            self.data_dict['other_judgment'] += 1

        # якщо відмовлено у задоволенні вимог позивача
        elif (category == self.numbers_data['negative_judgment']):
            self.data_dict['negative_judgment'] += 1