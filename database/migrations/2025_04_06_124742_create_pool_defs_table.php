<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pool_defs', function(Blueprint $table) {
            $table->id();
            $table->string('title', 300);
            $table->integer('amount');
            $table->string('notes')->nullable();
            $table->json('properties');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('guild_id');
            $table->foreign('guild_id')->references('id')->on('guilds');
        });

        // Fill the pool_defs table
        $insertQuery = <<<SQL
insert into pool_defs(id,title,amount,notes,properties,active,guild_id)
    select p.id,p.title,p.amount,p.notes,p.properties,true,r.guild_id
        from pools p inner join rounds r on r.id=p.round_id
SQL;
        DB::statement($insertQuery);
        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('pool_defs_id_seq', (select MAX(id) FROM pool_defs))");
        }

        Schema::table('pools', function(Blueprint $table) {
            $table->unsignedBigInteger('def_id')->nullable();
            $table->foreign('def_id')->references('id')->on('pool_defs');
            $table->unsignedBigInteger('start_sid')->nullable();
            $table->foreign('start_sid')->references('id')->on('sessions');
            $table->unsignedBigInteger('end_sid')->nullable();
            $table->foreign('end_sid')->references('id')->on('sessions');
        });

        // The pool updates queries require all rounds to have at least one session.
        DB::table('rounds')->whereNotExists(function ($query) {
            $query->select(DB::raw(1))->from('sessions')
                ->whereColumn('sessions.round_id', 'rounds.id');
        })->get()->each(function($round) {
            DB::table('sessions')->insert([
                'title' => 'First session',
                'status' => 0,
                'start_at' => '2022-01-01',
                'end_at' => '2022-01-01',
                'round_id' => $round->id,
            ]);
        });

        $updateQuery = <<<SQL
update pools p set def_id = id,
    end_sid=(select min(se.id) from sessions se
        inner join v_pools vp on vp.id=p.id and vp.end_at=se.start_at
        inner join rounds r on se.round_id=r.id and r.guild_id=vp.guild_id),
    start_sid=(select min(ss.id) from sessions ss
        inner join v_pools vp on vp.id=p.id and vp.start_at=ss.start_at
        inner join rounds r on ss.round_id=r.id and r.guild_id=vp.guild_id)
SQL;
        DB::statement($updateQuery);

        Schema::table('pools', function(Blueprint $table) {
            $table->unsignedBigInteger('def_id')->nullable(false)->change();
            $table->unsignedBigInteger('start_sid')->nullable(false)->change();
            $table->unsignedBigInteger('end_sid')->nullable(false)->change();
        });

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

        // This view is no more used.
        $sql = <<<SQL
