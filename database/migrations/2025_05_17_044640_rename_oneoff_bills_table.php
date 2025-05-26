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
        // The v_bills view depends on bills data and must be updated.
        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

        // We create a new table in order to have the indexes renamed properly.
        Schema::create('onetime_bills', function (Blueprint $table) {
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

        // The onetime_bills table is dropped in a next migration.
        $this->copyData('onetime_bills', 'oneoff_bills');

        Schema::dropIfExists('oneoff_bills');

        // The v_bills view depends on bills data and must be updated.
        $sql = <<<SQL
create view v_bills as
    select b.bill_id, 0 as bill_type, md.name as member,
        b.charge_id, md.guild_id, s.round_id, b.session_id
    from libre_bills b
        inner join sessions s on b.session_id = s.id
        inner join members m on b.member_id = m.id
        inner join member_defs md on m.def_id = md.id
    union
    select b.bill_id, 1 as bill_type, md.name as member,
        b.charge_id, md.guild_id, s.round_id, b.session_id
    from session_bills b
        inner join sessions s on b.session_id = s.id
        inner join members m on b.member_id = m.id
        inner join member_defs md on m.def_id = md.id
    union
    select b.bill_id, 2 as bill_type, md.name as member,
        b.charge_id, md.guild_id, b.round_id,
        (select s1.id from sessions s1 where s1.round_id = b.round_id
            and s1.day_date = (select min(s2.day_date) from sessions s2
            where s2.round_id = b.round_id)) as session_id
    from round_bills b
        inner join members m on b.member_id = m.id
        inner join member_defs md on m.def_id = md.id
    union
    select b.bill_id, 3 as bill_type, md.name as member,
        b.charge_id, md.guild_id, 0 as round_id,
        (select s3.id from sessions s3 inner join rounds r1 on s3.round_id = r1.id
            where r1.guild_id = md.guild_id
            and s3.day_date = (select min(s4.day_date) from sessions s4
            inner join rounds r2 on s4.round_id = r2.id
            where r2.guild_id = md.guild_id)) as session_id
    from onetime_bills b
        inner join members m on b.member_id = m.id
        inner join member_defs md on m.def_id = md.id
SQL;
        DB::statement($sql);

        if(DB::getDriverName() !== 'pgsql')
        {
            return;
        }

        DB::statement("select setval('bills_id_seq', (select MAX(id) FROM bills))");
        DB::statement("select setval('session_bills_id_seq', (select MAX(id) FROM session_bills))");
        DB::statement("select setval('round_bills_id_seq', (select MAX(id) FROM round_bills))");
        DB::statement("select setval('onetime_bills_id_seq', (select MAX(id) FROM onetime_bills))");
        DB::statement("select setval('libre_bills_id_seq', (select MAX(id) FROM libre_bills))");
        DB::statement("select setval('savings_id_seq', (select MAX(id) FROM savings))");
        DB::statement("select setval('loans_id_seq', (select MAX(id) FROM loans))");
        DB::statement("select setval('debts_id_seq', (select MAX(id) FROM debts))");
        DB::statement("select setval('outflows_id_seq', (select MAX(id) FROM outflows))");
        DB::statement("select setval('settlement_targets_id_seq', (select MAX(id) FROM settlement_targets))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The v_bills view depends on bills data and must be updated.
        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

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

        $this->copyData('oneoff_bills', 'onetime_bills');

        Schema::dropIfExists('onetime_bills');

        // The v_bills view depends on bills data and must be updated.
        $sql = <<<SQL
create view v_bills as
    select b.bill_id, 0 as bill_type, md.name as member,
        b.charge_id, md.guild_id, s.round_id, b.session_id
    from libre_bills b
        inner join sessions s on b.session_id = s.id
        inner join members m on b.member_id = m.id
        inner join member_defs md on m.def_id = md.id
    union
    select b.bill_id, 1 as bill_type, md.name as member,
        b.charge_id, md.guild_id, s.round_id, b.session_id
    from session_bills b
        inner join sessions s on b.session_id = s.id
        inner join members m on b.member_id = m.id
        inner join member_defs md on m.def_id = md.id
    union
    select b.bill_id, 2 as bill_type, md.name as member,
        b.charge_id, md.guild_id, b.round_id,
        (select s1.id from sessions s1 where s1.round_id = b.round_id
            and s1.day_date = (select min(s2.day_date) from sessions s2
            where s2.round_id = b.round_id)) as session_id
    from round_bills b
        inner join members m on b.member_id = m.id
        inner join member_defs md on m.def_id = md.id
    union
    select b.bill_id, 3 as bill_type, md.name as member,
        b.charge_id, md.guild_id, 0 as round_id,
        (select s3.id from sessions s3 inner join rounds r1 on s3.round_id = r1.id
            where r1.guild_id = md.guild_id
            and s3.day_date = (select min(s4.day_date) from sessions s4
            inner join rounds r2 on s4.round_id = r2.id
            where r2.guild_id = md.guild_id)) as session_id
    from oneoff_bills b
        inner join members m on b.member_id = m.id
        inner join member_defs md on m.def_id = md.id
SQL;
        DB::statement($sql);
    }
};
