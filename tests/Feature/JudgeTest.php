<?php

namespace Tests\Feature;

use Exception;
use Toecyd\JudgeNameParsed;
use Tests\TestCase;

/**
 * Тест для пасингу ПІБ судді
 * Class JudgeTest
 * @package Tests\Feature
 */
class JudgeTest extends TestCase
{
	
	/**
	 * @throws \Exception
	 */
	public function testParseIncorrectJudgeName()
    {
        $this->expectException(Exception::class);
        JudgeNameParsed::parseJudgeName('');
		JudgeNameParsed::parseJudgeName('Україн');
    }
	
	
	/**
	 * @dataProvider judgeNamesProvider
	 * @throws \Exception
	 */
    public function testParseJudgeName(string $nameRaw, array $nameParsedEtalon)
    {
        $name_parsed_test = JudgeNameParsed::parseJudgeName($nameRaw);
        $this->assertEquals($nameParsedEtalon, $name_parsed_test);
    }

    public function judgeNamesProvider()
    {
    	$valid_name1 = ['surname'=>'Українка', 'name'=>'Лариса', 'patronymic'=>'Петрівна'];
		$valid_name2 = ['surname'=>'Українка', 'name'=>'Л', 'patronymic'=>'П'];
	
		$valid_name3 = ['surname'=>'Косач-Драгоманова', 'name'=>'Лариса', 'patronymic'=>'Петрівна'];
		$valid_name4 = ['surname'=>'Косач-Драгоманова', 'name'=>'Л', 'patronymic'=>'П'];
	
		$valid_name5 = ['surname'=>'Іваненко', 'name'=>'Дар’я', 'patronymic'=>'Петрівна'];
		$valid_name6 = ['surname'=>'Іваненко', 'name'=>'Анна-Віола', 'patronymic'=>'Петрівна'];
		
		$valid_name7 = ['surname'=>'Кропив’янська', 'name'=>'Лариса', 'patronymic'=>'Петрівна'];

        return [
            ['Українка Лариса Петрівна', $valid_name1],
            ['Українка  Лариса 		Петрівна', $valid_name1],
			['УКРАЇНКА ЛАРИСА ПЕТРІВНА', $valid_name1],
            ['Українка Л. П.', $valid_name2],
            ['Українка Л.П.', $valid_name2],
            [' Українка Л.П.', $valid_name2],
            ['Українка Л П', $valid_name2],
			['Українка ЛП', $valid_name2],
			[' Л П Українка ', $valid_name2],
			['Л.П. Українка', $valid_name2],
			['Л П. Українка', $valid_name2],
			['Л.П.Українка', $valid_name2],
			['ЛП.Українка', $valid_name2],
            ['Косач-Драгоманова Лариса Петрівна', $valid_name3],
			['Косач-драгоманова Лариса Петрівна', $valid_name3],
			['косач-Драгоманова Лариса Петрівна', $valid_name3],
			['косач-драгоманова Л.П', $valid_name4],
			['Л.П косач-драгоманова', $valid_name4],
            ['Іваненко Дар\'я Петрівна', $valid_name5],
            ['Іваненко Дар"я Петрівна', $valid_name5],
			['Іваненко Дар"\'я Петрівна', $valid_name5],
            ['Іваненко Анна-Віола Петрівна', $valid_name6],
			['Іваненко анна-Віола Петрівна', $valid_name6],
			['Іваненко анна-віола Петрівна', $valid_name6],
			['ІВАНЕНКО АННА-ВІОЛА ПЕТРІВНА', $valid_name6],
            ['Українка (Драгоманова) Лариса Петрівна', $valid_name1],
            ['Українка(Драгоманова) Лариса Петрівна', $valid_name1],
			[' (Драгоманова)Українка Лариса Петрівна', $valid_name1],
            ['Кропив"янська Лариса Петрівна', $valid_name7],
            ['Кропив\'янська Лариса Петрівна', $valid_name7],
			['Кропив’янська Лариса Петрівна', $valid_name7],
            ['Українка Л.П. Лариса Петрівна', $valid_name2],
			['Українка Л.П. Л. П.', $valid_name2],
            ['головуючий суддя: Українка Лариса Петрівна', $valid_name1],
			['головуючий суддя: Українка Л. П.', $valid_name2],
			['головуючий суддя: Л. П. Українка ', $valid_name2],
            ['головуючий суддя: Українка Лариса Петрівна, суддя-учасник колегії: Драгоманов Михайло Петрович', $valid_name1],
            ['головуючий суддя: Українка Лариса Петрівна; суддя-учасник колегії: Драгоманов Михайло Петрович', $valid_name1],
        ];
    }
}
