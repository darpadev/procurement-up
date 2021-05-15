<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement')->constrained('procurements');
            $table->foreignId('vendor')->constrained('vendors');
            $table->foreignId('item')->constrained('items');
            $table->string('name')->nullable();
            $table->string('doc_type')->nullable();
            $table->boolean('winner')->default(0);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE quotations ADD doc MEDIUMBLOB NULL AFTER doc_type");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
