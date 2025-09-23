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
        try
        {
            // Fix columns that was not deleted in a previous migration.
            Schema::table('charges', function(Blueprint $table) {
                $table->dropColumn(['name', 'type', 'period', 'amount', 'lendable']);
            });
        }
        catch(Exception)
        {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
