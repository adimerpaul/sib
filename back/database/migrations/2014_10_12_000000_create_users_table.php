<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rni')->nullable();
            $table->string('paterno')->nullable();
            $table->string('materno')->nullable();
            $table->string('nombres')->nullable();
            $table->date('fechaNac')->nullable();
            $table->string('departamento')->nullable();
            $table->string('nacimiento')->nullable();
            $table->string('sexo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('zona')->nullable();
            $table->string('telefono')->nullable();
            $table->string('oficina')->nullable();
            $table->string('celular')->nullable();
            $table->string('especialidad')->nullable();
            $table->date('fechaDiploma')->nullable();
            $table->string('universidad')->nullable();
            $table->string('status')->nullable()->default('activo');
            $table->string('avatar')->nullable()->default('avatar.png');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('email')->unique();
            $table->string('type')->default('usuario');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
