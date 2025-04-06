<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @param string $source
     * @param string $target
     *
     * @return void
     */
    private function copyData(string $source, string $target)
    {
        $fields = 'bill_id,charge_id,member_id';
        DB::statement("INSERT INTO $target($fields) SELECT $fields FROM $source ORDER BY id ASC");
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // We create a new table in order to have the indexes renamed properly.
        Schema::create('oneoff_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills');
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unique('bill_id');
            $table->unique(['charge_id', 'member_id']);
        });

        $this->copyData('tontine_bills', 'oneoff_bills');

        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_bills as
    select libre_bills.bill_id, 0 as bill_type, members.name as member,
        libre_bills.charge_id, members.tontine_id, sessions.round_id, libre_bills.session_id
    from libre_bills inner join sessions on libre_bills.session_id = sessions.id
        inner join members on libre_bills.member_id = members.id
    union
    select session_bills.bill_id, 1 as bill_type, members.name as member,
        session_bills.charge_id, members.tontine_id, sessions.round_id, session_bills.session_id
    from session_bills inner join sessions on session_bills.session_id = sessions.id
        inner join members on session_bills.member_id = members.id
    union
    select round_bills.bill_id, 2 as bill_type, members.name as member,
        round_bills.charge_id, members.tontine_id, round_bills.round_id,
        (select s1.id from sessions s1 where s1.round_id = round_bills.round_id
            and s1.start_at = (select min(s2.start_at) from sessions s2
            where s2.round_id = round_bills.round_id)) as session_id
    from round_bills inner join members on round_bills.member_id = members.id
    union
    select oneoff_bills.bill_id, 3 as bill_type, members.name as member,
        oneoff_bills.charge_id, members.tontine_id, 0 as round_id,
        (select s3.id from sessions s3 inner join rounds r1 on s3.round_id = r1.id
            where r1.tontine_id = members.tontine_id
            and s3.start_at = (select min(s4.start_at) from sessions s4
            inner join rounds r2 on s4.round_id = r2.id
            where r2.tontine_id = members.tontine_id)) as session_id
    from oneoff_bills inner join members on oneoff_bills.member_id = members.id
SQL;
        DB::statement($sql);

        Schema::dropIfExists('tontine_bills');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('tontine_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills');
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unique('bill_id');
            $table->unique(['charge_id', 'member_id']);
        });

        $this->copyData('oneoff_bills', 'tontine_bills');

        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_bills as
    select libre_bills.bill_id, 0 as bill_type, members.name as member,
        libre_bills.charge_id, members.tontine_id, sessions.round_id, libre_bills.session_id
    from libre_bills inner join sessions on libre_bills.session_id = sessions.id
        inner join members on libre_bills.member_id = members.id
    union
    select session_bills.bill_id, 1 as bill_type, members.name as member,
        session_bills.charge_id, members.tontine_id, sessions.round_id, session_bills.session_id
    from session_bills inner join sessions on session_bills.session_id = sessions.id
        inner join members on session_bills.member_id = members.id
    union
    select round_bills.bill_id, 2 as bill_type, members.name as member,
        round_bills.charge_id, members.tontine_id, round_bills.round_id,
        (select s1.id from sessions s1 where s1.round_id = round_bills.round_id
            and s1.start_at = (select min(s2.start_at) from sessions s2
            where s2.round_id = round_bills.round_id)) as session_id
    from round_bills inner join members on round_bills.member_id = members.id
    union
    select tontine_bills.bill_id, 3 as bill_type, members.name as member,
        tontine_bills.charge_id, members.tontine_id, 0 as round_id,
        (select s3.id from sessions s3 inner join rounds r1 on s3.round_id = r1.id
            where r1.tontine_id = members.tontine_id
            and s3.start_at = (select min(s4.start_at) from sessions s4
            inner join rounds r2 on s4.round_id = r2.id
            where r2.tontine_id = members.tontine_id)) as session_id
    from tontine_bills inner join members on tontine_bills.member_id = members.id
SQL;
        DB::statement($sql);

        Schema::dropIfExists('oneoff_bills');
    }
};
