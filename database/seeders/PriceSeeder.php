<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        Price::create([
            'stripe_price_id' => 'price_H5ggYwtDq4fbrJ',
            'name' => 'Consulta Estándar',
            'amount' => 50.00,
            'description' => 'Consulta médica estándar de 30 minutos'
        ]);
    }
}