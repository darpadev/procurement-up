<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateItemsTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::STATEMENT("ALTER TABLE items ADD ref BIGINT(20) UNSIGNED NULL AFTER qty");
        DB::STATEMENT("ALTER TABLE items ADD CONSTRAINT items_quotations_foreign FOREIGN KEY (ref) REFERENCES quotations (id)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