drop view if exists v_pool_counters
SQL;
        DB::statement($sql);

        Schema::dropIfExists('pool_rounds');

        Schema::table('pools', function(Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('amount');
            $table->dropColumn('notes');
            $table->dropColumn('properties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pools', function(Blueprint $table) {
            $table->string('title', 100)->nullable();
            $table->integer('amount')->nullable();
            $table->string('notes')->nullable();
            $table->json('properties')->nullable();
        });

        Schema::create('pool_rounds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pool_id');
            $table->foreign('pool_id')->references('id')->on('pools');
            $table->unsignedBigInteger('start_session_id');
            $table->foreign('start_session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('end_session_id');
            $table->foreign('end_session_id')->references('id')->on('sessions');
            $table->unique('pool_id');
        });

        $insertQuery = <<<SQL
insert into pool_rounds(pool_id,start_session_id,end_session_id)
    select p.id,p.start_sid,p.end_sid from pools p
SQL;
        DB::statement($insertQuery);

        $updateQuery = <<<SQL
update pools p set
    title=(select title from pool_defs pd where pd.id=p.def_id),
    amount=(select amount from pool_defs pd where pd.id=p.def_id),
    notes=(select notes from pool_defs pd where pd.id=p.def_id),
    properties=(select properties from pool_defs pd where pd.id=p.def_id)
SQL;
        DB::statement($updateQuery);

        Schema::table('pools', function(Blueprint $table) {
            $table->string('title', 100)->nullable(false)->change();
            $table->integer('amount')->nullable(false)->change();
            $table->json('properties')->nullable(false)->change();
        });

        $sql = <<<SQL
drop view if exists v_pools
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_pools as select pp.id, rp.guild_id,
    case
        when exists(select 1 from pool_rounds pra where pra.pool_id = pp.id)
            then (select sa.start_at from pool_rounds prb inner join sessions sa
                on (prb.start_session_id = sa.id and prb.pool_id = pp.id))
            else (select min(start_at) from sessions sb where sb.round_id = pp.round_id)
    end as start_at,
    case
        when exists(select 1 from pool_rounds prc where prc.pool_id = pp.id)
            then (select sc.start_at from pool_rounds prd inner join sessions sc
                on (prd.end_session_id = sc.id and prd.pool_id = pp.id))
            else (select max(start_at) from sessions sd where sd.round_id = pp.round_id)
    end as end_at
from pools pp inner join rounds rp on pp.round_id = rp.id;
SQL;
        DB::statement($sql);

        // Create a view for pools with their numbers of sessions and disabled sessions.
        $sql = <<<SQL
create view v_pool_counters as select pp.id, pp.title, pp.amount,
    case
        when exists(select 1 from pool_rounds pra where pra.pool_id = pp.id)
            then (select count(*) from sessions sa inner join rounds ra
                on (sa.round_id = ra.id and ra.tontine_id = rp.tontine_id)
                where sa.start_at between
                    (select sb.start_at from pool_rounds prb, sessions sb
                        where prb.pool_id = pp.id and prb.start_session_id = sb.id) and
                    (select sc.start_at from pool_rounds prc, sessions sc
                        where prc.pool_id = pp.id and prc.end_session_id  = sc.id))
            else (select count(*) from sessions sd where sd.round_id = pp.round_id)
    end as sessions,
    case
        when exists(select 1 from pool_rounds prd where prd.pool_id = pp.id)
            then (select count(*) from pool_session_disabled psa inner join sessions se
                on (psa.session_id = se.id and psa.pool_id = pp.id)
                where se.start_at between
                    (select sf.start_at from pool_rounds pre, sessions sf
                        where pre.pool_id = pp.id and pre.start_session_id = sf.id) and
                    (select sg.start_at from pool_rounds prf, sessions sg
                        where prf.pool_id = pp.id and prf.end_session_id  = sg.id))
            else (select count(*) from pool_session_disabled psb where psb.pool_id = pp.id)
    end as disabled_sessions
from pools pp inner join rounds rp on pp.round_id = rp.id;
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_pool_session
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_pool_session as
    select distinct p.id as pool_id, s.id as session_id
        from pools p, sessions s, rounds rp, rounds rs, guilds gp, guilds gs, pool_rounds pr
            where
                (p.round_id = rp.id and s.round_id = rp.id
                and not exists(select 1 from pool_rounds pra where pra.pool_id = p.id))
            or
                (p.round_id = rp.id and rp.guild_id = gp.id
                and s.round_id = rs.id and rs.guild_id = gs.id
                and gp.id = gs.id and pr.pool_id = p.id
                and (s.start_at between
                    (select sc.start_at from pool_rounds prc, sessions sc
                        where prc.pool_id = p.id and prc.start_session_id = sc.id) and
                    (select sd.start_at from pool_rounds prd, sessions sd
                        where prd.pool_id = p.id and prd.end_session_id  = sd.id)))
SQL;
        DB::statement($sql);

        Schema::table('pools', function(Blueprint $table) {
            $table->dropColumn('def_id');
            $table->dropColumn('start_sid');
            $table->dropColumn('end_sid');
        });

        Schema::dropIfExists('pool_defs');
    }
};
