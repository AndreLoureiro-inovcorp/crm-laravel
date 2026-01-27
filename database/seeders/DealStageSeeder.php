<?php

namespace Database\Seeders;

use App\Models\DealStage;
use Illuminate\Database\Seeder;

class DealStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            ['name' => 'Lead', 'order' => 1, 'color' => '#3b82f6'],
            ['name' => 'Proposta', 'order' => 2, 'color' => '#f59e0b'],
            ['name' => 'Negociação', 'order' => 3, 'color' => '#8b5cf6'],
            ['name' => 'Ganho', 'order' => 4, 'color' => '#10b981'],
            ['name' => 'Perdido', 'order' => 5, 'color' => '#ef4444'],
            ['name' => 'Follow Up', 'order' => 6, 'color' => '#6366f1'],
        ];

        foreach ($stages as $stage) {
            DealStage::create([
                'tenant_id' => 1, // ID do tenant criado no TenantSeeder
                'name' => $stage['name'],
                'order' => $stage['order'],
                'color' => $stage['color'],
            ]);
        }
    }
}
