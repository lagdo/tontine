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
            // Delete the duplicated columns
            $table->dropColumn(['name', 'type', 'period', 'amount',
                'lendable', 'active', 'guild_id']);
        });

        // Create a temp view
        $createViewQuery = <<<SQL
create view tmp_v_round_charge as
    select distinct o.charge_id, s.round_id
        from outflows o
        inner join sessions s on s.id = o.session_id
        where o.charge_id is not null
    union select distinct st.charge_id, s.round_id
        from settlement_targets st
        inner join sessions s on s.id = st.session_id
    union select distinct lb.charge_id, s.round_id
        from libre_bills lb
        inner join sessions s on s.id = lb.session_id
    union select distinct sb.charge_id, s.round_id
        from session_bills sb
        inner join sessions s on s.id = sb.session_id
    union select distinct rb.charge_id, rb.round_id
        from round_bills rb
    union select distinct ob.charge_id, min(r.id) as round_id
        from oneoff_bills ob
        inner join charge_defs cd on cd.id = ob.charge_id
        inner join rounds r on r.guild_id = cd.guild_id
        group by ob.charge_id
SQL;
        DB::statement($createViewQuery);

        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('charge_defs_id_seq', (select MAX(id) FROM charge_defs))");
        }

        // Add new entries to the charges table
        $insertQuery = <<<SQL
insert into charges(round_id,def_id)
    select distinct round_id,charge_id from tmp_v_round_charge
SQL;
        DB::statement($insertQuery);

        // Drop the temp view
        $dropViewQuery = <<<SQL
drop view if exists tmp_v_round_charge
SQL;
        DB::statement($dropViewQuery);

        // Update the foreign keys with the new charges ids
        $updateQuery = <<<SQL
update outflows ou set charge_id = (select id from charges c
    where c.def_id = ou.charge_id and c.round_id is not null and
        c.round_id = (select round_id from sessions se where se.id = ou.session_id))
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update settlement_targets st set charge_id = (select id from charges c
    where c.def_id = st.charge_id and c.round_id is not null and
        c.round_id = (select round_id from sessions se where se.id = st.session_id))
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update libre_bills lb set charge_id = (select id from charges c
    where c.def_id = lb.charge_id and c.round_id is not null and
        c.round_id = (select round_id from sessions se where se.id = lb.session_id))
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update session_bills sb set charge_id = (select id from charges c
    where c.def_id = sb.charge_id and c.round_id is not null and
        c.round_id = (select round_id from sessions se where se.id = sb.session_id))
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update round_bills rb set charge_id = (select id from charges c
    where c.def_id = rb.charge_id and c.round_id is not null and c.round_id = rb.round_id)
SQL;
        DB::statement($updateQuery);
        $updateQuery = <<<SQL
update oneoff_bills ob set charge_id = (select id from charges c
    where c.def_id = ob.charge_id and c.round_id is not null and
        c.round_id = (select min(r.id) from rounds r where r.guild_id =
            (select cd.guild_id from charge_defs cd where cd.id = c.def_id)))
SQL;
        DB::statement($updateQuery);

        // Delete the obsolete entries
        DB::table('charges')->whereNull('round_id')->delete();

        if(DB::getDriverName() === 'pgsql')
        {
            DB::statement("select setval('charges_id_seq', (select MAX(id) FROM charges))");
        }

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
