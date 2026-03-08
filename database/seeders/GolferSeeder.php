<?php

namespace Database\Seeders;

use App\Models\Golfer;
use Illuminate\Database\Seeder;

class GolferSeeder extends Seeder
{
    public function run(): void
    {
    // Continue debitor_account numbering from the current maximum value in the database.
    $lastDebitorAccount = Golfer::max('debitor_account') ?? 999;

    for ($i = 1; $i <= 100; $i++) {

        Golfer::factory()->create([
            'debitor_account' => $lastDebitorAccount + $i
        ]);

    }
    }
}
