<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sessions', function(Blueprint $table) {
            $table->date('day_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
        });

        $updateQuery = <<<SQL
update sessions set day_date=start_at::date, start_time=start_at::time, end_time=end_at::time
SQL;
        DB::statement($updateQuery);

        Schema::table('sessions', function(Blueprint $table) {
            $table->date('day_date')->nullable(false)->change();
            $table->time('start_time')->nullable(false)->change();
            $table->time('end_time')->nullable(false)->change();
            $table->unique(['round_id', 'day_date']);
        });

        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_bills as
    select libre_bills.bill_id, 0 as bill_type, members.name as member,
        libre_bills.charge_id, members.guild_id, sessions.round_id, libre_bills.session_id
    from libre_bills inner join sessions on libre_bills.session_id = sessions.id
        inner join members on libre_bills.member_id = members.id
    union
    select session_bills.bill_id, 1 as bill_type, members.name as member,
        session_bills.charge_id, members.guild_id, sessions.round_id, session_bills.session_id
    from session_bills inner join sessions on session_bills.session_id = sessions.id
        inner join members on session_bills.member_id = members.id
    union
    select round_bills.bill_id, 2 as bill_type, members.name as member,
        round_bills.charge_id, members.guild_id, round_bills.round_id,
        (select s1.id from sessions s1 where s1.round_id = round_bills.round_id
            and s1.day_date = (select min(s2.day_date) from sessions s2
            where s2.round_id = round_bills.round_id)) as session_id
    from round_bills inner join members on round_bills.member_id = members.id
    union
    select oneoff_bills.bill_id, 3 as bill_type, members.name as member,
        oneoff_bills.charge_id, members.guild_id, 0 as round_id,
        (select s3.id from sessions s3 inner join rounds r1 on s3.round_id = r1.id
            where r1.guild_id = members.guild_id
            and s3.day_date = (select min(s4.day_date) from sessions s4
            inner join rounds r2 on s4.round_id = r2.id
            where r2.guild_id = members.guild_id)) as session_id
    from oneoff_bills inner join members on oneoff_bills.member_id = members.id
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_pools
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_pools as
    select p.id as pool_id, ss.day_date as start_date, se.day_date as end_date,
        (select count(s.id) from sessions s
            where s.day_date between ss.day_date and se.day_date
            and s.round_id in (select id from rounds r where r.guild_id=pd.guild_id))
            as sessions_count,
        (select count(psd.session_id)
            from pool_session_disabled psd
            inner join sessions pss on pss.id=psd.session_id
            where psd.pool_id=p.id and pss.day_date between ss.day_date and se.day_date)
            as disabled_sessions_count
        from pools p
        inner join pool_defs pd on pd.id=p.def_id
        inner join sessions ss on ss.id=p.start_sid
        inner join sessions se on se.id=p.end_sid
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_pool_session
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_pool_session as
    select distinct p.id as pool_id, s.id as session_id
        from pools p
        inner join pool_defs pd on pd.id=p.def_id
        inner join sessions se on se.id=p.end_sid
        inner join sessions ss on ss.id=p.start_sid
        inner join sessions s on s.day_date between ss.day_date and se.day_date
            and s.round_id in (select id from rounds r where r.guild_id=pd.guild_id)
            and not exists (select * from pool_session_disabled psd
                where psd.pool_id=p.id and psd.session_id=s.id)
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_funds
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_funds as
    select f.id as fund_id, ss.day_date as start_date, se.day_date as end_date,
        si.day_date as interest_date,
        (select count(s.id) from sessions s
            where s.day_date between ss.day_date and se.day_date
            and s.round_id in (select id from rounds r where r.guild_id=fd.guild_id))
            as sessions_count
        from funds f
        inner join fund_defs fd on f.def_id=fd.id
        inner join sessions ss on f.start_sid=ss.id
        inner join sessions se on f.end_sid=se.id
        inner join sessions si on f.interest_sid=si.id
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_fund_session
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_fund_session as
    select distinct f.id as fund_id, s.id as session_id
        from funds f
        inner join fund_defs fd on fd.id=f.def_id
        inner join sessions se on se.id=f.end_sid
        inner join sessions ss on ss.id=f.start_sid
        inner join sessions s on s.day_date between ss.day_date and se.day_date
            and s.round_id in (select id from rounds r where r.guild_id=fd.guild_id)
