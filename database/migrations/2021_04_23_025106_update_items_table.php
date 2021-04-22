<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE items ADD category BIGINT(20) UNSIGNED NULL AFTER procurement');
        DB::statement('ALTER TABLE items ADD CONSTRAINT items_category_foreign FOREIGN KEY (category) REFERENCES item_categories(id)');
        DB::statement('ALTER TABLE items ADD sub_category BIGINT(20) UNSIGNED NULL AFTER category');
        DB::statement('ALTER TABLE items ADD CONSTRAINT items_sub_category_foreign FOREIGN KEY (sub_category) REFERENCES item_sub_categories(id)');
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
