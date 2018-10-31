<?php

namespace Toecyd;

use Exception;

/**
 * Представляє собою розпарсене ПІБ судді.
 */
class JudgeNameParsed {
 	/**
	 * Парсить ПІБ судді
	 * повертає його у вигляді масиву
	 * @param string $judge_name_raw
	 * @return array
	 * @throws \Exception
	 */
	public static function parseJudgeName(string $judge_name_raw) {
		$matches = [];
		if (preg_match("/головуючий суддя:\s*([^,;]+)/iu", $judge_name_raw, $matches)) {
			$judge_name_raw = $matches[1];
		}
		// видалення пробільних символів з початку і кінця тексту
		$judge_name_raw = trim($judge_name_raw);
		// видалення слів в дужках
		$judge_name_raw = preg_replace("/\(.+\)/", "", $judge_name_raw);
		// виставлення всіх слів через один пробіл
		$judge_name_raw = preg_replace("/\s+/u", " ", $judge_name_raw);
		// апострофи якщо вони знаходяться в слові приводимо до єдиного стандару
		$judge_name_raw = preg_replace('/(\w)[\'"`´‘′‵”]+(\w)/u', "$1’$2", $judge_name_raw);
		
		$separated = self::separatePIBJudge($judge_name_raw);
		if (empty($separated)) {
			return NULL;
			throw new Exception("Не вдалось розпарсити ім'я судді: '{$judge_name_raw}'");
		}
		$separated['surname'] = self::validateRegister($separated['surname']);
		$separated['name'] = self::validateRegister($separated['name']);
		$separated['patronymic'] = self::validateRegister($separated['patronymic']);
		
		return($separated);
	}
	
	/**
	 * отримує ПІБ судді, розрізає
	 * і повертає його у вигляді масиву
	 * @param string $judge
	 * @return array $arr_judge [surname=>"Петренко", name=>"О", patronymic=>"Б"]
	 */
	private static function separatePIBJudge(string $judge) : array {
		// Петренко О. Б
		if (preg_match('/^(\w\S+) (\w)\.? ?(\w)\.?$/u', $judge, $initials)) {
			return (['surname'=>$initials[1], 'name'=>$initials[2], 'patronymic'=>$initials[3]]);
			// Петренко О.Б О. Б.
		} elseif (preg_match('/^(\w\S+) (\w)\. ?(\w)\./u', $judge, $initials)) {
			return (['surname'=>$initials[1], 'name'=>$initials[2], 'patronymic'=>$initials[3]]);
			// О. Б. Петренко
		} elseif (preg_match('/^(\w)\.? (\w)\.? (\w\S+)$/u', $judge, $initials)) {
			return (['surname'=>$initials[3], 'name'=>$initials[1], 'patronymic'=>$initials[2]]);
			// ОБ. Петренко
		} elseif (preg_match('/^(\w)\.? ?(\w)\.? (\w\S+)$/u', $judge, $initials)) {
			return (['surname'=>$initials[3], 'name'=>$initials[1], 'patronymic'=>$initials[2]]);
			// ОБ.Петренко
		} elseif (preg_match('/^(\w)\.? ?(\w)\.(\w\S+)$/u', $judge, $initials)) {
			return (['surname'=>$initials[3], 'name'=>$initials[1], 'patronymic'=>$initials[2]]);
			// Петренко Олександр Борисович
		} elseif (preg_match('/^(\w\S+) (\w\S+) (\w\S+)$/u', $judge, $initials)) {
			return (['surname'=>$initials[1], 'name'=>$initials[2], 'patronymic'=>$initials[3]]);
		}
		return ([]);
	}
	
	
	/**
	 * Виставляє ПІБ у правильний регістр
	 * @param string $judge
	 * @return array $arr_judge [surname=>"Петренко", name=>"О", patronymic=>"Б"]
	 */
	private static function validateRegister(string $name) : string {
		$name = mb_strtoupper(mb_substr($name, 0, 1)) .
			mb_strtolower(mb_substr($name, 1));
		
		// якщо в прізвищі(імені) є дефіс - піднімаємо букву після дефісу у вехній регістр
		$hyphen = mb_strpos($name, '-');
		if ($hyphen != false) {
			$name = mb_substr($name, 0, $hyphen + 1) .
				mb_strtoupper(mb_substr($name, $hyphen + 1, 1)) .
				mb_substr($name, $hyphen + 2);
		}
		return ($name);
	}
	
}