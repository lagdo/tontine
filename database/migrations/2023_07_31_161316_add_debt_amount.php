<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->integer('amount')->default(0);
        });

        // Copy data from the current field to the new one
        DB::statement("update debts set amount=(select amount from loans where id=debts.loan_id) where type='p'");
        DB::statement("update debts set amount=(select interest from loans where id=debts.loan_id) where type='i'");

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('interest');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->integer('amount')->default(0); // Will be set to 0 for a remitment loan.
            $table->integer('interest')->default(0);
        });

        // Copy data from the current field to the old one
        DB::statement("update loans set amount=(select amount from debts where loan_id=loans.id and type='p') " .
            "where exists(select * from debts where loan_id=loans.id and type='p')");
        DB::statement("update loans set interest=(select amount from debts where loan_id=loans.id and type='i') " .
            "where exists(select * from debts where loan_id=loans.id and type='i')");

        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
