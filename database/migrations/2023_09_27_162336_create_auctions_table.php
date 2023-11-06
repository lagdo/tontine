<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Loan;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->boolean('paid');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('remitment_id');
            $table->foreign('remitment_id')->references('id')->on('remitments');
            $table->unique(['remitment_id']);
        });

        // Copy fields from the loans, debts and refunds tables.
        $loans = Loan::with(['debts', 'debts.refund', 'remitment.payable'])
            ->whereNotNull('remitment_id')->get();
        foreach($loans as $loan)
        {
            if($loan->interest_debt !== null)
            {
                DB::table('auctions')->insert([
                    'amount' => $loan->interest_debt->amount,
                    'paid' => $loan->interest_debt->refund !== null,
                    'remitment_id' => $loan->remitment_id,
                    'session_id' => $loan->interest_debt->refund !== null ?
                        $loan->interest_debt->refund->session_id :
                        $loan->remitment->payable->session_id,
                ]);
            }
        }

        // Delete the lines
        $loanIds = $loans->pluck('id');
        $debtIds = DB::table('debts')->whereIn('loan_id', $loanIds)->pluck('id');
        DB::table('refunds')->whereIn('debt_id', $debtIds)->delete();
        DB::table('debts')->whereIn('id', $debtIds)->delete();
        DB::table('loans')->whereIn('id', $loanIds)->delete();

        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['remitment_id']);
            $table->dropColumn('remitment_id');
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
            $table->unsignedBigInteger('remitment_id')->nullable();
            $table->foreign('remitment_id')->references('id')->on('remitments');
            $table->unique(['remitment_id']);
        });

        // Copy fields to the loans, debts and refunds tables.
        $auctions = Auction::with(['remitment.payable.subscription'])->get();
        foreach($auctions as $auction)
        {
            DB::transaction(function() use($auction) {
                // Create the corresponding loan, debt and refund.
                $loanId = DB::table('loans')->insertGetId([
                    'member_id' => $auction->remitment->payable->subscription->member_id,
                    'session_id' => $auction->remitment->payable->session_id,
                    'remitment_id' => $auction->remitment->id,
                ]);
                $debtId = DB::table('debts')->insertGetId([
                    'type' => Debt::TYPE_INTEREST,
                    'amount' => $auction->amount,
                    'loan_id' => $loanId,
                ]);
                if($auction->paid)
                {
                    DB::table('refunds')->insert([
                        'session_id' => $auction->session_id,
                        'debt_id' => $debtId,
                    ]);
                }
            });
        }

        Schema::dropIfExists('auctions');
    }
};
