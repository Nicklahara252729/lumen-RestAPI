<?php

namespace Database\Seeders;

/**
 * import component
 */

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

/**
 * import models
 */

use App\Models\User\User;
use App\Models\Bidang\Bidang\Bidang;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker      = Faker::create();
        $role       = [
            'superadmin',
            'admin',
            'kabid pbb',
            'kasubbid pbb',
            'kabid bphtb',
            'kasubbid bphtb',
            'operator',
            'kecamatan',
            'kelurahan',
            'notaris',
            'umum',
            'petugas lapangan',
            'kaban'
        ];
        $dataBidang = Bidang::all();
        for ($i = 0; $i < sizeof($role); $i++) {
            $uuidBidang = $role[$i] == 'superadmin' || $role[$i] == 'admin' || $role[$i] == 'kecamatan'  || $role[$i] == 'kelurahan' || $role[$i]  == 'notaris' || $role[$i] == 'umum' || $role[$i] == 'kaban' ?  null : $dataBidang[$i]->uuid_bidang;
            $kdKecamatan = $role[$i] == 'kecamatan'  || $role[$i] == 'kelurahan' ?  '010' : null;
            $kdKelurahan = $role[$i] == 'kelurahan' ?  '001' : null;
            User::create([
                'uuid_bidang' => $uuidBidang,
                'name'        => $faker->name,
                'email'       => $faker->email,
                'password'    => Hash::make("password"),
                'role'        => $role[$i],
                'username'    => $faker->username,
                'nip'         => rand(),
                'no_hp'       => rand() . rand(0, 100),
                'kd_kecamatan' => $kdKecamatan,
                'kd_kelurahan' => $kdKelurahan
            ]);
        }
    }
}
