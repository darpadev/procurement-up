<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->string('ref');
            $table->string('name');
            $table->integer('value');
            $table->foreignId('applicant')->constrained('users');
            $table->foreignId('origin')->nullable()->constrained('origins');
            $table->foreignId('unit')->nullable()->constrained('units');
            $table->foreignId('status')->constrained('statuses');
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('procurements');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
