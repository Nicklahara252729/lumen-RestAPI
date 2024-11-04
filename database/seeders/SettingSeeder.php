<?php

namespace Database\Seeders;

/**
 * import component
 */

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * import models
 */

use App\Models\Setting\Setting\Setting;

/**
 * import traits
 */

use App\Traits\Cipher;

class SettingSeeder extends Seeder
{
    use Cipher;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'category' => 'copyright',
                'description' => 'Copyright © 2023',
            ],
            [
                'category' => 'logo',
                'description' => 'logo.png',
            ],
            [
                'category' => 'application name',
                'description' => 'E-PBB Pemkot',
            ],
            [
                'category' => 'shared ip',
                'description' => null,
            ],
            [
                'category' => 'printer name',
                'description' => null,
            ],
            [
                'category' => 'folder print',
                'c' => null,
            ],
            [
                'category' => 'whatsapp notif',
                'description' => 'enabled',
            ],
            [
                'category' => 'whatsapp key',
                'description' => 'ui3VK8IaDIB4BNMBEGTU',
            ]
        ];

        foreach ($data as $value) :

            $desc = $value['category'] == 'whatsapp key' ? $this->encipher($value['description'], env('CIPHER_KEY')) : $value['description'];
            Setting::create([
                'category' => $value['category'],
                'description' => $desc,
            ]);
        endforeach;
    }
}
