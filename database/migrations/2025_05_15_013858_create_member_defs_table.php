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
        // The v_bills view depends on members data and must be updated.
        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

        Schema::create('member_defs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->date('registered_at')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('guild_id');
            $table->foreign('guild_id')->references('id')->on('guilds');
        });

        // Fill the member_defs table
        $insertQuery = <<<SQL
insert into member_defs(id,name,email,phone,address,city,registered_at,active,guild_id)
    select id,name,email,phone,address,city,registered_at,active,guild_id from members
SQL;
        DB::statement($insertQuery);

        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('member_defs_id_seq', (select MAX(id) FROM member_defs))");
        }

        Schema::table('members', function(Blueprint $table) {
            $table->unsignedBigInteger('round_id')->nullable();
            $table->foreign('round_id')->references('id')->on('rounds');
            $table->unsignedBigInteger('def_id')->nullable();
            $table->foreign('def_id')->references('id')->on('member_defs');
            $table->unique(['round_id', 'def_id']);
            // Delete the duplicated columns
            $table->dropColumn(['name', 'email', 'phone', 'address',
                'city', 'registered_at', 'birthday', 'active', 'guild_id']);
        });

        // Create a temp view
        $createViewQuery = <<<SQL
create view tmp_v_round_member as
    select distinct l.member_id, s.round_id
        from loans l
        inner join sessions s on s.id = l.session_id
    union select distinct v.member_id, s.round_id
        from savings v
        inner join sessions s on s.id = v.session_id
    union select distinct o.member_id, s.round_id
        from outflows o
        inner join sessions s on s.id = o.session_id
        where o.member_id is not null
    union select distinct a.member_id, s.round_id
        from absences a
        inner join sessions s on s.id = a.session_id
    union select distinct s.member_id, p.round_id
        from subscriptions s
        inner join pools p on p.id = s.pool_id
    union select distinct s.host_id as member_id, s.round_id
        from sessions s where s.host_id is not null
    union select distinct lb.member_id, s.round_id
        from libre_bills lb
        inner join sessions s on s.id = lb.session_id
    union select distinct sb.member_id, s.round_id
        from session_bills sb
        inner join sessions s on s.id = sb.session_id
    union select distinct rb.member_id, rb.round_id
        from round_bills rb
    union select distinct ob.member_id, min(r.id) as round_id
        from oneoff_bills ob
        inner join member_defs md on md.id = ob.member_id
        inner join rounds r on r.guild_id = md.guild_id
        group by ob.member_id
SQL;
        DB::statement($createViewQuery);

        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('members_id_seq', (select MAX(id) FROM members))");
        }

        // Add new entries to the members table
        $insertQuery = <<<SQL
insert into members(round_id,def_id)
    select distinct round_id,member_id from tmp_v_round_member
SQL;
        DB::statement($insertQuery);

        // Drop the temp view
        $dropViewQuery = <<<SQL
drop view if exists tmp_v_round_member
SQL;
        DB::statement($dropViewQuery);

        // Update the foreign keys with the new members ids
        $updateQuery = <<<SQL
update loans lo set member_id = (select id from members m
    where m.def_id = lo.member_id and m.round_id is not null and
        m.round_id = (select round_id from sessions se where se.id = lo.session_id));
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update savings sa set member_id = (select id from members m
    where m.def_id = sa.member_id and m.round_id is not null and
        m.round_id = (select round_id from sessions se where se.id = sa.session_id));
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update outflows ou set member_id = (select id from members m
    where m.def_id = ou.member_id and m.round_id is not null and
        m.round_id = (select round_id from sessions se where se.id = ou.session_id));
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update absences ab set member_id = (select id from members m
    where m.def_id = ab.member_id and m.round_id is not null and
        m.round_id = (select round_id from sessions se where se.id = ab.session_id));
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update subscriptions su set member_id = (select id from members m
    where m.def_id = su.member_id and m.round_id is not null and
        m.round_id = (select round_id from pools po where po.id = su.pool_id));
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update sessions se set host_id = (select id from members m
    where m.def_id = se.host_id and m.round_id is not null and m.round_id = se.round_id);
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update libre_bills lb set member_id = (select id from members m
    where m.def_id = lb.member_id and m.round_id is not null and
        m.round_id = (select round_id from sessions se where se.id = lb.session_id));
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update session_bills sb set member_id = (select id from members m
    where m.def_id = sb.member_id and m.round_id is not null and
        m.round_id = (select round_id from sessions se where se.id = sb.session_id));
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update round_bills rb set member_id = (select id from members m
    where m.def_id = rb.member_id and m.round_id is not null and m.round_id = rb.round_id);
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update oneoff_bills ob set member_id = (select id from members m
    where m.def_id = ob.member_id and m.round_id is not null and
        m.round_id = (select min(r.id) from rounds r where r.guild_id =
            (select md.guild_id from member_defs md where md.id = m.def_id)));
SQL;
        DB::statement($updateQuery);

        // Delete the obsolete entries
        DB::table('members')->whereNull('round_id')->delete();

        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('members_id_seq', (select MAX(id) FROM members))");
        }

        Schema::table('members', function(Blueprint $table) {
            $table->unsignedBigInteger('round_id')->nullable(false)->change();
            $table->unsignedBigInteger('def_id')->nullable(false)->change();
        });

        // The v_bills view depends on members data and must be updated.
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('member_defs');
        throw new Exception('Rollback is not allowed on this migration.');
    }
};
