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
        Schema::create('fund_defs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('guild_id');
            $table->foreign('guild_id')->references('id')->on('guilds');
        });

        // Fill the fund_defs table
        $insertQuery = <<<SQL
insert into fund_defs(id,title,notes,active,guild_id)
    select id,title,notes,active,guild_id from funds
SQL;
        DB::statement($insertQuery);
        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('fund_defs_id_seq', (select MAX(id) FROM fund_defs))");
        }

        Schema::table('funds', function(Blueprint $table) {
            $table->json('options')->nullable();
            $table->unsignedBigInteger('round_id')->nullable();
            $table->foreign('round_id')->references('id')->on('rounds');
            $table->unsignedBigInteger('def_id')->nullable();
            $table->foreign('def_id')->references('id')->on('fund_defs');
            $table->unsignedBigInteger('start_sid')->nullable();
            $table->foreign('start_sid')->references('id')->on('sessions');
            $table->unsignedBigInteger('end_sid')->nullable();
            $table->foreign('end_sid')->references('id')->on('sessions');
            $table->unsignedBigInteger('interest_sid')->nullable();
            $table->foreign('interest_sid')->references('id')->on('sessions');
        });

        // Fill the funds table
        $insertQuery = <<<SQL
insert into funds(title,options,round_id,def_id,start_sid,end_sid,interest_sid,guild_id)
    select '' as title, '{}' as options, r.id as round_id, f.id as def_id,
        ss.id as start_sid, se.id as end_sid, se.id as interest_sid, f.guild_id
    from rounds r
    inner join funds f on r.guild_id=f.guild_id and f.round_id is null
    inner join sessions ss on ss.round_id=r.id and
        ss.start_at=(select min(start_at) from sessions s1 where s1.round_id=r.id)
    inner join sessions se on se.round_id=r.id and
        se.start_at=(select max(start_at) from sessions s2 where s2.round_id=r.id)
SQL;
        DB::statement($insertQuery);

        // Update the fund_id field in the savings table
        $updateQuery = <<<SQL
update savings t set fund_id=(select f.id from funds f
    where f.id in (select id from funds ft where ft.def_id=t.fund_id)
    and f.round_id=(select s.round_id from sessions s where s.id=t.session_id))
SQL;
        DB::statement($updateQuery);

        // Update the fund_id field in the loans table
        $updateQuery = <<<SQL
update loans t set fund_id=(select f.id from funds f
    where f.id in (select id from funds ft where ft.def_id=t.fund_id)
    and f.round_id=(select s.round_id from sessions s where s.id=t.session_id))
SQL;
        DB::statement($updateQuery);

        // Update the fund_id field in the closings table
        $updateQuery = <<<SQL
update closings t set fund_id=(select f.id from funds f
    where f.id in (select id from funds ft where ft.def_id=t.fund_id)
    and f.round_id=(select s.round_id from sessions s where s.id=t.session_id))
SQL;
        DB::statement($updateQuery);

        $sql = <<<SQL
drop view if exists v_funds
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_funds as
    select p.id as fund_id, ss.start_at, se.start_at as end_at
        from funds p
        inner join sessions ss on p.start_sid=ss.id
        inner join sessions se on p.end_sid=se.id
SQL;
        DB::statement($sql);

        // Copy closings data
        DB::table('closings')->get()->each(function($closing) {
            $query = DB::table('funds')->where('id', $closing->fund_id);
            if($closing->type === 'r')
            {
                $query->update([
                    'options' => $closing->options,
                    'end_sid' => $closing->session_id,
                    'interest_sid' => $closing->session_id,
                ]);
                return;
            }
            if($closing->type === 'i')
            {
                $query->update([
                    'interest_sid' => $closing->session_id,
                ]);
            }
        });
        Schema::dropIfExists('closings');

        Schema::table('funds', function(Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('notes');
            $table->dropColumn('active');
            $table->dropColumn('guild_id');
        });

        // Delete the obsolete entries
        DB::table('funds')->whereNull('round_id')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('closings', function (Blueprint $table) {
            $table->id();
            $table->json('options');
            $table->char('type', 1); // enum('type', ['r', 'i']);
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('fund_id');
            $table->foreign('fund_id')->references('id')->on('funds');
            $table->unique(['type', 'session_id', 'fund_id']);
        });

        // Fill the funds table
        $insertQuery = <<<SQL
insert into closings(options,type,session_id,fund_id)
    select options,'r',end_sid,id from funds
SQL;
        DB::statement($insertQuery);
        $insertQuery = <<<SQL
insert into closings(options,type,session_id,fund_id)
    select '{}','i',interest_sid,id from funds f
    where f.interest_sid != f.end_sid
SQL;
        DB::statement($insertQuery);

        $sql = <<<SQL
drop view if exists v_funds
SQL;
        DB::statement($sql);

        Schema::table('funds', function(Blueprint $table) {
            $table->string('title', 100)->default('');
            $table->string('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('guild_id')->nullable();
            $table->foreign('guild_id')->references('id')->on('guilds');
        });

        // Fill the funds table
        $insertQuery = <<<SQL
insert into funds(title,notes,active,guild_id,def_id)
    select title,notes,active,guild_id,id from fund_defs
SQL;
        DB::statement($insertQuery);

        // Update the fund_id field in the savings table
        // The new rows are the ones with a non null guild_id values
        $updateQuery = <<<SQL
update savings t set
    fund_id=(select f.id from funds f where f.guild_id is not null
        and f.def_id=(select def_id from funds fd where fd.id=t.fund_id))
SQL;
        DB::statement($updateQuery);

        // Update the fund_id field in the loans table
        // The new rows are the ones with a non null guild_id values
        $updateQuery = <<<SQL
update loans t set
    fund_id=(select f.id from funds f where f.guild_id is not null
        and f.def_id=(select def_id from funds fd where fd.id=t.fund_id))
SQL;
        DB::statement($updateQuery);

        // Update the fund_id field in the closings table
        // The new rows are the ones with a non null guild_id values
        $updateQuery = <<<SQL
update closings t set
    fund_id=(select f.id from funds f where f.guild_id is not null
        and f.def_id=(select def_id from funds fd where fd.id=t.fund_id))
SQL;
        DB::statement($updateQuery);

        // Delete the obsolete entries
        DB::table('funds')->whereNull('guild_id')->delete();

        Schema::table('funds', function(Blueprint $table) {
            $table->unsignedBigInteger('guild_id')->nullable(false)->change();
            $table->dropColumn('options');
            $table->dropColumn('round_id');
            $table->dropColumn('def_id');
            $table->dropColumn('start_sid');
            $table->dropColumn('end_sid');
            $table->dropColumn('interest_sid');
        });

        Schema::dropIfExists('fund_defs');
    }
};
