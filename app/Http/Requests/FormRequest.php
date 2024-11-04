<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class FormRequest
{
    use ProvidesConvenienceMethods;

    public Request $req;
    protected $request_method;
    protected $id;
    protected $uuid_bidang;
    protected $uuid_menu;
    protected $role;
    protected $KD_KECAMATAN;
    protected $KD_KELURAHAN;
    protected $NO_URUT;
    protected $no_urut_awal;
    protected $no_urut_akhir;
    protected $THN_PBB_MINIMAL;
    protected $pelayanan;
    protected $current_password;
    protected $tahun;
    protected $tahun_pajak_awal;
    protected $tahun_pajak_gabung;
    protected $jenis_pajak;
    protected $kecamatan;
    protected $kelurahan;

    public function __construct(Request $request, array $customAttributes = [])
    {
        $this->req = $request;

        $this->prepareForValidation();

        if (!$this->authorize()) throw new UnauthorizedException;

        /**
         * set id for update
         */
        $segments = $request->segments();
        $this->id = end($segments);

        /**
         * set form input
         */
        $this->uuid_bidang = $this->req->uuid_bidang;
        $this->uuid_menu = $this->req->uuid_menu;
        $this->role = $this->req->role;
        $this->KD_KECAMATAN = $this->req->KD_KECAMATAN;
        $this->KD_KELURAHAN = $this->req->KD_KELURAHAN;
        $this->NO_URUT = $this->req->NO_URUT;
        $this->THN_PBB_MINIMAL = $this->req->THN_PBB_MINIMAL;
        $this->pelayanan = $this->req->pelayanan;
        $this->no_urut_awal = $this->req->no_urut_awal;
        $this->no_urut_akhir = $this->req->no_urut_akhir;
        $this->current_password = $this->req->current_password;
        $this->tahun = $this->req->tahun;
        $this->tahun_pajak_awal = $this->req->tahun_pajak_awal;
        $this->tahun_pajak_gabung = $this->req->tahun_pajak_gabung;
        $this->jenis_pajak = $this->req->jenis_pajak;
        $this->kecamatan = $this->req->kecamatan;
        $this->kelurahan = $this->req->kelurahan;

        $this->validate($this->req, $this->rules(), $this->messages(), $customAttributes);
    }

    public function all()
    {
        return $this->req->except(['/' . $this->req->path(),'_method']);
    }

    public function get(string $key, $default = null)
    {
        return $this->req->get($key, $default);
    }

    protected function prepareForValidation()
    {
        //
    }

    protected function authorize()
    {
        return true;
    }

    protected function rules()
    {
        return [];
    }

    protected function messages()
    {
        return [];
    }
}
