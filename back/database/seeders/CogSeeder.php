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
            ['name' => 'nombre', 'value' => ' Sociedad de Ingenieros de Bolivia'],
            ['name' => 'direccion', 'value' => 'Av. 6 de Agosto Nro. 1000'],
            ['name' => 'telefono', 'value' => '2-222-222'],
            ['name' => 'email', 'value' => 'https://www.sib.org.bo/']
        ]);
    }
}
