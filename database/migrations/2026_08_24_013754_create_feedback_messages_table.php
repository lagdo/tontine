<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Delete the previous version (kustomer) table.
        Schema::dropIfExists('feedbacks');

        Schema::create(config('volet.feedback-messages.table'), function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('status')->default('new'); // new, read, resolve
            $table->string('category');
            $table->json('user_info')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('volet.feedback-messages.table'));

        // Restore the previous version (kustomer) table.
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->text('message');
            $table->text('user_info')->nullable();
            $table->boolean('reviewed')->default(false);
            $table->timestamps();

            $table->index('type');
        });
    }
};
