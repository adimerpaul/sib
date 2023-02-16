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
            $table->string('avatar')->nullable()->default('avatar.png');
            $table->string('libro')->nullable();
            $table->string('categoria')->nullable();
            $table->date('renovacion')->nullable();
            $table->string('tipoCodumento')->nullable();
            $table->string('emitido')->nullable();
            $table->string('genero')->nullable();
            $table->date('fechaNac')->nullable();
            $table->string('paginas')->nullable();
            $table->string('nivel')->nullable();
            $table->date('fechaInscripcion')->nullable();
            $table->date('fechaValido')->nullable();
            $table->string('ci')->nullable();
            $table->string('ciAnverso')->nullable();
            $table->string('ciReverso')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('departamento')->nullable();
            $table->string('municipio')->nullable();
            $table->string('barrio')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('web')->nullable();
            $table->string('skype')->nullable();
            $table->string('facebook')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('email')->unique();
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
