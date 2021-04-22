<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProcurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procurements', function(Blueprint $table){
            $table->foreignId('approver')->constrained('roles');
            $table->foreignId('approver_origin')->constrained('origins');
            $table->foreignId('approver_unit')->nullable()->constrained('units');
            $table->string('approval_status')->nullable();
            $table->foreignId('pic')->nullable()->constrained('users');
            $table->foreignId('category')->nullable()->constrained('proc_categories');
            $table->foreignId('priority')->nullable()->constrained('priorities');
            $table->foreignId('mechanism')->nullable()->constrained('proc_mechanisms');
        });
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
