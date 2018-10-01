<?php

namespace Toecyd;

class JudgeNameParsed
{
    public $surname;
    public $name;
    public $patronymic;

    public function __construct($surname, $name, $patronymic)
    {
        $this->surname = $this->myMbUcFirst($surname);
        $this->name = $this->myMbUcFirst($name);
        $this->patronymic = $this->myMbUcFirst($patronymic);
    }

    private function myMbUcFirst(string $str):string
    {
        return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
    }
}