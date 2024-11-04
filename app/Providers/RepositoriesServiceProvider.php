<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->authRepositories();
        $this->bidangRepositories();
        $this->tokenRepositories();
        $this->settingRepositories();
        $this->userRepositories();
        $this->layananRepositories();
        $this->pelayananRepositories();
        $this->regionRepositories();
        $this->refrensiRepositories();
        $this->aksesRepositories();
        $this->dashboardRepositories();
        $this->printRepositories();
        $this->pbbMinimalRepositories();
        $this->spptRepositories();
        $this->bankRepositories();
        $this->logRepositories();
        $this->reportRepositories();
        $this->dhkpRepositories();
        $this->publicRepositories();
        $this->jenisPerolehanRepositories();
        $this->masterDataRepositories();
        $this->notarisRepositories();
        $this->tunggakanRepositories();
        $this->tagihanKolektorRepositories();
        $this->skpdkbRepositories();
        $this->petaObjekPajakRepositories();
        $this->patRepositories();
        $this->reklameRepositories();
        $this->kabanRepositories();
        $this->operatorLapanganRepositories();
        $this->pengutipRepositories();
    }

    /**
     * authentication
     */
    public function authRepositories()
    {

        $this->app->bind(
            'App\Repositories\Auth\Login\LoginRepositories',
            'App\Repositories\Auth\Login\EloquentLoginRepositories',
        );
        $this->app->bind(
            'App\Repositories\Auth\Logout\LogoutRepositories',
            'App\Repositories\Auth\Logout\EloquentLogoutRepositories',
        );
        $this->app->bind(
            'App\Repositories\Auth\Register\RegisterRepositories',
            'App\Repositories\Auth\Register\EloquentRegisterRepositories',
        );
    }

    /**
     * bidang & sub bidang
     */
    public function bidangRepositories()
    {
        $this->app->bind(
            'App\Repositories\Bidang\Bidang\BidangRepositories',
            'App\Repositories\Bidang\Bidang\EloquentBidangRepositories',
        );
        $this->app->bind(
            'App\Repositories\Bidang\SubBidang\SubBidangRepositories',
            'App\Repositories\Bidang\SubBidang\EloquentSubBidangRepositories',
        );
    }

    /**
     * token
     */
    public function tokenRepositories()
    {
        $this->app->bind(
            'App\Repositories\Token\TokenRepositories',
            'App\Repositories\Token\EloquentTokenRepositories',
        );
    }

    /**
     * setting
     */
    public function settingRepositories()
    {
        $this->app->bind(
            'App\Repositories\Setting\General\SettingRepositories',
            'App\Repositories\Setting\General\EloquentSettingRepositories',
        );

        $this->app->bind(
            'App\Repositories\Setting\Slider\SliderRepositories',
            'App\Repositories\Setting\Slider\EloquentSliderRepositories',
        );

        $this->app->bind(
            'App\Repositories\Setting\Layanan\LayananRepositories',
            'App\Repositories\Setting\Layanan\EloquentLayananRepositories',
        );

        $this->app->bind(
            'App\Repositories\Setting\Menu\MenuRepositories',
            'App\Repositories\Setting\Menu\EloquentMenuRepositories',
        );
    }

    /**
     * user
     */
    public function userRepositories()
    {
        $this->app->bind(
            'App\Repositories\User\UserRepositories',
            'App\Repositories\User\EloquentUserRepositories',
        );
    }

    /**
     * layanan & jenis layanan
     */
    public function layananRepositories()
    {
        $this->app->bind(
            'App\Repositories\Layanan\Layanan\LayananRepositories',
            'App\Repositories\Layanan\Layanan\EloquentLayananRepositories',
        );
        $this->app->bind(
            'App\Repositories\Layanan\JenisLayanan\JenisLayananRepositories',
            'App\Repositories\Layanan\JenisLayanan\EloquentJenisLayananRepositories',
        );
    }

    /**
     * pelayanan
     */
    public function pelayananRepositories()
    {
        $this->app->bind(
            'App\Repositories\Pelayanan\Bphtb\BphtbRepositories',
            'App\Repositories\Pelayanan\Bphtb\EloquentBphtbRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pelayanan\PelayananRepositories',
            'App\Repositories\Pelayanan\Pelayanan\EloquentPelayananRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\PecahNop\PecahNopRepositories',
            'App\Repositories\Pelayanan\Pbb\PecahNop\EloquentPecahNopRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\GabungNop\GabungNopRepositories',
            'App\Repositories\Pelayanan\Pbb\GabungNop\EloquentGabungNopRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\PerubahanStatusNop\PerubahanStatusNopRepositories',
            'App\Repositories\Pelayanan\Pbb\PerubahanStatusNop\EloquentPerubahanStatusNopRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\Pendaftaran\PendaftaranRepositories',
            'App\Repositories\Pelayanan\Pbb\Pendaftaran\EloquentPendaftaranRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\PetaObjekPajak\PetaObjekPajakRepositories',
            'App\Repositories\Pelayanan\Pbb\PetaObjekPajak\EloquentPetaObjekPajakRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\Mutasi\MutasiRepositories',
            'App\Repositories\Pelayanan\Pbb\Mutasi\EloquentMutasiRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\PembatalanSppt\PembatalanSpptRepositories',
            'App\Repositories\Pelayanan\Pbb\PembatalanSppt\EloquentPembatalanSpptRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\PenetapanSppt\PenetapanSpptRepositories',
            'App\Repositories\Pelayanan\Pbb\PenetapanSppt\EloquentPenetapanSpptRepositories',
        );
        $this->app->bind(
            'App\Repositories\Pelayanan\Pbb\Lspop\LspopRepositories',
            'App\Repositories\Pelayanan\Pbb\Lspop\EloquentLspopRepositories',
        );
    }

    /**
     * region
     */
    public function regionRepositories()
    {
        $this->app->bind(
            'App\Repositories\Region\Provinsi\ProvinsiRepositories',
            'App\Repositories\Region\Provinsi\EloquentProvinsiRepositories',
        );
        $this->app->bind(
            'App\Repositories\Region\Kabupaten\KabupatenRepositories',
            'App\Repositories\Region\Kabupaten\EloquentKabupatenRepositories',
        );
        $this->app->bind(
            'App\Repositories\Region\Kecamatan\KecamatanRepositories',
            'App\Repositories\Region\Kecamatan\EloquentKecamatanRepositories',
        );
        $this->app->bind(
            'App\Repositories\Region\Kelurahan\KelurahanRepositories',
            'App\Repositories\Region\Kelurahan\EloquentKelurahanRepositories',
        );
    }

    /**
     * refrensi
     */
    public function refrensiRepositories()
    {
        $this->app->bind(
            'App\Repositories\Refrensi\Pekerjaan\PekerjaanRepositories',
            'App\Repositories\Refrensi\Pekerjaan\EloquentPekerjaanRepositories',
        );
        $this->app->bind(
            'App\Repositories\Refrensi\Blok\BlokRepositories',
            'App\Repositories\Refrensi\Blok\EloquentBlokRepositories',
        );
        $this->app->bind(
            'App\Repositories\Refrensi\Kecamatan\KecamatanRepositories',
            'App\Repositories\Refrensi\Kecamatan\EloquentKecamatanRepositories',
        );
        $this->app->bind(
            'App\Repositories\Refrensi\Provinsi\ProvinsiRepositories',
            'App\Repositories\Refrensi\Provinsi\EloquentProvinsiRepositories',
        );
        $this->app->bind(
            'App\Repositories\Refrensi\Kelurahan\KelurahanRepositories',
            'App\Repositories\Refrensi\Kelurahan\EloquentKelurahanRepositories',
        );
        $this->app->bind(
            'App\Repositories\Refrensi\KelasBumi\KelasBumiRepositories',
            'App\Repositories\Refrensi\KelasBumi\EloquentKelasBumiRepositories',
        );
        $this->app->bind(
            'App\Repositories\Refrensi\Lspop\Jpb\JpbRepositories',
            'App\Repositories\Refrensi\Lspop\Jpb\EloquentJpbRepositories',
        );
        $this->app->bind(
            'App\Repositories\Refrensi\Lspop\Pekerjaan\PekerjaanRepositories',
            'App\Repositories\Refrensi\Lspop\Pekerjaan\EloquentPekerjaanRepositories',
        );
        $this->app->bind(
            'App\Repositories\Refrensi\JenisPajak\JenisPajakRepositories',
            'App\Repositories\Refrensi\JenisPajak\EloquentJenisPajakRepositories',
        );
    }

    /**
     * akses
     */
    public function aksesRepositories()
    {
        $this->app->bind(
            'App\Repositories\Akses\AksesRepositories',
            'App\Repositories\Akses\EloquentAksesRepositories',
        );
    }

    /**
     * dashboard
     */
    public function dashboardRepositories()
    {
        $this->app->bind(
            'App\Repositories\Dashboard\DashboardRepositories',
            'App\Repositories\Dashboard\EloquentDashboardRepositories',
        );
    }

    /**
     * print
     */
    public function printRepositories()
    {
        $this->app->bind(
            'App\Repositories\Print\PrintRepositories',
            'App\Repositories\Print\EloquentPrintRepositories',
        );
    }

    /**
     * pbb minimal
     */
    public function pbbMinimalRepositories()
    {
        $this->app->bind(
            'App\Repositories\PbbMinimal\PbbMinimalRepositories',
            'App\Repositories\PbbMinimal\EloquentPbbMinimalRepositories',
        );
    }

    /**
     * sppt
     */
    public function spptRepositories()
    {
        $this->app->bind(
            'App\Repositories\Sppt\Sppt\SpptRepositories',
            'App\Repositories\Sppt\Sppt\EloquentSpptRepositories',
        );
        $this->app->bind(
            'App\Repositories\Sppt\PembayaranManual\PembayaranManualRepositories',
            'App\Repositories\Sppt\PembayaranManual\EloquentPembayaranManualRepositories',
        );
        $this->app->bind(
            'App\Repositories\Sppt\PembatalanTransaksi\PembatalanTransaksiRepositories',
            'App\Repositories\Sppt\PembatalanTransaksi\EloquentPembatalanTransaksiRepositories',
        );
        $this->app->bind(
            'App\Repositories\Sppt\PembatalanDenda\PembatalanDendaRepositories',
            'App\Repositories\Sppt\PembatalanDenda\EloquentPembatalanDendaRepositories',
        );
    }

    /**
     * bank
     */
    public function bankRepositories()
    {
        $this->app->bind(
            'App\Repositories\Bank\Briva\BrivaRepositories',
            'App\Repositories\Bank\Briva\EloquentBrivaRepositories',
        );
        $this->app->bind(
            'App\Repositories\Bank\Bpn\BpnRepositories',
            'App\Repositories\Bank\Bpn\EloquentBpnRepositories',
        );
    }

    /**
     * report
     */
    public function reportRepositories()
    {
        $this->app->bind(
            'App\Repositories\Report\Pbb\ReportPbbRepositories',
            'App\Repositories\Report\Pbb\EloquentReportPbbRepositories',
        );
        $this->app->bind(
            'App\Repositories\Report\Bphtb\Notaris\ReportNotarisRepositories',
            'App\Repositories\Report\Bphtb\Notaris\EloquentReportNotarisRepositories',
        );
        $this->app->bind(
            'App\Repositories\Report\Bphtb\Skpdkb\ReportSkpdkbRepositories',
            'App\Repositories\Report\Bphtb\Skpdkb\EloquentReportSkpdkbRepositories',
        );
    }

    /**
     * dhkp
     */
    public function dhkpRepositories()
    {
        $this->app->bind(
            'App\Repositories\Dhkp\DhkpRepositories',
            'App\Repositories\Dhkp\EloquentDhkpRepositories',
        );
    }

    /**
     * log
     */
    public function logRepositories()
    {
        $this->app->bind(
            'App\Repositories\Log\LogRepositories',
            'App\Repositories\Log\EloquentLogRepositories',
        );
    }

    /**
     * public
     */
    public function publicRepositories()
    {
        $this->app->bind(
            'App\Repositories\Public\PublicRepositories',
            'App\Repositories\Public\EloquentPublicRepositories',
        );
    }

    /**
     * jenis perolehan
     */
    public function jenisPerolehanRepositories()
    {
        $this->app->bind(
            'App\Repositories\JenisPerolehan\JenisPerolehanRepositories',
            'App\Repositories\JenisPerolehan\EloquentJenisPerolehanRepositories',
        );
    }

    /**
     * maste data
     */
    public function masterDataRepositories()
    {
        $this->app->bind(
            'App\Repositories\MasterData\Npoptkp\NpoptkpRepositories',
            'App\Repositories\MasterData\Npoptkp\EloquentNpoptkpRepositories',
        );
    }

    /**
     * notaris
     */
    public function notarisRepositories()
    {
        $this->app->bind(
            'App\Repositories\Notaris\NotarisRepositories',
            'App\Repositories\Notaris\EloquentNotarisRepositories',
        );
    }

    /**
     * tunggakan
     */
    public function tunggakanRepositories()
    {
        $this->app->bind(
            'App\Repositories\Tunggakan\TunggakanRepositories',
            'App\Repositories\Tunggakan\EloquentTunggakanRepositories',
        );
    }

    /**
     * tagihan kolektor
     */
    public function tagihanKolektorRepositories()
    {
        $this->app->bind(
            'App\Repositories\TagihanKolektor\TagihanKolektorRepositories',
            'App\Repositories\TagihanKolektor\EloquentTagihanKolektorRepositories',
        );
    }

    /**
     * SKPDKB
     */
    public function skpdkbRepositories()
    {
        $this->app->bind(
            'App\Repositories\Skpdkb\SkpdkbRepositories',
            'App\Repositories\Skpdkb\EloquentSkpdkbRepositories',
        );
    }

    /**
     * peta objek pajak
     */
    public function petaObjekPajakRepositories()
    {
        $this->app->bind(
            'App\Repositories\PetaObjekPajak\PetaObjekPajakRepositories',
            'App\Repositories\PetaObjekPajak\EloquentPetaObjekPajakRepositories',
        );
    }

    /**
     * pat
     */
    public function patRepositories()
    {
        $this->app->bind(
            'App\Repositories\Pat\PatRepositories',
            'App\Repositories\Pat\EloquentPatRepositories',
        );
    }

    /**
     * reklame
     */
    public function reklameRepositories()
    {
        $this->app->bind(
            'App\Repositories\Reklame\ReklameRepositories',
            'App\Repositories\Reklame\EloquentReklameRepositories',
        );
    }

    /**
     * kaban
     */
    public function kabanRepositories()
    {
        $this->app->bind(
            'App\Repositories\Kaban\KabanRepositories',
            'App\Repositories\Kaban\EloquentKabanRepositories',
        );
    }

    /**
     * operator lapangan
     */
    public function operatorLapanganRepositories()
    {
        $this->app->bind(
            'App\Repositories\OperatorLapangan\OperatorLapanganRepositories',
            'App\Repositories\OperatorLapangan\EloquentOperatorLapanganRepositories',
        );
    }

    /**
     * pengutip
     */
    public function pengutipRepositories()
    {
        $this->app->bind(
            'App\Repositories\Pengutip\PengutipRepositories',
            'App\Repositories\Pengutip\EloquentPengutipRepositories',
        );
    }
}
