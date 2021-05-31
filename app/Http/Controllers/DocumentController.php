<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class DocumentController extends Controller
{
    public function upload(Request $request, $name){
        $pdo = DB::getPdo();

        $file = $_FILES[$name];

        $id = $request->id;
        $procurement = $request->procurement;
        $vendor = $request->vendor;
        $item = $request->item;

        $ref = strtoupper($request->spph_ref);
        $doc_type = $file['type'];
        $doc = file_get_contents($file['tmp_name']);
        $date = date('Y-m-d H:i:s');
        $type = $name;
        
        if($name == 'quotation'){
            $stmt = $pdo->prepare("UPDATE quotations 
                                    SET name=?, type=?, doc_type=?, doc=?, updated_at=? 
                                    WHERE id=? AND procurement=? AND vendor=? AND item=?");
            $stmt->bindParam(1, $ref);
            $stmt->bindParam(2, $type);
            $stmt->bindParam(3, $doc_type);
            $stmt->bindParam(4, $doc);
            $stmt->bindParam(5, $date);
            $stmt->bindParam(6, $id);
            $stmt->bindParam(7, $procurement);
            $stmt->bindParam(8, $vendor);
            $stmt->bindParam(9, $item);

            $stmt->execute();
        }elseif ($name == 'spph'){
            if (\App\Models\VendorDoc::where('procurement', '=', $procurement)->where('type', '=', 'spph')->doesntExist()){
                $new_status = \App\Models\Status::select('id')->where('name', '=', 'SPPH')->get()[0];
                \App\Models\Procurement::where('id', '=', $request->procurement)
                    ->update([
                        'status' => $new_status->id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            }

            $stmt = $pdo->prepare("INSERT INTO vendor_docs VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $procurement);
            $stmt->bindParam(2, $vendor);
            $stmt->bindParam(3, $item);
            $stmt->bindParam(4, $ref);
            $stmt->bindParam(5, $type);
            $stmt->bindParam(6, $doc_type);
            $stmt->bindParam(7, $doc);
            $stmt->bindParam(8, $date);
            $stmt->bindParam(9, $date);

            $stmt->execute();
        }

        return (redirect(url()->previous()));
    }

    public function view($id, $table){
        $pdo = DB::getPdo();

        $row = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $row->bindParam(1, $id);

        $row->execute();

        $data = $row->fetch();
        
        return view('procurement.my.doc', [
            'data' => $data,
        ]);
    }

    public function generateSpph(Request $request){
        $procurement = \App\Models\Procurement::where('id', '=', $request->proc_id)->get()[0];
        $vendor = \App\Models\Vendor::where('id', '=', $request->vendor_id)->get()[0];
        $items = \App\Models\Quotation::join('items', 'items.id', '=', 'quotations.item')
            ->select('items.name', 'items.specs', 'items.qty')
            ->where('quotations.vendor', '=', $request->vendor_id)
            ->get();
        $proc_manager = \App\Models\User::join('roles', 'roles.id', '=', 'users.role')
            ->join('units', 'units.id', '=', 'users.unit')
            ->select('users.email')
            ->where('roles.name', '=', 'Manajer')
            ->where('units.name', '=', 'Fungsi Pengadaan Barang dan Jasa')
            ->get()[0];
        $pic = \App\Models\User::join('procurements', 'procurements.pic', '=', 'users.id')
            ->select('email')
            ->get()[0];

        $mail_list = "
        <li><a href='mailto:procurement@universitaspertamina.ac.id'>procurement@universitaspertamina.ac.id</a></li>
        <li><a href='mailto:$proc_manager->email'>$proc_manager->email</a></li>
        ";

        if($proc_manager->email != $pic->email){
            $mail_list .= "<li><a href='mailto:$pic->email'>$pic->email</a></li>";
        }

        // CSS
        $font_bold = "font-weight: bold;";
        $font_italic = "font-style: italic;";
        $font_size_body = "font-size: 10pt;";
        $font_size_footer = "font-size: 6pt;";
        $text_justify = "text-align: justify; text-justify: inter-word;";

        $mpdf = new \Mpdf\Mpdf([
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ]);

        $doc_name = "SPPH_" . $request->vendor . "_" . $procurement->name . "_" . date('Ymd-His');

        $mpdf->SetTitle($doc_name);

        $header_logo_path = asset('img/universitas-pertamina.png');

        $mpdf->SetHTMLHeader(
            "<div style='text-align: center;'>
                <img src='https://universitaspertamina.ac.id/wp-content/uploads/2017/11/logo-Press-201x146.png' width='100'>
            </div>"
        );

        $mpdf->SetHTMLFooter(
            "
            <div style='width: 100%; height: 1;'>
                <p style='$font_size_footer'>
                    Gedung Rektorat (R1)
                    <br>
                    Kawasan Universitas Pertamina
                    <br>
                    Jl. Teuku Nyak Arief
                    <br>
                    Simprug, Kebayoran Lama, Jakarta Selatan. 12220.
                    <br>
                    Telp. (+62) 21 722 3029
                </p>
            </div>
            <div style='background-color: #4091f5; width: 100%; height: 30px; border-radius: 0 100% 0 0;'></div>
            "
        );

        setlocale(LC_TIME, 'id_ID');
        $mpdf->WriteHTML("<p style='$font_size_body'>Jakarta, " . strftime('%d %B %Y') .  '</p>');

        $mpdf->WriteHTML(
            "<table style='$font_size_body'>
                <tr>
                    <td>Nomor</td>
                    <td>:</td>
                    <td>" . strtoupper($request->ref) . "</td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>1 (satu) lembar</td>
                </tr>
                <tr>
                    <td style='vertical-align: top;'>Perihal</td>
                    <td style='vertical-align: top;'>:</td>
                    <td style='$font_bold'>Permohonan Proposal Penawawan Harga $procurement->name</td>
                </tr>
            </table>"
        );

        $mpdf->WriteHTML(
            "<p style='$font_bold $font_size_body'>
                Yth. $request->receiver $request->vendor,
            </p>
            <p style='$text_justify $font_size_body'>
                Dengan hormat, sehubungan dengan memorandum no. $procurement->ref 
                pada tanggal " . date("d F Y", strtotime($procurement->created_at)) . "
                tentang <span style='$font_bold'>$procurement->name</span>, 
                kami mengharapkan perusahaan Bapak/Ibu untuk dapat mengajukan penawaran
                terkait pengadaan tersebut. Spesifikasi kebutuhan dapat dilihat pada Lampiran.
                Berikut adalah penjelasan singkat dalam pengajuan permohonan penawaran ini:
            </p>"
        );

        $day_deadline = date_create('NOW')->modify('+14 day')->format('d F Y');

        $mpdf->WriteHTML(
            "<ol style='$text_justify $font_size_body'>
                <li>
                    Perusahaan yang telah diundang dipersilahkan untuk melampirkan
                    penawaran harga dengan syarat sebagai berikut:
                    <ol>
                        <li>
                            Profil Perusahaan
                            <ol style='list-style-type: lower-alpha;'>
                                <li>
                                    Salinan Surat Izin Tempat Usaha / Surat Keterangan Domisili Perusahaan
                                    dari instansi berwenang.
                                </li>
                                <li>Salinan Nomor Pokok Wajib Pajak (NPWP)</li>
                                <li>Salinan surat pengukuhan pengusaha kena pajak</li>
                                <li>Salinan Tanda Daftar Perusahaan (TDP)</li>
                                <li>Salinan Surat Izin Usaha Perdagangan (SIUP)</li>
                                <li>Salinan Surat neraca perusahaan (kualifikasi perusahaan)</li>
                                <li>Salinan akta pendirian/anggaran dasar penyedia barang/jasa</li>
                                <li>Salinan tanda pengenal pengurus</li>
                                <li>Salinan surat perjanjian keagenan/distributor</li>
                                <li>Daftar pengalaman kerja 2 (dua) tahun terakhir</li>
                                <li>Surat pernyataan asli di atas materai bahwa semua informasi yang disampaikan adalah benar</li>
                                <li>Pakta Integritas dan Surat Pernyataan (format terlampir)</li>
                            </ol>
                        </li>
                        <li>Surat pengantar penawaran harga</li>
                        <li>Lampiran penawaran harga harus sesuai dengan spesifikasi yang dilampirkan</li>
                    </ol>
                </li>
                <li>
                    Perusahaan yang telah menerima surat undangan resmi ini diberikan waktu untuk mengirimkan penawaran
                    sampai dengan tanggal <span style='$font_bold'>$day_deadline</span>
                </li>
                <li>
                    Surat permohonan penawaran dalam bentuk <span style='$font_italic'>softcopy</span> dapat dikirimkan ke alamat email:
                    <ol style='list-style-type: none;'>
                        $mail_list
                    </ol>
                </li>
            </ol>"
        );

        $mpdf->WriteHTML(
            "<p style='$text_justify $font_size_body'>
                Demikian surat undangan ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.
            </p>"
        );

        $mpdf->AddPageByArray(['type' => '']);

        $mpdf->WriteHTML(
            "<p style='$font_size_body $font_bold text-align: center;'>Lampiran Kebutuhan</p>"
        );
        
        $table_style = $font_size_body . "border-collapse: collapse; border: 1px solid black; width: 75%; margin-left: auto; margin-right: auto;";
        $table_border = "border: 1px solid black;";

        $item_list = "";

        foreach ($items as $item){
            $item_list .= "
            <tr style='$table_border'>
                <td style='$table_border'>$item->name</td>
                <td style='$table_border'>$item->specs</td>
                <td style='$table_border text-align: center;'>$item->qty</td>
            </tr>";
        }

        $mpdf->WriteHTML(
            "<table style='$table_style'>
                <thead>
                    <tr style='$table_border'>
                        <th style='$table_border'>Perangkat</th>
                        <th style='$table_border'>Spesifikasi</th>
                        <th style='$table_border'>Kebutuhan</th>
                    </tr>
                </thead>
                <tbody>
                    $item_list
                </tbody>
            </table>"
        );

        $mpdf->WriteHTML(
            "<p style='$font_size_body $text_justify'>
                * Spesifikasi yang disebutkan adalah referensi perangkat yang dibutuhkan. 
                Vendor diperbolehkan untuk mengirimkan alat dengan merek/tipe lain selama spesifikasi minimum berdasarkan
                alat referensi yang kami sebutkan terpenuhi.
                Dalam proses evaluasi, kriteria yang digunakan adalah pemenuhan spesifikasi minimum, harga, dan layanan purna jual.
            </p>"
        );

        $mpdf->WriteHTML(
            "<p style='$font_size_body $font_bold'>
                Kriteria Penilaian:
                <br>
                Berikut kriteria penilaian untuk setiap penawaran yang masuk:
            </p>"
        );

        $mpdf->WriteHTML(
            "<table style='$table_style'>
                <thead>
                    <tr style='$table_border background-color: #99e6ff;'>
                        <th style='$table_border'>Item Penilaian</th>
                        <th style='$table_border'>Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style='$table_border'>
                        <th style='$table_border text-align: left;'>Spesifikasi</th>
                        <th style='$table_border text-align: center;'>Mandatory <br> (Spesifikasi minimum wajib terpenuhi)</th>
                    </tr>
                    <tr style='$table_border'>
                        <th style='$table_border text-align: left;'>Garansi</th>
                        <th style='$table_border text-align: center;'>Wajib memberi garansi (wajib terpenuhi) <br> Menyampaikan jenis garansi yang ditawarkan</th>
                    </tr>
                    <tr style='$table_border'>
                        <th style='$table_border text-align: left;'>Harga</th>
                        <th style='$table_border text-align: center;'>80%</th>
                    </tr>
                    <tr style='$table_border'>
                        <th style='$table_border text-align: left;'>Komitmen waktu penyelesaian</th>
                        <th style='$table_border text-align: center;'>20%</th>
                    </tr>
                </tbody>
            </table>"
        );

        $mpdf->WriteHTML(
            "<p style='$font_size_body $font_bold'>
                Catatan: Kelengkapan dokumen perusahaan harus sesuai dengan Ketentuan Pengadaan Universitas Pertamina
            </p>"
        );

        $doc_name = $doc_name . ".pdf";

        return $mpdf->Output($doc_name, "I");
    }

    public function generateBapp(Request $request){
        for ($i=0; $i < count($request->quotation_price); $i++) { 
            \App\Models\Item::where('id', '=', $request->item[$i])
                ->update([
                    'quotation_price' => $request->quotation_price[$i],
                    'nego_price' => $request->nego_price[$i],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        }

        $procurement = \App\Models\Procurement::join('proc_mechanisms', 'proc_mechanisms.id', '=', 'procurements.mechanism')
            ->select('procurements.*', 'proc_mechanisms.name AS mech')
            ->where('procurements.id', '=', $request->proc_id)->get()[0];
        
        $vendors_id = '';

        for ($i=0; $i < count($request->vendor); $i++) { 
            $vendors_id .= "vendors.id = " . $request->vendor[$i];
            if(++$i != count($request->vendor)){
                $vendors_id .= " OR ";
            }
        }

        $pdo = DB::getPdo();

        $row = $pdo->prepare("SELECT items.name, items.specs, items.qty, items.quotation_price, items.nego_price, vendors.name AS vendor, vendors.address, vendors.phone, vendors.tin
                                FROM items INNER JOIN quotations ON items.id = quotations.item INNER JOIN vendors ON vendors.id = quotations.vendor
                                WHERE $vendors_id");

        $row->execute();

        $items = $row->fetch(PDO::FETCH_ASSOC);

        
        $receiver = \App\Models\User::join('roles', 'roles.id', '=', 'users.role')
            ->join('units', 'units.id', '=', 'users.unit')
            ->select('users.name', 'roles.name AS role', 'units.name AS unit')
            ->where('roles.name', '=', 'Wakil Rektor')
            ->where('units.name', '=', 'Bidang Keuangan dan Sumber Daya Organisasi')
            ->get()[0];
        $sender = \App\Models\User::join('roles', 'roles.id', '=', 'users.role')
            ->join('origins', 'origins.id', '=', 'users.origin')
            ->select('users.name', 'roles.name AS role', 'origins.name AS origin')
            ->where('roles.name', '=', 'Direktur')
            ->where('origins.name', '=', 'Fungsi Pengelola Fasilitas Universitas')
            ->get()[0];

        // CSS
        $font_bold = "font-weight: bold;";
        $font_italic = "font-style: italic;";
        $font_size_body = "font-size: 10pt;";
        $font_size_footer = "font-size: 6pt;";
        $text_justify = "text-align: justify; text-justify: inter-word;";

        $mpdf = new \Mpdf\Mpdf([
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ]);

        $doc_name = "BAPP_" . $items['vendor'] . "_" . $procurement->name . "_" . date('Ymd-His');


        $mpdf->SetTitle($doc_name);

        $header_logo_path = asset('img/universitas-pertamina.png');

        $mpdf->SetHTMLHeader(
            "<div style='text-align: center;'>
                <img src='https://universitaspertamina.ac.id/wp-content/uploads/2017/11/logo-Press-201x146.png' width='100'>
            </div>"
        );

        $mpdf->SetHTMLFooter(
            "
            <div style='width: 100%; height: 1;'>
                <p style='$font_size_footer'>
                    Gedung Rektorat (R1)
                    <br>
                    Kawasan Universitas Pertamina
                    <br>
                    Jl. Teuku Nyak Arief
                    <br>
                    Simprug, Kebayoran Lama, Jakarta Selatan. 12220.
                    <br>
                    Telp. (+62) 21 722 3029
                </p>
            </div>
            <div style='background-color: #4091f5; width: 100%; height: 30px; border-radius: 0 100% 0 0;'></div>
            "
        );
        


        $mpdf->WriteHTML(
            "<p style='$font_size_body text-align: center;'>
            <u style='$font_bold font-size: 12pt;'>BERITA ACARA PENUNJUKKAN PEMENANG</u>
            <br>
            $request->ref
            </p>"
        );
        
        setlocale(LC_TIME, 'id_ID');
        $mpdf->WriteHTML("<p style='$font_size_body'>Jakarta, " . strftime('%d %B %Y') .  '</p>');

        $mpdf->WriteHTML(
            "<table style='$font_size_body'>
                <tr>
                    <td>Kepada</td>
                    <td>:</td>
                    <td>$receiver->role $receiver->unit</td>
                </tr>
                <tr>
                    <td>Dari</td>
                    <td>:</td>
                    <td>$sender->role $sender->origin</td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>1 Bundel</td>
                </tr>
                <tr>
                    <td>Sifat</td>
                    <td>:</td>
                    <td>Rahasia/Terbatas</td>
                </tr>
                <tr>
                    <td style='vertical-align: top;'>Perihal</td>
                    <td style='vertical-align: top;'>:</td>
                    <td style='$font_bold'>Permohonan Persetujuan Penetapan Pemenang $procurement->name</td>
                </tr>
            </table>"
        );

        $itemVendor = $items['vendor'];

        $mpdf->WriteHTML(
            "<p style='$font_size_body $text_justify'>
                Berkaitan dengan:
                <ol style='$font_size_body $text_justify'>
                    <li> Memorandum <span style='$font_bold'>$procurement->ref</span> tentang <span style='$font_bold'>$procurement->name</span> pada tanggal " . date('d F Y', strtotime($procurement->created_at)) . ".</li>
                </ol>
            </p>"
        );

        $doc_name = $doc_name . ".pdf";

        return $mpdf->Output($doc_name, "I");
    }

    public function generateSpphForm($proc_id, $vendor_id){
        $vendor = \App\Models\Vendor::select('name')->where('id', '=', $vendor_id)->get()[0];
        $procurement = \App\Models\Procurement::select('ref', 'name')->where('id', '=', $proc_id)->get()[0];
        $items = \App\Models\Quotation::join('items', 'items.id', '=', 'quotations.item')
            ->select('items.name', 'items.specs')
            ->where('quotations.vendor', '=', $vendor_id)
            ->get();
        
        return view('procurement.documents.spph.form', [
            'proc_id' => $proc_id,
            'vendor_id' => $vendor_id,
            'vendor' => $vendor,
            'procurement' => $procurement,
            'items' => $items,
        ]);
    }

    public function generateBappForm($proc_id, $vendor_id){
        $procurement = \App\Models\Procurement::select('ref', 'name')->where('id', '=', $proc_id)->get()[0];
        $items = \App\Models\Quotation::join('items', 'items.id', '=', 'quotations.item')
            ->select('items.id', 'items.name', 'items.specs', 'quotations.vendor')
            ->where('quotations.vendor', '=', $vendor_id)
            ->get();
        
        
        return view('procurement.documents.bapp.form', [
            'proc_id' => $proc_id,
            'vendor_id' => $vendor_id,
            'items' => $items,
            'procurement' => $procurement,
        ]);
    } 
}
