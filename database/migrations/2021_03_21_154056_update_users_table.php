<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table){
            $table->foreignId('role')->constrained('roles');
            $table->foreignId('origin')->constrained('origins');
            $table->foreignId('unit')->nullable()->constrained('units');
        });

        DB::table('users')->insert([
            ['name' => "Staf Fungsi Pengadaan Barang dan Jasa", 'email' => "staf@up.ac.id", 'role' => 7, 'origin' => 2, 'unit' => 4, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Manajer Fungsi Pengadaan Barang dan Jasa", 'email' => "manajerprocurement@up.ac.id", 'role' => 6, 'origin' => 2, 'unit' => 4, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Kaprodi Ilmu Komputer", 'email' => "kaprodics@up.ac.id", 'role' => 5, 'origin' => 5, 'unit' => 10, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Kaprodi Kimia", 'email' => "kaprodich@up.ac.id", 'role' => 5, 'origin' => 5, 'unit' => 9, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Dekan FSK", 'email' => "dekan@up.ac.id", 'role' => 4, 'origin' => 5, 'unit' => NULL, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Direktur Pengelola Fasilitas Universitas", 'email' => "direkturpfu@up.ac.id", 'role' => 3, 'origin' => 2, 'unit' => NULL, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Direktur Lainnya", 'email' => "direkturlain@up.ac.id", 'role' => 3, 'origin' => 3, 'unit' => NULL, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Wakil Rektor 1", 'email' => "wr1@up.ac.id", 'role' => 2, 'origin' => 1, 'unit' => 1, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Wakil Rektor 2", 'email' => "wr2@up.ac.id", 'role' => 2, 'origin' => 1, 'unit' => 2, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => "Wakil Rektor 3", 'email' => "wr3@up.ac.id", 'role' => 2, 'origin' => 1, 'unit' => 3, 'password' => bcrypt("admin"), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);
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
