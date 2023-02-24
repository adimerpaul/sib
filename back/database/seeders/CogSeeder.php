<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cogs')->insert([
            ['name' => 'nombre', 'value' => 'SOCIEDAD DE INGENIEROS ORURO'],
            ['name' => 'direccion', 'value' => 'C. Belzu No. 650 Entre Vázquez y La Paz'],
            ['name' => 'telefono', 'value' => '5247222'],
            ['name' => 'email', 'value' => 'https://www.siboruro.org']
        ]);
    }
}
