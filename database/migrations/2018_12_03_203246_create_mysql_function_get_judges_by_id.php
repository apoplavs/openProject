<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMysqlFunctionGetJudgesById extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = '
            CREATE FUNCTION get_one_judge_by_id(id INTEGER) RETURNS VARCHAR(767) DETERMINISTIC
              BEGIN
                DECLARE surname VARCHAR(255);
                DECLARE name VARCHAR(255);
                DECLARE patronymic VARCHAR(255);
            
                SELECT judges.name, judges.surname, judges.patronymic
                    INTO name, surname, patronymic
                FROM judges WHERE judges.id = id;
            
                SET name = IF(CHAR_LENGTH(name) = 1, CONCAT(name, \'.\'), name);
                SET patronymic = IF(CHAR_LENGTH(patronymic) = 1, CONCAT(patronymic, \'.\'), patronymic);
            
                RETURN CONCAT(surname, \' \', name, \' \', patronymic);
              END;
            
            CREATE FUNCTION get_judges_by_id(id1 INTEGER, id2 INTEGER, id3 INTEGER) RETURNS VARCHAR(2400) DETERMINISTIC
              BEGIN
                DECLARE judge1 VARCHAR(767);
                DECLARE judge2 VARCHAR(767);
                DECLARE judge3 VARCHAR(767);
            
                SET judge1 = get_one_judge_by_id(id1);
                SET judge2 = get_one_judge_by_id(id2);
                SET judge3 = get_one_judge_by_id(id3);
            
                RETURN IF(judge2 IS NULL, judge1,
                  IF(judge3 IS NULL, CONCAT(\'головуючий суддя: \', judge1, \'; \', judge2),
                     CONCAT(\'головуючий суддя: \', judge1, \'; учасник колегії: \', judge2, \'; учасник колегії: \', judge3))
                );
            
                RETURN get_one_judge_by_id(id1);
              END;
        ';

        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = '
            DROP FUNCTION if exists get_judges_by_id;
            DROP FUNCTION if exists get_one_judge_by_id;
        ';

        DB::unprepared($sql);
    }
}
