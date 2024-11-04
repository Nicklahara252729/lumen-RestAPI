<?php

/**
 * file location
 */

namespace App\Traits;

/**
 * import collection
 */

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * import models
 */

use App\Models\User\User;
use App\Models\Akses\Akses;
use App\Models\Setting\Menu\Menu;

trait JwtResponse
{

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\tokenResponse
     */
    protected function tokenResponse($token = null)
    {
        $user = User::leftJoin("bidang", "bidang.uuid_bidang", "=", "users.uuid_bidang")
            ->leftJoin("sub_bidang", "sub_bidang.uuid_sub_bidang", "=", "users.uuid_sub_bidang")
            ->leftJoin("ref_kecamatan", "ref_kecamatan.KD_KECAMATAN", "=", "users.kd_kecamatan")
            ->leftJoin('ref_kelurahan', function ($join) {
                $join->on('users.kd_kecamatan', '=', 'ref_kelurahan.KD_KECAMATAN')
                    ->on('users.kd_kelurahan', '=', 'ref_kelurahan.KD_KELURAHAN');
            })
            ->select(
                "users.*",
                DB::raw('CONCAT("' . url(path('user')) . '/", CASE WHEN profile_photo_path IS NULL THEN "blank.png" ELSE  profile_photo_path END) AS profile_photo_path'),
                "nama_bidang",
                "nama_sub_bidang",
                DB::raw('IFNULL(ref_kecamatan.NM_KECAMATAN, NULL) AS nm_kecamatan'),
                DB::raw('IFNULL(ref_kelurahan.NM_KELURAHAN, NULL) AS nm_kelurahan'),
            )
            ->where('users.id', auth()->id())
            ->first();
        if ($user->role == 'superadmin' || $user->role == 'admin') :
            $akses = Menu::get();
        else :
            $akses = Akses::select("menus.*")
                ->where(['role' => $user->role, 'uuid_bidang' => $user->uuid_bidang])
                ->join("menus", "akses.uuid_menu", "=", "menus.uuid_menu")
                ->get();
        endif;

        
        $signature = base64_encode($user->uuid_user);
        $return = [
            'token_type'   => 'bearer',
            'signature'    => $signature,
            'user'         => $user,
            'permission'   => $akses,
            'expires_in'   => Auth::factory()->getTTL() * 60 * 24
        ];
        if (!is_null($token)) $return['access_token'] = $token;
        return $return;
    }
}
