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
        Schema::create('charge_defs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->tinyInteger('type');
            $table->tinyInteger('period');
            $table->integer('amount');
            $table->boolean('lendable')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('guild_id');
            $table->foreign('guild_id')->references('id')->on('guilds');
        });

        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('charges_id_seq', (select MAX(id) FROM charges))");
        }

        // Fill the charge_defs table
        $insertQuery = <<<SQL
insert into charge_defs(id,name,type,period,amount,lendable,active,guild_id)
    select id,name,type,period,amount,lendable,active,guild_id from charges
SQL;
        DB::statement($insertQuery);

        Schema::table('charges', function(Blueprint $table) {
            $table->unsignedBigInteger('round_id')->nullable();
            $table->foreign('round_id')->references('id')->on('rounds');
            $table->unsignedBigInteger('def_id')->nullable();
            $table->foreign('def_id')->references('id')->on('charge_defs');
            $table->unique(['round_id', 'def_id']);
        });

        Schema::table('charges', function(Blueprint $table) {
            $table->dropColumn(['name', 'type', 'period', 'amount',
                'lendable', 'active', 'guild_id']);
        });

        // Create a temp view
        $createViewQuery = <<<SQL
create view tmp_v_round_charge as
    select distinct rb.charge_id, rb.round_id
        from round_bills rb
    union select distinct sb.charge_id, s.round_id
        from session_bills sb
        inner join sessions s on s.id=sb.session_id
    union select distinct ob.charge_id, min(r.id) as round_id
        from oneoff_bills ob
        inner join members m on m.id=ob.member_id
        inner join rounds r on r.guild_id=m.guild_id
        group by ob.charge_id
    union select distinct lb.charge_id, s.round_id
        from libre_bills lb
        inner join sessions s on s.id=lb.session_id
    union select distinct o.charge_id, s.round_id
        from outflows o
        inner join sessions s on s.id=o.session_id
        where o.charge_id is not null
    union select distinct st.charge_id, s.round_id
        from settlement_targets st
        inner join sessions s on s.id=st.session_id
SQL;
        DB::statement($createViewQuery);

        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('charge_defs_id_seq', (select MAX(id) FROM charge_defs))");
        }

        // Create new entries for charges
        $tables = ['round_bills', 'session_bills', 'oneoff_bills', 'libre_bills',
            'outflows', 'settlement_targets'];
        DB::table('tmp_v_round_charge')
            ->distinct()
            ->cursor()
            ->each(function($rm) use($tables) {
                $chargeId = DB::table('charges')->insertGetId([
                    'round_id' => $rm->round_id,
                    'def_id' => $rm->charge_id
                ]);
                foreach($tables as $table)
                {
                    DB::table($table)
                        ->where(['charge_id' => $rm->charge_id])
                        ->update(['charge_id' => $chargeId]);
                }
            });

        // Delete the obsolete entries
        DB::table('charges')->whereNull('round_id')->delete();

        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('charges_id_seq', (select MAX(id) FROM charges))");
        }

        // Drop the temp view
        $dropViewQuery = <<<SQL
drop view if exists tmp_v_round_charge
SQL;
        DB::statement($dropViewQuery);

        Schema::table('charges', function(Blueprint $table) {
            $table->unsignedBigInteger('round_id')->nullable(false)->change();
            $table->unsignedBigInteger('def_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('charge_defs');
        throw new Exception('Rollback is not allowed on this migration.');
    }
};
