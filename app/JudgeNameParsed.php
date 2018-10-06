<?php

namespace Toecyd;

/**
 * Представляє собою розпарсене ПІБ судді.
 */
class JudgeNameParsed
{
    public $surname;
    public $name;
    public $patronymic;

    /**
     * @param string $surname    -- прізвище
     * @param string $name       -- ім'я
     * @param string $patronymic -- по-батькові
     */
    public function __construct($surname, $name, $patronymic) {
        $this->surname = $this->format($surname);
        $this->name = $this->format($name);
        $this->patronymic = $this->format($patronymic);
    }

    /**
     * Форматує рядок таким чином, щоб у ньому була перша буква велика, а всі інші букви малі
     * Використовується для придання єдиного вигляду ПІБ суддів (щоб Іванова та ІВАНОВА не розглядались як різні)
     *
     * @param string $str
     *
     * @return string
     */
    private function format(string $str): string {
        return mb_strtoupper(mb_substr($str, 0, 1)) . mb_strtolower(mb_substr($str, 1));
    }

    /**
     * Парсить ПІБ судді
     *
     * @param string $judge_name_raw
     *
     * @return JudgeNameParsed
     * @throws Exception
     */
    public static function parseJudgeName(string $judge_name_raw) {
        $matches = [];
        if (preg_match("/головуючий суддя:\s*([^,;]+)/iu", $judge_name_raw, $matches)) {
            $judge_name_raw = $matches[1];
        }

        // хак для обробки кейсу "Косач (Драгоманова) Лариса Петрівна"
        $judge_name_raw = str_replace(' (', '(', $judge_name_raw);

        $char_good = "[^\s\.]";
        $char_bad = str_replace('^', '', $char_good);

        $reg_exp_name = "{$char_bad}*({$char_good}+){$char_bad}*";
        $reg_exp_surname = "{$reg_exp_name}{$char_bad}+";
        $reg_exp_initial = "({$char_good}{1}){$char_bad}*";

        $matches = [];
        if (preg_match("/^{$reg_exp_surname}{$reg_exp_name}{$reg_exp_name}/ui", $judge_name_raw, $matches)) {
            // Варіант "Шевченко Анатолій Борисович"
            return new JudgeNameParsed($matches[1], $matches[2], $matches[3]);
        } elseif (preg_match("/^{$reg_exp_surname}{$reg_exp_initial}{$reg_exp_initial}/ui", $judge_name_raw, $matches)) {
            // Варіант "Шевченко А.Б."
            return new JudgeNameParsed($matches[1], $matches[2], $matches[3]);
        } else {
            throw new \Exception("Не вдалось розпарсити ім'я судді: '{$judge_name_raw}'");
        }
    }
}