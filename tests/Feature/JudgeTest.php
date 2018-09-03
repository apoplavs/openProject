<?php

namespace Tests\Feature;

use Toecyd\Judge;
use Toecyd\JudgeNameParsed;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $ukrainkaJudgeName = new JudgeNameParsed('Українка', 'Л', 'П');

        return [
            ['Українка Лариса Петрівна', $ukrainkaJudgeName],
            ['Українка  Лариса Петрівна', $ukrainkaJudgeName],
            ['Українка Л. П.', $ukrainkaJudgeName],
            ['Українка Л.П.', $ukrainkaJudgeName],
            ['Українка Л П', $ukrainkaJudgeName],
            ['Косач-Драгоманова Лариса Петрівна', new JudgeNameParsed('Косач-Драгоманова', 'Л', 'П')],
            ['головуючий суддя: Українка Лариса Петрівна', $ukrainkaJudgeName],
            ['головуючий суддя: Українка Лариса Петрівна, суддя-учасник колегії: Драгоманов Михайло Петрович', $ukrainkaJudgeName],
            ['головуючий суддя: Українка Лариса Петрівна; суддя-учасник колегії: Драгоманов Михайло Петрович', $ukrainkaJudgeName],
        ];
    }
}
