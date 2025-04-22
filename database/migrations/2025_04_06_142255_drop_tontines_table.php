<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $refTables = [
        'charges' => false,
        'members' => false,
        'rounds' => false,
        'categories' => true,
        'funds' => false,
        'guest_tontine' => false,
    ];

    /**
     * @param string $source
     * @param string $target
     *
     * @return void
     */
    private function copyData(string $source, string $target): void
    {
        $fields = 'id,name,shortname,biography,email,phone,address,city,website,' .
            'country_code,currency_code,created_at,updated_at,user_id';
        DB::statement("INSERT INTO $target($fields) SELECT $fields FROM $source ORDER BY id ASC");
    }

    /**
     * @param string $prev
     * @param string $next
     *
     * @return void
     */
    private function setForeignKeys(string $prev, string $next): void
    {
        foreach($this->refTables as $tableName => $nullable)
        {
            Schema::table($tableName, function(Blueprint $table) use($next) {
                $table->unsignedBigInteger("{$next}_id")->nullable();
            });

            // Copy the data from the prev field to the next.
            DB::statement("update $tableName set {$next}_id={$prev}_id");

            Schema::table($tableName, function(Blueprint $table) use($nullable, $next) {
                if(!$nullable)
                {
                    $table->unsignedBigInteger("{$next}_id")->nullable(false)->change();
                }
                $table->foreign("{$next}_id")->references('id')->on("{$next}s")->change();
            });
        }
    }

    /**
     * @param string $column
     *
     * @return void
     */
    private function dropForeignKeys(string $column): void
    {
        foreach($this->refTables as $tableName => $_)
        {
            Schema::table($tableName, function(Blueprint $table) use($column) {
                $table->dropColumn($column);
            });
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dropForeignKeys('tontine_id');

        Schema::dropIfExists('tontines');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The type field is ignored
        Schema::create('tontines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('shortname', 25);
            $table->text('biography')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('country_code', 2);
            $table->string('currency_code', 3);
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });

        $this->copyData('guilds', 'tontines');
        $this->setForeignKeys('guild', 'tontine');
    }
};
