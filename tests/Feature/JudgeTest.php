<?php

namespace Tests\Feature;

use Toecyd\Judge;
use Toecyd\JudgeNameParsed;
use Tests\TestCase;

class JudgeTest extends TestCase
{
    /**
     * @dataProvider judgeNamesProvider
     */
    public function testParseJudgeName(string $nameRaw, JudgeNameParsed $nameParsedEtalon)
    {
        $nameParsedTest = Judge::parseJudgeName($nameRaw);
        $this->assertEquals($nameParsedEtalon, $nameParsedTest);
    }

    public function judgeNamesProvider()
    {
        $ukrainkaJudgeName = new JudgeNameParsed('Українка', 'Лариса', 'Петрівна');
        $ukrainkaJudgeInitials = new JudgeNameParsed('Українка', 'Л', 'П');

        return [
            ['Українка Лариса Петрівна', $ukrainkaJudgeName],
            ['Українка  Лариса Петрівна', $ukrainkaJudgeName],
            ['Українка Л. П.', $ukrainkaJudgeInitials],
            ['Українка Л.П.', $ukrainkaJudgeInitials],
            [' Українка Л.П.', $ukrainkaJudgeInitials],
            ['Українка Л П', $ukrainkaJudgeInitials],
            ['Косач-Драгоманова Лариса Петрівна', new JudgeNameParsed('Косач-Драгоманова', 'Лариса', 'Петрівна')],
            ['Іваненко Дар\'я Петрівна', new JudgeNameParsed('Іваненко', 'Дар\'я', 'Петрівна')],
            ['Іваненко Дар"я Петрівна', new JudgeNameParsed('Іваненко', 'Дар"я', 'Петрівна')],
            ['Іваненко Анна-Віола Петрівна', new JudgeNameParsed('Іваненко', 'Анна-Віола', 'Петрівна')],
            ['Косач (Драгоманова) Лариса Петрівна', new JudgeNameParsed('Косач(Драгоманова)', 'Лариса', 'Петрівна')],
            ['Косач(Драгоманова) Лариса Петрівна', new JudgeNameParsed('Косач(Драгоманова)', 'Лариса', 'Петрівна')],
            ['Кропив"янська Лариса Петрівна', new JudgeNameParsed('Кропив"янська', 'Лариса', 'Петрівна')],
            ['Кропив\'янська Лариса Петрівна', new JudgeNameParsed('Кропив\'янська', 'Лариса', 'Петрівна')],
            ['Українка Л.П. Лариса Петрівна', $ukrainkaJudgeInitials],
            ['головуючий суддя: Українка Лариса Петрівна', $ukrainkaJudgeName],
            ['головуючий суддя: Українка Лариса Петрівна, суддя-учасник колегії: Драгоманов Михайло Петрович', $ukrainkaJudgeName],
            ['головуючий суддя: Українка Лариса Петрівна; суддя-учасник колегії: Драгоманов Михайло Петрович', $ukrainkaJudgeName],
        ];
    }
}
