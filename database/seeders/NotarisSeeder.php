<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * import models
 */

 use App\Models\User\User;

class NotarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataNotaris = [
            [
              "KODE" => "001",
              "ALAMAT" => "-",
              "KONTAK_PERSON" => "-",
              "KOTA" => "BINJAI",
              "NAMA" => "KHAIRUNISA, SH",
              "PASSWORD" => "1998",
              "TELEPON" => "-"
            ],[
              "KODE" => "002",
              "ALAMAT" => "JL.JEND.GATOT SUBROTO NO.365",
              "KONTAK_PERSON" => "085297235992",
              "KOTA" => "BINJAI",
              "NAMA" => "RASMI, SH",
              "PASSWORD" => "000",
              "TELEPON" => "085297235992"
            ],[
              "KODE" => "003",
              "ALAMAT" => "-",
              "KONTAK_PERSON" => "-",
              "KOTA" => "BINJAI",
              "NAMA" => "EVI FITRIANI, S.Psi, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "-"
            ],[
              "KODE" => "004",
              "ALAMAT" => "-",
              "KONTAK_PERSON" => "-",
              "KOTA" => "BINJAI",
              "NAMA" => "JULITA BR.SAGALA, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "-"
            ],[
              "KODE" => "005",
              "ALAMAT" => "-",
              "KONTAK_PERSON" => "-",
              "KOTA" => "BINJAI",
              "NAMA" => "MUCHAIRANI, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "-"
            ],[
              "KODE" => "006",
              "ALAMAT" => "JL.KUIL NO.22",
              "KONTAK_PERSON" => "081376464671",
              "KOTA" => "BINJAI",
              "NAMA" => "EKA FIRMAN JAYA, SH, M.Kn",
              "PASSWORD" => "3k4jaya",
              "TELEPON" => "081376464671"
            ],[
              "KODE" => "007",
              "ALAMAT" => "JL.P.DIPONEGORO NO.10-A",
              "KONTAK_PERSON" => "08126050953",
              "KOTA" => "BINJAI",
              "NAMA" => "ERIKA MIANNA HUTAGAOL, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "061-8829802"
            ],[
              "KODE" => "008",
              "ALAMAT" => "JL.SOEKARNO HATTA NO.26",
              "KONTAK_PERSON" => "082369067777",
              "KOTA" => "BINJAI",
              "NAMA" => "YUSNIAMAN HAREFA, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081361263626"
            ],[
              "KODE" => "009",
              "ALAMAT" => "JL.SOEKARNO HATTA NO.42",
              "KONTAK_PERSON" => "081376149385",
              "KOTA" => "BINJAI",
              "NAMA" => "HERLINA GINTING, SH",
              "PASSWORD" => "123",
              "TELEPON" => "-"
            ],[
              "KODE" => "010",
              "ALAMAT" => "JL.SOEKARNO HATTA NO.322 KM.18",
              "KONTAK_PERSON" => "082162111007",
              "KOTA" => "BINJAI",
              "NAMA" => "SJAFEI SAMSUDDIN, SH, M.Kn",
              "PASSWORD" => "672689ssSS",
              "TELEPON" => "082162111007"
            ],[
              "KODE" => "011",
              "ALAMAT" => "JL.AHMAD YANI NO.5 KEL.KARTINI KEC.BINJAI KOTA",
              "KONTAK_PERSON" => "081269290605",
              "KOTA" => "BINJAI",
              "NAMA" => "IKA AMALIA SYAFITRY LUBIS, SH, M.Kn",
              "PASSWORD" => "1976",
              "TELEPON" => "081377052876"
            ],[
              "KODE" => "012",
              "ALAMAT" => "JL.TAMTAMA NO.6-C, BINJAI KOTA",
              "KONTAK_PERSON" => "085277055823",
              "KOTA" => "BINJAI",
              "NAMA" => "EVI MONITA BR.SINURAYA, SH",
              "PASSWORD" => "ppat2008",
              "TELEPON" => "061-8827485"
            ],[
              "KODE" => "013",
              "ALAMAT" => "JL.JAMIN GINTING",
              "KONTAK_PERSON" => "082272895343",
              "KOTA" => "BINJAI",
              "NAMA" => "FIRLY MUTIA, SH, M.Kn",
              "PASSWORD" => "753",
              "TELEPON" => "085257700668"
            ],[
              "KODE" => "014",
              "ALAMAT" => "JL.T.AMIR HAMZAH",
              "KONTAK_PERSON" => "082368261499",
              "KOTA" => "BINJAI",
              "NAMA" => "DESI PURNAMASARI NAINGGOLAN, SH, M.Kn",
              "PASSWORD" => "450889ng",
              "TELEPON" => "082273099398"
            ],[
              "KODE" => "015",
              "ALAMAT" => "JL.KARTINI NO.57",
              "KONTAK_PERSON" => "082121808537",
              "KOTA" => "BINJAI",
              "NAMA" => "TAUFIKA HIDAYATI, SH,S.Pd, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081265061730"
            ],[
              "KODE" => "016",
              "ALAMAT" => "JL.JEND SUDIRMAN NO.91",
              "KONTAK_PERSON" => "08196084385",
              "KOTA" => "BINJAI",
              "NAMA" => "HENDRIK TANJAYA, SH, M.Kn",
              "PASSWORD" => "805588",
              "TELEPON" => "082362883038"
            ],[
              "KODE" => "017",
              "ALAMAT" => "JL.IR.H.JUANDA NO.182",
              "KONTAK_PERSON" => "085372944200",
              "KOTA" => "BINJAI",
              "NAMA" => "DEVI KUMALA, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "0852666555"
            ],[
              "KODE" => "018",
              "ALAMAT" => "JL.PALEMBANG NO.54",
              "KONTAK_PERSON" => "085262654350",
              "KOTA" => "BINJAI",
              "NAMA" => "TENGKU MARWIATI OKTAVIANI HAMID, SH, M.Kn",
              "PASSWORD" => "160107",
              "TELEPON" => "081370289168"
            ],[
              "KODE" => "019",
              "ALAMAT" => "JL.DAHLIA NO.2-B KEL.PAHLAWAN",
              "KONTAK_PERSON" => "081269684056",
              "KOTA" => "BINJAI",
              "NAMA" => "SYAFVAN RIZKI, SH, M.Kn",
              "PASSWORD" => "kikigabe22",
              "TELEPON" => "082276095464"
            ],[
              "KODE" => "020",
              "ALAMAT" => "JL.SUTOMO NO.35",
              "KONTAK_PERSON" => "081376299954",
              "KOTA" => "BINJAI",
              "NAMA" => "TEDDY TAUFIK, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "061-8826573"
            ],[
              "KODE" => "021",
              "ALAMAT" => "JL.RA.KARTINI NO.10-12 KEL.KARTINI",
              "KONTAK_PERSON" => "082363333913",
              "KOTA" => "BINJAI",
              "NAMA" => "INTES NURLIANA, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "0811618431"
            ],[
              "KODE" => "022",
              "ALAMAT" => "JL.CENDRAWASIH NO.21-A",
              "KONTAK_PERSON" => "081397342336",
              "KOTA" => "BINJAI",
              "NAMA" => "HALIMAH, SH",
              "PASSWORD" => "cendrawasih",
              "TELEPON" => "0812631460553"
            ],[
              "KODE" => "023",
              "ALAMAT" => "JL.SOEKARNO HATTA KM.18",
              "KONTAK_PERSON" => "087819218261",
              "KOTA" => "BINJAI",
              "NAMA" => "MIRANTY, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "08116000660"
            ],[
              "KODE" => "024",
              "ALAMAT" => "JL.P.DIPONEGORO GG.DIPO LK.V",
              "KONTAK_PERSON" => "-",
              "KOTA" => "BINJAI",
              "NAMA" => "DWIKA ADHIANI, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "085296359987"
            ],[
              "KODE" => "025",
              "ALAMAT" => "JL.HASANUDDIN ",
              "KONTAK_PERSON" => "082273299077",
              "KOTA" => "BINJAI",
              "NAMA" => "SRI AYU UTAMI, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "085297349479"
            ],[
              "KODE" => "026",
              "ALAMAT" => "JL.SOEKARNO HATTA NO.32",
              "KONTAK_PERSON" => "081370480487",
              "KOTA" => "BINJAI",
              "NAMA" => "ROTUA HOTMAULI S, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "085359999313"
            ],[
              "KODE" => "027",
              "ALAMAT" => "JL.JEND SUDIRMAN NO.18",
              "KONTAK_PERSON" => "082166206868",
              "KOTA" => "BINJAI",
              "NAMA" => "RABITHAH KHAIRUL , SH, M.Kn",
              "PASSWORD" => "manjaddawajada",
              "TELEPON" => "085371749262"
            ],[
              "KODE" => "028",
              "ALAMAT" => "JL.AR.HAKIM NO.46",
              "KONTAK_PERSON" => "081362300678",
              "KOTA" => "BINJAI",
              "NAMA" => "EVA SARI HUTAJULU, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081375491548"
            ],[
              "KODE" => "029",
              "ALAMAT" => "JL.SIBOLGA NO.24",
              "KONTAK_PERSON" => "0819880199",
              "KOTA" => "BINJAI",
              "NAMA" => "YOSEF AGUSTINUS, SH, SpN",
              "PASSWORD" => "121212notaris",
              "TELEPON" => "0819880199"
            ],[
              "KODE" => "030",
              "ALAMAT" => "JL.JAMIN GINTING",
              "KONTAK_PERSON" => "085372891978",
              "KOTA" => "BINJAI",
              "NAMA" => "REZA ZURIANSYAH, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "085372891978"
            ],[
              "KODE" => "031",
              "ALAMAT" => "JL.T.IMAM BONJOL NO.53",
              "KONTAK_PERSON" => "081377132085",
              "KOTA" => "BINJAI",
              "NAMA" => "NURHABIBAH KEMALA PUTRI, SH, M.Kn",
              "PASSWORD" => "nkputri93",
              "TELEPON" => "081377132085"
            ],[
              "KODE" => "032",
              "ALAMAT" => "JL.T.IMAM BONJOL NO.53",
              "KONTAK_PERSON" => "081562191143",
              "KOTA" => "BINJAI",
              "NAMA" => "FITRI TRISNASARI NASUTION, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081562191143"
            ],[
              "KODE" => "033",
              "ALAMAT" => "JL.T.AMIR HAMZAH NO.356-E",
              "KONTAK_PERSON" => "081375839889",
              "KOTA" => "BINJAI",
              "NAMA" => "HERLINA E.NAPITUPULU, S.Pt, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081361301234"
            ],[
              "KODE" => "034",
              "ALAMAT" => "JL.TUAN IMAM NO.1-S",
              "KONTAK_PERSON" => "085761763389",
              "KOTA" => "BINJAI",
              "NAMA" => "YENNY, SH, M.Kn",
              "PASSWORD" => "unchallenged",
              "TELEPON" => "082267370868"
            ],[
              "KODE" => "035",
              "ALAMAT" => "JL.PERINTIS KEMERDEKAAN GG.LANGSAT NO.137-B",
              "KONTAK_PERSON" => "082365560381",
              "KOTA" => "BINJAI",
              "NAMA" => "NURMILYS BR GINTING, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081269464448"
            ],[
              "KODE" => "036",
              "ALAMAT" => "JL.T.AMIR HAMZAH GG.RODA NO.2",
              "KONTAK_PERSON" => "08126330959",
              "KOTA" => "BINJAI",
              "NAMA" => "SITI SYARIFAH, SH",
              "PASSWORD" => "123",
              "TELEPON" => "081265142727"
            ],[
              "KODE" => "037",
              "ALAMAT" => "JL.SAMANHUDI NO.83",
              "KONTAK_PERSON" => "081361099946",
              "KOTA" => "BINJAI",
              "NAMA" => "ELFRIDA DWI ROSA SITINDAON, SH, M.Kn",
              "PASSWORD" => "akucantik210987",
              "TELEPON" => "081361099946"
            ],[
              "KODE" => "038",
              "ALAMAT" => "JL.CUT NYAK DHIEN NO.16",
              "KONTAK_PERSON" => "081361185123",
              "KOTA" => "BINJAI",
              "NAMA" => "SYARIFAH NADIRA, SH, M.Kn",
              "PASSWORD" => "tomangelok",
              "TELEPON" => "081361185123"
            ],[
              "KODE" => "039",
              "ALAMAT" => "JL.T.AMIR HAMZAH NO.105",
              "KONTAK_PERSON" => "087890745625",
              "KOTA" => "BINJAI",
              "NAMA" => "SITTI HAWA, SH, M.Kn",
              "PASSWORD" => "hazura",
              "TELEPON" => "081392008655"
            ],[
              "KODE" => "040",
              "ALAMAT" => "JL.SOEKARNO HATTA KM.18,5 KOMP RUKO PURI KURNIA NO.5",
              "KONTAK_PERSON" => "-",
              "KOTA" => "BINJAI",
              "NAMA" => "REZA FAHMI, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "-"
            ],[
              "KODE" => "041",
              "ALAMAT" => "JL.JAMIN GINTING TANAH SERIBU NO.376",
              "KONTAK_PERSON" => "081385964553",
              "KOTA" => "BINJAI",
              "NAMA" => "YUDHISTIRA CRIESA ZEFANI TARIGAN, SH, M.Kn",
              "PASSWORD" => "notarisYT",
              "TELEPON" => "085362261490"
            ],[
              "KODE" => "042",
              "ALAMAT" => "JL.SOEKRANO HATTA NO.89",
              "KONTAK_PERSON" => "082374515219",
              "KOTA" => "BINJAI",
              "NAMA" => "NATAL RIA ARGENTINA BR SURBAKTI, SH, M.Kn",
              "PASSWORD" => "2018.PPAT",
              "TELEPON" => "085296646000"
            ],[
              "KODE" => "043",
              "ALAMAT" => "JL.SULTAN HASANUDDIN NO.77",
              "KONTAK_PERSON" => "085359185541",
              "KOTA" => "BINJAI",
              "NAMA" => "MARTINA JULIANCE P.B SIHOMBING, SH, M.Kn",
              "PASSWORD" => "1258",
              "TELEPON" => "082164991325"
            ],[
              "KODE" => "044",
              "ALAMAT" => "JL.SOEKARNO HATTA NO.237 KM.20",
              "KONTAK_PERSON" => "081361652750",
              "KOTA" => "BINJAI",
              "NAMA" => "AFNIDA NOVRIANY, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081361652750"
            ],[
              "KODE" => "045",
              "ALAMAT" => "JL.PANGERAN DIPONEGORO NO.58-G",
              "KONTAK_PERSON" => "081361000845",
              "KOTA" => "BINJAI",
              "NAMA" => "HUMAIRA RIDANTY, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081361000845"
            ],[
              "KODE" => "046",
              "ALAMAT" => "KM.17,5 KEL.TUNGGURONO KEC.BINJAI TIMUR",
              "KONTAK_PERSON" => "082166349611",
              "KOTA" => "BINJAI",
              "NAMA" => "YUSRIANSYAH RAMADHAN, SH, M.Kn",
              "PASSWORD" => "330040",
              "TELEPON" => "082166349611"
            ],[
              "KODE" => "047",
              "ALAMAT" => "JL.COKLAT NO.86",
              "KONTAK_PERSON" => "082274048150",
              "KOTA" => "BINJAI",
              "NAMA" => "ASTARI PRIARDHYNI, SH, M.Kn",
              "PASSWORD" => "astari",
              "TELEPON" => "081362606211"
            ],[
              "KODE" => "048",
              "ALAMAT" => "JL.JEND.AHMAD YANI GG.ANGGREK NO.8-B",
              "KONTAK_PERSON" => "082276425933",
              "KOTA" => "BINJAI",
              "NAMA" => "SABRINA ALICE TUTUPOLY, SH, M.Kn",
              "PASSWORD" => "333",
              "TELEPON" => "082276425933"
            ],[
              "KODE" => "049",
              "ALAMAT" => "JL.GATOT SUBROTO NO.8-A",
              "KONTAK_PERSON" => "082370374921",
              "KOTA" => "BINJAI",
              "NAMA" => "FARIDA HANUM, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "082370374921"
            ],[
              "KODE" => "050",
              "ALAMAT" => "JL.CENDANA NO.52 LK.V",
              "KONTAK_PERSON" => "082369667147",
              "KOTA" => "BINJAI",
              "NAMA" => "NUR AFRILA, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "082369667147"
            ],[
              "KODE" => "051",
              "ALAMAT" => "JL.TENGKU AMIR HAMZAH NO.218",
              "KONTAK_PERSON" => "081265088777",
              "KOTA" => "BINJAI",
              "NAMA" => "HENRY FERHAD, SH",
              "PASSWORD" => "2020",
              "TELEPON" => "081362162256"
            ],[
              "KODE" => "052",
              "ALAMAT" => "JL.SAMANHUDI NO.344",
              "KONTAK_PERSON" => "085262801287",
              "KOTA" => "BINJAI",
              "NAMA" => "ZULFIANI, SH, M.Kn",
              "PASSWORD" => "zulfianimara75",
              "TELEPON" => "085262801287"
            ],[
              "KODE" => "053",
              "ALAMAT" => "JL.TENGKU AMIR HAMZAH NO.394",
              "KONTAK_PERSON" => "0811641641",
              "KOTA" => "BINJAI",
              "NAMA" => "ZAISIKA KHAIRUNNISAK, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "0811641641"
            ],[
              "KODE" => "054",
              "ALAMAT" => "JL.PERINTIS KEMERDEKAAN NO.204",
              "KONTAK_PERSON" => "08116199020",
              "KOTA" => "BINJAI",
              "NAMA" => "HISKIA MEIKO AUNAMULA PANGGABEAN, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "08116199020"
            ],[
              "KODE" => "055",
              "ALAMAT" => "JL.DR.WAHIDIN NO.151-B",
              "KONTAK_PERSON" => "081265888783",
              "KOTA" => "BINJAI",
              "NAMA" => "EVA ARTHA ANASTASIA SITANGGANG, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "082164757004"
            ],[
              "KODE" => "056",
              "ALAMAT" => "JL.JAMIN GINTING",
              "KONTAK_PERSON" => "082132801433",
              "KOTA" => "BINJAI",
              "NAMA" => "ISKANDAR SAWALEO, SH, M.Kn",
              "PASSWORD" => "aplikasi",
              "TELEPON" => "082132801433"
            ],[
              "KODE" => "057",
              "ALAMAT" => "JL.GATOT SUBROTO NO.115-B",
              "KONTAK_PERSON" => "081397607666",
              "KOTA" => "BINJAI",
              "NAMA" => "LESTARI SEMBIRING MELIALA, SH, M.Kn",
              "PASSWORD" => "841123",
              "TELEPON" => "081397607666"
            ],[
              "KODE" => "058",
              "ALAMAT" => "JL.ISMAIL NO.2-C",
              "KONTAK_PERSON" => "082145597355",
              "KOTA" => "BINJAI",
              "NAMA" => "LAMHOT HERIANTO SIGIRO, SH, M.Kn",
              "PASSWORD" => "171088",
              "TELEPON" => "082145597355"
            ],[
              "KODE" => "059",
              "ALAMAT" => "JL.SOEKARNO HATTA NO.108 KM.20",
              "KONTAK_PERSON" => "-",
              "KOTA" => "BINJAI",
              "NAMA" => "TIGOR SINAMBELA, SH",
              "PASSWORD" => "123",
              "TELEPON" => "-"
            ],[
              "KODE" => "060",
              "ALAMAT" => "JL.CUT NYAK DHIEN NO.39-B",
              "KONTAK_PERSON" => "081370779597",
              "KOTA" => "BINJAI",
              "NAMA" => "AYU FULIASARI, SH, M.Kn",
              "PASSWORD" => "notaris2016",
              "TELEPON" => "081370779597"
            ],[
              "KODE" => "061",
              "ALAMAT" => "JL.SUTOMO NO.39",
              "KONTAK_PERSON" => "081370167956",
              "KOTA" => "BINJAI",
              "NAMA" => "MARIA SIANTURI, SH, M.Kn",
              "PASSWORD" => "mars123",
              "TELEPON" => "081370167956"
            ],[
              "KODE" => "062",
              "ALAMAT" => "JL.SAMANHUDI NO.169",
              "KONTAK_PERSON" => "085276440870",
              "KOTA" => "BINJAI",
              "NAMA" => "GERNALIA NOVA PANGGABEAN, SH, M.Kn",
              "PASSWORD" => "notgerna",
              "TELEPON" => "085276440870"
            ],[
              "KODE" => "063",
              "ALAMAT" => "JL.JUANDA NO.1",
              "KONTAK_PERSON" => "081311162077",
              "KOTA" => "BINJAI",
              "NAMA" => "JESIE SALIM, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081311162077"
            ],[
              "KODE" => "064",
              "ALAMAT" => "JL.RUKAM NO.9-B",
              "KONTAK_PERSON" => "085262663356",
              "KOTA" => "BINJAI",
              "NAMA" => "WILLIANA HALIM, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "085262663356"
            ],[
              "KODE" => "065",
              "ALAMAT" => "JL.SULTAN HASANUDDIN NO.77",
              "KONTAK_PERSON" => "082164991325",
              "KOTA" => "BINJAI",
              "NAMA" => "MARTINA JULIANCE PB SIHOMBING",
              "PASSWORD" => "123",
              "TELEPON" => "082164991325"
            ],[
              "KODE" => "066",
              "ALAMAT" => "JL.PERINTIS KEMERDEKAAN NO. 117 C",
              "KONTAK_PERSON" => "082277399587",
              "KOTA" => "BINJAI",
              "NAMA" => "MELLY, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "082277399587"
            ],[
              "KODE" => "067",
              "ALAMAT" => "JL.PERINTIS KEMERDEKAAN NO.49 C",
              "KONTAK_PERSON" => "081361710115",
              "KOTA" => "BINJAI",
              "NAMA" => "DEVI MUTIA MASTURA, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081361710115"
            ],[
              "KODE" => "068",
              "ALAMAT" => "JL.T.AMIR HAMZAH NO.380",
              "KONTAK_PERSON" => "081396495269",
              "KOTA" => "BINJAI",
              "NAMA" => "SALAWATI SUYITNO, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081396495269"
            ],[
              "KODE" => "069",
              "ALAMAT" => "JL.SOEKARNO HATTA KM.20 NO.289",
              "KONTAK_PERSON" => "05361044764",
              "KOTA" => "BINJAI",
              "NAMA" => "ABDUL KARIM, S.Pd, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "05361044764"
            ],[
              "KODE" => "070",
              "ALAMAT" => "JL.GUNUNG BENDAHARA II KEL.BINJAI ESTATE, KEC.BINJAI SELATAN",
              "KONTAK_PERSON" => "085296666700",
              "KOTA" => "BINJAI",
              "NAMA" => "SUKMA HARTATI, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "085296666700"
            ],[
              "KODE" => "071",
              "ALAMAT" => "JL.MT.HARYONO KEL.JATI KARYA, KEC.BINJAI UTARA",
              "KONTAK_PERSON" => "081362027668",
              "KOTA" => "BINJAI",
              "NAMA" => "YUHENI HASARIAH SIREGAR, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081362027668"
            ],[
              "KODE" => "072",
              "ALAMAT" => "JL.CUT NYAK DHIEN NO.16",
              "KONTAK_PERSON" => "081361185123",
              "KOTA" => "BINJAI",
              "NAMA" => "SYARIFAH NADIRA, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081361185123"
            ],[
              "KODE" => "073",
              "ALAMAT" => "JL.T.AMIR HAMZAH NO.35",
              "KONTAK_PERSON" => "085261502267",
              "KOTA" => "BINJAI",
              "NAMA" => "KHAIRUNA MALIK HASIBUAN, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "085261502267"
            ],[
              "KODE" => "074",
              "ALAMAT" => "JL.BANDUNG NO.15 RAMBUNG BARAT, BINJAI SELATAN",
              "KONTAK_PERSON" => "081370225943",
              "KOTA" => "BINJAI",
              "NAMA" => "JOKO KUNCORO, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "081370225943"
            ],[
              "KODE" => "075",
              "ALAMAT" => "JL.TENGKU AMIR HAMZAH NO.48",
              "KONTAK_PERSON" => "082167568956",
              "KOTA" => "BINJAI",
              "NAMA" => "TIESA SALEH, SH, M.Kn",
              "PASSWORD" => "241092",
              "TELEPON" => "082167568956"
            ],[
              "KODE" => "076",
              "ALAMAT" => "JL.LETJEND JAMIN GINTING NO.11",
              "KONTAK_PERSON" => "082168597327",
              "KOTA" => "BINJAI",
              "NAMA" => "JULI MURNIATY GINTING, SH, M.Kn",
              "PASSWORD" => "jgnjgnlupa78",
              "TELEPON" => "082168597327"
            ],[
              "KODE" => "077",
              "ALAMAT" => "JL.SOEKARNO HATTA KM 18.6 NO.387 D",
              "KONTAK_PERSON" => "08112226232",
              "KOTA" => "BINJAI",
              "NAMA" => "DONNY YUDHISTIRA, SH, M.Kn",
              "PASSWORD" => "123",
              "TELEPON" => "08112226232"
            ],[
              "KODE" => "888",
              "ALAMAT" => "JL.JAMBI KEL.RAMBUNG BARAT KEC.BINJAI SELATAN",
              "KONTAK_PERSON" => "-",
              "KOTA" => "BINJAI",
              "NAMA" => "BADAN PENGELOLAAN KEUANGAN PENDAPATAN DAN ASET DAERAH",
              "PASSWORD" => "123",
              "TELEPON" => "-"
            ]
        ];

        foreach ($dataNotaris as $value) :
            User::create([
                'role'        => 'notaris',
                'kode'       => $value['KODE'],
                'alamat'       => $value['ALAMAT'],
                'kontak_person'       => $value['KONTAK_PERSON'],
                'name'        => $value['NAMA'],
                'password'    => Hash::make($value['PASSWORD']),
                'no_hp'       => $value['TELEPON'],
                'kota'       => $value['KOTA'],
            ]);
        endforeach;
    }
}