SQL;
        DB::statement($sql);

        Schema::table('sessions', function(Blueprint $table) {
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function(Blueprint $table) {
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
        });

        $sql = <<<SQL
update sessions s set
    end_at=(select day_date + end_time as datetime_field from sessions sd where sd.id=s.id),
    start_at=(select day_date + start_time as datetime_field from sessions sd where sd.id=s.id)
SQL;
        DB::statement($sql);

        Schema::table('sessions', function(Blueprint $table) {
            $table->datetime('start_at')->nullable(false)->change();
            $table->datetime('end_at')->nullable(false)->change();
        });

        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_bills as
    select libre_bills.bill_id, 0 as bill_type, members.name as member,
        libre_bills.charge_id, members.guild_id, sessions.round_id, libre_bills.session_id
    from libre_bills inner join sessions on libre_bills.session_id = sessions.id
        inner join members on libre_bills.member_id = members.id
    union
    select session_bills.bill_id, 1 as bill_type, members.name as member,
        session_bills.charge_id, members.guild_id, sessions.round_id, session_bills.session_id
    from session_bills inner join sessions on session_bills.session_id = sessions.id
        inner join members on session_bills.member_id = members.id
    union
    select round_bills.bill_id, 2 as bill_type, members.name as member,
        round_bills.charge_id, members.guild_id, round_bills.round_id,
        (select s1.id from sessions s1 where s1.round_id = round_bills.round_id
            and s1.start_at = (select min(s2.start_at) from sessions s2
            where s2.round_id = round_bills.round_id)) as session_id
    from round_bills inner join members on round_bills.member_id = members.id
    union
    select oneoff_bills.bill_id, 3 as bill_type, members.name as member,
        oneoff_bills.charge_id, members.guild_id, 0 as round_id,
        (select s3.id from sessions s3 inner join rounds r1 on s3.round_id = r1.id
            where r1.guild_id = members.guild_id
            and s3.start_at = (select min(s4.start_at) from sessions s4
            inner join rounds r2 on s4.round_id = r2.id
            where r2.guild_id = members.guild_id)) as session_id
    from oneoff_bills inner join members on oneoff_bills.member_id = members.id
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_pools
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_pools as
    select p.id as pool_id, ss.start_at, se.start_at as end_at,
        (select count(s.id) from sessions s
            where s.start_at between ss.start_at and se.start_at
            and s.round_id in (select id from rounds r where r.guild_id=pd.guild_id))
            as sessions_count,
        (select count(psd.session_id)
            from pool_session_disabled psd
            inner join sessions pss on pss.id=psd.session_id
            where psd.pool_id=p.id and pss.start_at between ss.start_at and se.start_at)
            as disabled_sessions_count
        from pools p
        inner join pool_defs pd on pd.id=p.def_id
        inner join sessions ss on ss.id=p.start_sid
        inner join sessions se on se.id=p.end_sid
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_pool_session
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_pool_session as
    select distinct p.id as pool_id, s.id as session_id
        from pools p
        inner join pool_defs pd on pd.id=p.def_id
        inner join sessions se on se.id=p.end_sid
        inner join sessions ss on ss.id=p.start_sid
        inner join sessions s on s.start_at between ss.start_at and se.start_at
            and s.round_id in (select id from rounds r where r.guild_id=pd.guild_id)
            and not exists (select * from pool_session_disabled psd
                where psd.pool_id=p.id and psd.session_id=s.id)
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_funds
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_funds as
    select f.id as fund_id, ss.start_at, se.start_at as end_at, si.start_at as interest_at,
        (select count(s.id) from sessions s
            where s.start_at between ss.start_at and se.start_at
            and s.round_id in (select id from rounds r where r.guild_id=fd.guild_id))
            as sessions_count
        from funds f
        inner join fund_defs fd on f.def_id=fd.id
        inner join sessions ss on f.start_sid=ss.id
        inner join sessions se on f.end_sid=se.id
        inner join sessions si on f.interest_sid=si.id
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_fund_session
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_fund_session as
    select distinct f.id as fund_id, s.id as session_id
        from funds f
        inner join fund_defs fd on fd.id=f.def_id
        inner join sessions se on se.id=f.end_sid
        inner join sessions ss on ss.id=f.start_sid
        inner join sessions s on s.start_at between ss.start_at and se.start_at
            and s.round_id in (select id from rounds r where r.guild_id=fd.guild_id)
SQL;
        DB::statement($sql);

        Schema::table('sessions', function(Blueprint $table) {
            $table->dropColumn('day_date');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
        });
    }
};
