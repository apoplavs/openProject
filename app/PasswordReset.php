<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    const UPDATED_AT = null; // щоб модель не намагалась заповнити колонку updated_at в БД. Подробиці тут: https://stackoverflow.com/questions/28277955/laravel-unknown-column-updated-at

    protected $fillable = ['email', 'token'];

    protected $primaryKey = 'token'; // для видалення записів з таблиці. Подробиці тут: https://stackoverflow.com/questions/29347253/sqlstate42s22-column-not-found-1054-unknown-column-id-in-where-clause-s
}
