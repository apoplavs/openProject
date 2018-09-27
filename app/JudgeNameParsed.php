<?php

namespace Toecyd;

class JudgeNameParsed
{
    public $surname;
    public $name;
    public $patronymic;

    public function __construct($surname, $name, $patronymic)
    {
        $this->surname = $surname;

        if (mb_strlen($name) != 1) {
            throw new Exception("Ініціал має складатися з однієї букви, проте маємо " . var_export($name, 1));
        }
        $this->name = mb_strtoupper($name);

        if (mb_strlen($patronymic) != 1) {
            throw new Exception("Ініціал має складатися з однієї букви, проте маємо " . var_export($patronymic, 1));
        }
        $this->patronymic = mb_strtoupper($patronymic);
    }
}