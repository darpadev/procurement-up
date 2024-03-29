<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use PDO;

class DocumentController extends Controller
{
    public function upload(Request $request, $name){
        $pdo = DB::getPdo();

        $file = $_FILES[$name];
        $doc_type = $file['type'];
        $doc = file_get_contents($file['tmp_name']);
        $date = date('Y-m-d H:i:s');

        $ref = strtoupper($request->ref);
        if(!strpos($ref, '.pdf')){
            $ref .= '.pdf';
        }

        if($name == 'quotation'){
            $vendor = $request->vendor;

            $stmt = $pdo->prepare("UPDATE quotations 
                                    SET name=?, doc_type=?, doc=?, updated_at=? 
                                    WHERE vendor=?");
            $stmt->bindParam(1, $ref);
            $stmt->bindParam(2, $doc_type);
            $stmt->bindParam(3, $doc);
            $stmt->bindParam(4, $date);
            $stmt->bindParam(5, $vendor);

            $stmt->execute();

            $vendor = \App\Models\Vendor::select('name')->where('id', '=', $request->vendor)->first();

            $log = new \App\Models\ProcLog;

            $log->procurement   = $request->procurement;
            $log->message       = "Penawaran dari $vendor->name berhasil diunggah";
            $log->sender        = Auth::user()->id;

            $log->save();
        }elseif ($name == 'spph'){
            $sub_categories = \App\Models\Quotation::select('item_sub_category AS name')
                                ->where('vendor', '=', $request->vendor)
                                ->get();

            foreach($sub_categories AS $sub){
                $procurement = $request->procurement;
                $vendor = $request->vendor;
                $item = $sub->name;
                $type = $name;

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

            $vendor = \App\Models\Vendor::select('name')->where('id', '=', $request->vendor)->first();

            $log = new \App\Models\ProcLog;

            $log->procurement   = $procurement;
            $log->message       = "SPPH untuk $vendor->name berhasil diunggah";
            $log->sender        = Auth::user()->id;

            $log->save();

            $proc = \App\Models\Procurement::find($procurement);
            $new_status     = \App\Models\Status::select('id')->where('name', '=', 'SPPH')->first();
            $proc->status   = $new_status->id;

            $proc->save();
        }elseif ($name == 'bapp'){
            $procurement = $request->procurement;
            $type = $name;

            $stmt = $pdo->prepare("INSERT INTO documents VALUES(NULL, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $procurement);
            $stmt->bindParam(2, $ref);
            $stmt->bindParam(3, $doc_type);
            $stmt->bindParam(4, $type);
            $stmt->bindParam(5, $date);
            $stmt->bindParam(6, $date);
            $stmt->bindParam(7, $doc);

            $stmt->execute();

            $log = new \App\Models\ProcLog;

            $log->procurement   = $procurement;
            $log->message       = "BAPP berhasil diunggah";
            $log->sender        = Auth::user()->id;

            $log->save();

            $proc_id = $procurement;
            $status = \App\Models\Status::select('id')->where('name', '=', 'BAPP')->first();

            $procurement = \App\Models\Procurement::find($proc_id);

            $procurement->status = $status->id;

            $procurement->save();
        }

        return (redirect(url()->previous()));
    }

    public function view($id, $table, $proc_id = NULL){
        if($proc_id){
            $procurement = \App\Models\Procurement::find($proc_id);
            $status = \App\Models\Status::select('id', 'name')->where('name', '=', 'Tender Evaluation')->first();

            if(Auth::user()->id == $procurement->applicant And $procurement->status != $status->id){
                
                $procurement->status = $status->id;

                $procurement->save();

                $log = new \App\Models\ProcLog;

                $log->procurement   = $proc_id;
                $log->message       = "Status pengadaan sudah diperbaharui: $status->name";
                $log->sender        = Auth::user()->id;

                $log->save();
            }
        }

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
        $procurement = \App\Models\Procurement::where('id', '=', $request->proc_id)->first();
        $vendor = \App\Models\Vendor::where('id', '=', $request->vendor_id)->first();
        $items = \App\Models\Item::join('quotations', 'quotations.item_sub_category', '=', 'items.sub_category')
                    ->select('items.name', 'items.specs', 'items.qty')
                    ->where('items.procurement', '=', $request->proc_id)
                    ->where('quotations.vendor', '=', $request->vendor_id)
                    ->get();
        $proc_manager = \App\Models\User::join('roles', 'roles.id', '=', 'users.role')
            ->join('units', 'units.id', '=', 'users.unit')
            ->select('users.name', 'users.email', 'roles.name AS role', 'units.name AS unit')
            ->where('roles.name', '=', 'Manajer')
            ->where('units.name', '=', 'Fungsi Pengadaan Barang dan Jasa')
            ->first();
        $pic = \App\Models\User::join('procurements', 'procurements.pic', '=', 'users.id')
            ->select('email')
            ->first();

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

        $mpdf->WriteHTML("<p style='$font_size_body'>Jakarta, " . dateIDN(date('Ymd')) .  '</p>');

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
                    <td style='$font_bold'>Permohonan Proposal Penawaran Harga $procurement->name</td>
                </tr>
            </table>"
        );

        $mpdf->WriteHTML(
            "<p style='$font_bold $font_size_body'>
                Yth. $request->receiver $request->vendor,
            </p>
            <p style='$text_justify $font_size_body'>
                Dengan hormat, sehubungan dengan memorandum no. $procurement->ref 
                pada tanggal " . dateIDN($procurement->created_at) . "
                tentang <span style='$font_bold'>$procurement->name</span>, 
                kami mengharapkan perusahaan Bapak/Ibu untuk dapat mengajukan penawaran
                terkait pengadaan tersebut. Spesifikasi kebutuhan dapat dilihat pada Lampiran.
                Berikut adalah penjelasan singkat dalam pengajuan permohonan penawaran ini:
            </p>"
        );

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
                    sampai dengan tanggal <span style='$font_bold'>" . futureDate(14) . "</span>
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

        $mpdf->WriteHTML(
            "
            <p style='$font_size_body $font_bold'>
                $proc_manager->role $proc_manager->unit
                <br>
                <br>
                <br>
                <br>
                <br>
                $proc_manager->name
            </p>
            "
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
        // dd($request->all());
        // $pdo = DB::getPdo();

        // $row = $pdo->prepare("SELECT items.name, items.specs, items.qty, items.quotation_price, items.nego_price, vendors.name AS vendor, vendors.address, vendors.phone, vendors.tin
        //                         FROM items INNER JOIN quotations ON items.id = quotations.item INNER JOIN vendors ON vendors.id = quotations.vendor
        //                         WHERE $vendors_id");

        // $row->execute();

        // $items = $row->fetch(PDO::FETCH_ASSOC);
        
        $direktur_pfu = \App\Models\User::join('origins', 'origins.id', '=', 'users.origin')
                            ->join('roles', 'roles.id', '=', 'users.role')
                            ->select('users.name', 'roles.name AS role', 'origins.name AS origin')
                            ->where('roles.name', '=', 'Direktur')
                            ->where('origins.name', '=', 'Fungsi Pengelola Fasilitas Universitas')
                            ->first();

        $manajer_procurement = \App\Models\User::join('units', 'units.id', '=', 'users.unit')
                                ->join('roles', 'roles.id', '=', 'users.role')
                                ->select('users.name', 'roles.name AS role', 'units.name AS unit')
                                ->where('roles.name', '=', 'Manajer')
                                ->where('units.name', '=', 'Fungsi Pengadaan Barang dan Jasa')
                                ->first();

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

        $procurement = \App\Models\Procurement::where('id', '=', $request->procurement)->first();

        $doc_name = "BAPP_" . $procurement->name . "_" . date('Ymd-His') . ".pdf";


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
        
        $table_style = $font_size_body . "border-collapse: collapse; border: 1px solid black; width: 100%;";
        $table_border = "border: 1px solid black;";

        $mpdf->WriteHTML(
            "<p style='$font_size_body text-align: center;'>
                <u style='$font_bold font-size: 12pt;'>BERITA ACARA PENETAPAN PEMENANG</u>
                <br>
                No. $request->ref
            </p>"
        );

        $mpdf->WriteHTML("<p style='$font_size_body'>Jakarta, " . dateIDN(date('Ymd')) .  '</p>');
        
        $mpdf->WriteHTML("
            <table style='$font_size_body'>
                <tr>
                    <td style='padding-right: 16px;'>Kepada</td>
                    <td>:</td>
                    <td style='padding-left: 4px;'>Direktur Fungsi Pengelola Fasilitas Universitas</td>
                </tr>
                <tr>
                    <td style='padding-right: 16px;'>Dari</td>
                    <td>:</td>
                    <td style='padding-left: 4px;'>Manajer Fungsi Pengadaan Barang dan Jasa</td>
                </tr>
                <tr>
                    <td style='padding-right: 16px;'>Lampiran</td>
                    <td>:</td>
                    <td style='padding-left: 4px;'>1 Bundel</td>
                </tr>
                <tr>
                    <td style='padding-right: 16px;'>Sifat</td>
                    <td>:</td>
                    <td style='padding-left: 4px;'>Rahasia/Terbatas</td>
                </tr>
                <tr>
                    <td style='padding-right: 16px; vertical-align: top;'>Perihal</td>
                    <td style='vertical-align: top;'>:</td>
                    <td style='$font_bold $text_justify padding-left: 4px;'>Permohonan Persetujuan Penetapan Pemenang $procurement->name</td>
                </tr>
            </table>
        ");

        $quotations = \App\Models\Quotation::join('vendors', 'vendors.id', '=', 'quotations.vendor')
                        ->leftJoin('vendor_docs', 'vendor_docs.vendor', '=', 'quotations.vendor')
                        ->select('quotations.name', 'vendors.name AS vendor_name', 'vendor_docs.name AS spph_ref')
                        ->where('quotations.procurement', '=', $procurement->id)
                        ->orderBy('vendors.name')
                        ->distinct('quotations.vendor')
                        ->get();
        $earliest_quotation = \App\Models\Quotation::where('procurement', '=', $procurement->id)->min('created_at');

        $spph_data = '';

        $counter = 1;
        foreach($quotations as $index => $quotation){
            if($index < 1){
                $spph_data .= "
                    <tr>
                        <td style='$table_border text-align: center'>$counter</td>
                        <td rowspan=" . 10 . " style='$table_border padding: 8px; text-align: center;'>
                            Rp" . number_format($procurement->value * 1.1, 0, '', '.') . "
                        </td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>$quotation->vendor_name</td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>" . str_replace('.pdf', '', $quotation->spph_ref) . "</td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>" . str_replace('.pdf', '', $quotation->name) . "</td>
                    </tr>
                ";
            }else{
                $spph_data .= "
                    <tr>
                        <td style='$table_border text-align: center'>$counter</td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>$quotation->vendor_name</td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>" . str_replace('.pdf', '', $quotation->spph_ref) . "</td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>" . str_replace('.pdf', '', $quotation->name) . "</td>
                    </tr>
                    <tr>
                        <td style='$table_border text-align: center'>$counter</td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>$quotation->vendor_name</td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>" . str_replace('.pdf', '', $quotation->spph_ref) . "</td>
                        <td style='$table_border padding: 8px; vertical-align: top;'>" . str_replace('.pdf', '', $quotation->name) . "</td>
                    </tr>
                ";
            }
            $counter += 1;
        }

        $available_quotation = 0;

        foreach($quotations as $quotation){
            if($quotation->name != NULL) $available_quotation += 1;
        }

        $vendor_invited = \App\Models\Quotation::where('procurement', '=', $procurement->id)
                            ->distinct('vendor')
                            ->count('vendor');

        $mpdf->WriteHTML(
            "<p style='$font_size_body $text_justify'>
                Berkaitan dengan:
                <ol style='$font_size_body $text_justify'>
                    <li style='margin-bottom: 16px'> 
                        Memorandum no. <span style='$font_bold'>$procurement->ref</span> 
                        tentang <span style='$font_bold'>$procurement->name</span> pada tanggal " . dateIDN($procurement->created_at) . 
                    ".</li>
                    <li style='margin-bottom: 16px'>
                        Surat Permintaan Penawaran Harga yang dikirimkan kepada " . $vendor_invited . " 
                        vendor pada tanggal " . dateIDN($earliest_quotation) . "
                        sebagai berikut:
                        <table style='$table_border $table_style'>
                            <thead>
                                <tr>
                                    <th style='$table_border width: 5%'>No.</th>
                                    <th style='$table_border width: 20%'>Owner Estimate<br>(+PPN)</th>
                                    <th style='$table_border width: 15%'>Nama<br>Vendor</th>
                                    <th style='$table_border width: 30%'>No. SPPH</th>
                                    <th style='$table_border width: 30%'>Surat Penawaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                $spph_data
                            </tbody>
                        </table>
                    </li>
                    <li style='margin-bottom: 16px'>
                        Berdasarkan Surat Permintaan Penawaran Harga yang dikirimkan ke vendor, terdapat $available_quotation
                        vendor yang mengirimkan penawaran harga, sebagai berikut:
                    </li>
                </ol>
            </p>"
        );

        return $mpdf->Output($doc_name, "I");
    }

    public function generatePo(Request $request){
        $vendor = \App\Models\Vendor::where('id', '=', $request->vendor_id)->first();
        $procurement = \App\Models\Procurement::select('name')->where('id', '=', $request->proc_id)->first();
        $items = \App\Models\Item::where('procurement', '=', $request->proc_id)->get();

        $procurement_value = 0;
        $total_discount = 0;

        foreach($items as $index => $item){
            if($index == count($request->item)){
                break;
            }
            if($item->id == $request->item[$index]){
                $procurement_value += $item->quotation_price * $item->qty;
                $total_discount += $item->discount * $item->qty;
            }
        }
        
        $final_price = ($procurement_value - $total_discount) * 1.1;
        // CSS
        $font_bold = "font-weight: bold;";
        $font_italic = "font-style: italic;";
        $font_size_body = "font-size: 10pt;";
        $text_justify = "text-align: justify; text-justify: inter-word;";
        $table_style = $font_size_body . "border-collapse: collapse; border: 1px solid black; width: 100%;";
        $table_border = "border: 1px solid black;";

        $mpdf = new \Mpdf\Mpdf();

        $doc_name = "PO_" . $vendor->name . "_" . $procurement->name . "_" . date('Ymd-His') . ".pdf";

        $mpdf->SetTitle($doc_name);

        $header_logo_path = asset('img/universitas-pertamina.png');

        $mpdf->WriteHTML(
            "
            <table style='$table_border $table_style'>
                <tr>
                    <th style='$table_border width: 120; height: 90'><img src='https://universitaspertamina.ac.id/wp-content/uploads/2017/11/logo-Press-103x75.png'></th>
                    <th style='text-align: left; padding-left: 10px;'>
                        UNIVERSITAS PERTAMINA <br>
                        <p style='font-weight: normal'>
                            Komplek Universitas Pertamina <br>
                            Jalan Teuku Nyak Arief <br>
                            Simprug, Jakarta. 12220.
                        </p>
                    </th>
                    <th style='width: 5%'></th>
                    <th style='width: 5%'></th>
                    <th style='width: 10%'></th>
                    <th style='width: 5%'></th>
                    <th style='width: 5%'></th>
                    <th style='width: 10%'></th>
                    <th style='width: 10%'></th>
                </tr>
                <tr>
                    <td colspan='2' style='$table_border padding: 5px 0 5px 5px; vertical-align: top; height: 350'>
                        <p style='$font_bold'>
                            Purchase Order (PO) <br>
                            $procurement->name
                        </p>
                    </td>
                    <td colspan='4' style='$table_border padding: 5px 0 5px 5px; vertical-align: top'>
                        <p style='$font_bold'>Nomor SPMP/Tanggal</p>
                        $request->ref
                        " . date('d F Y') . "
                    </td>
                    <td colspan='3' rowspan='2' style='$table_border padding: 5px 0 5px 5px; vertical-align: top'>
                        <p style='$font_bold'>Alamat Pengiriman</p>
                        Universitas Pertamina <br>
                        Komplek Universitas Pertamina <br>
                        Jalan Teuku Nyak Arief <br>
                        Simprug, Jakarta. 12220.
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style='$table_border $font_bold padding: 5px 0 5px 5px;'>
                        Vendor: <br>
                        $vendor->name <br>
                        $vendor->address
                    </td>
                    <td colspan='4' style='$table_border padding: 5px 0 5px 5px;'>
                        <p style='$font_bold'>Nomor Vendor</p>
                        $vendor->reg_code
                        <br>
                        <p style='$font_bold'>NPWP Vendor</p>
                        $vendor->tin
                    </td>
                </tr>
                <tr>
                    <td colspan='3' style='$font_bold text-align: center; padding: 5px 0 5px 5px; vertical-align: top;'>
                        Ketentuan Pengiriman
                    </td>                        
                    <td colspan='6' rowspan='2' style='$table_border padding: 5px 0 5px 5px; vertical-align: top;'>
                        <p style='$font_bold'>Metode Pembayaran</p>
                        Transfer antar bank <br>
                        <p style='$font_bold'>Kurs Mata Uang</p>
                        Rupiah <br>
                        <p style='$font_bold'>Nama Bank</p>
                        $vendor->name_account<br>
                        <p style='$font_bold'>Nomor Rekening</p>
                        $vendor->bank_account<br>
                        <br>
                        <p style='$font_bold'>Ketentuan Pembayaran</p>
                        <br>
                        <p>
                            <span style='$font_bold'>Termin Pembayaran pertama </span>
                            sebesar 50% dari nilai harga PO, pembayaran dapat dilakukan setelah
                            PO (<i>Purchase Order</i>) ditandatangani kedua belah pihak. Dituangkan
                            dalam berita acara pembayaran termin pertama, <i>invoice</i>, kuitansi
                            bermaterai, surat permohonan pembayaran, faktur pajak, dan PO asli yang
                            sudah ditandatangani kedua belah pihak.
                        </p>
                        <br>
                        <p>
                            <span style='$font_bold'>Termin Pembayaran kedua </span>
                            sebesar 50% setelah seluruh barang diterima penuh 100% oleh pihak
                            Universitas Pertamina. Dituangkan dalam 
                            <span style='$font_bold'>Berita Acara Serah Terima Barang</span>, 
                            <i>invoice</i>, kuitansi bermaterai, surat permohonan pembayaran,
                            dan faktur pajak.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan='3' style='padding: 5px 0 5px 5px; vertical-align: top;'>
                        <ol>
                            <li>
                                Jangka pelaksanaan pekerjaan termasuk instalasi dan <i>training</i>
                                paling lambat selama $request->delivery_time " . strtolower($request->time_unit) . "
                                sejak PO ini diterima oleh Vendor
                            </li>
                            <li>
                                Vendor memberikan jaminan garansi atas kerusakan yang bukan
                                disebabkan oleh <i>User</i> (rusak pada saat pengiriman).
                            </li>
                            <li>
                                Vendor menjamin keaslian barang dan melampirkan perjanjian
                                keagenan dengan <i>principal</i> dan/atau surat terdaftar
                                sebagai agen/distributor pada departemen perdagangan atau
                                surat dukungan keagenan yang sah secara hukum.
                            </li>
                            <li>
                                Keterlambatan atas penyelesaian pekerjaan dikenakan denda
                                1‰ (permil) per hari, dan maskimal denda 5% (persen) 
                                (Rp" . number_format($final_price, '0', '', '.') . "). Denda tidak mengurangi kewajiban 
                                vendor terhadap pekerjaan.
                            </li>
                        </ol>
                    </td>
                </tr>
            </table>
            "
        );

        $mpdf->AddPageByArray(['type' => '']);

        $item_list = "";

        $counter = 1;
        foreach($items as $index => $item){
            if($index == count($request->item)){
                break;
            }
            if($item->id == $request->item[$index]){
                $item_list .= "
                    <tr>
                        <th style='$table_border'>$counter</th>
                        <td style='$table_border'>$item->name</td>
                        <td style='$table_border'>$item->specs</td>
                        <td style='$table_border'>$item->qty</td>
                        <td style='$table_border'>" . $request->item_unit[$index] . "</td>
                        <td style='$table_border'>Rp" . number_format($item->quotation_price, '0', '', '.') . "</td>
                        <td colspan='2' style='$table_border'>Rp" . number_format($item->quotation_price * $item->qty, '0', '', '.') . "</td>
                        <td style='$table_border'>$request->delivery_time " . strtolower($request->time_unit) . " sejak PO diterima vendor</td>
                    </tr>
                ";
            }
            $counter += 1;
        }

        $mpdf->WriteHTML(
            "
            <table style='$table_border $table_style text-align: center;'>
                <tr>
                    <th colspan='9' style='padding: 5px 0;'>Rincian Pembayaran</th>
                </tr>
                <tr>
                    <th style='$table_border font-size: 8pt; width: 5%; padding: 5px 2px;'>No</th>
                    <th style='$table_border font-size: 8pt; width: 10%; padding: 5px 2px;'>Nama Barang</th>
                    <th style='$table_border font-size: 8pt; width: 20%; padding: 5px 2px;'>Spesifikasi</th>
                    <th style='$table_border font-size: 8pt; width: 7%; padding: 5px 2px;'>Jumlah</th>
                    <th style='$table_border font-size: 8pt; width: 7%; padding: 5px 2px;'>Satuan</th>
                    <th style='$table_border font-size: 8pt; width: 15%; padding: 5px 2px;'>Harga Satuan</th>
                    <th colspan='2' style='$table_border font-size: 8pt; padding: 5px 2px;'>Total Harga</th>
                    <th style='$table_border font-size: 8pt; width: 15%; padding: 5px 2px;'>Waktu Pengiriman</th>
                </tr>
                $item_list
                <tr>
                    <th colspan='6' style='$table_border text-align: right; padding: 5px;'>Harga Total Penawaran</th>
                    <td style='border-bottom: 1px solid black; width: 5%; padding: 5px;'>Rp</td>
                    <td style='border-bottom: 1px solid black; width: 10%; padding: 5px;'></td>
                    <td style='text-align: right; border-bottom: 1px solid black; padding: 5px;'>Rp" . number_format($procurement_value, '0', '', '.') . "</td>
                </tr>
                <tr>
                    <th colspan='6' style='$table_border text-align: right; padding: 5px;'>Harga Total Negosiasi</th>
                    <td style='border-bottom: 1px solid black; width: 5%; padding: 5px;'>Rp</td>
                    <td style='border-bottom: 1px solid black; width: 10%; padding: 5px;'></td>
                    <td style='text-align: right; border-bottom: 1px solid black; padding: 5px;'>Rp" . number_format($total_discount, '0', '', '.') . "</td>
                </tr>
                <tr>
                    <th colspan='6' style='$table_border text-align: right; padding: 5px;'>Total Harga Pembelian</th>
                    <td style='border-bottom: 1px solid black; width: 5%; padding: 5px;'>Rp</td>
                    <td style='border-bottom: 1px solid black; width: 10%; padding: 5px;'></td>
                    <td style='text-align: right; border-bottom: 1px solid black; padding: 5px;'>Rp" . number_format($procurement_value - $total_discount, '0', '', '.') . "</td>
                </tr>
                <tr>
                    <th colspan='6' style='$table_border text-align: right; padding: 5px;'>Total Harga Akhir Pembelian Plus PPN 10%</th>
                    <th style='border-bottom: 1px solid black; width: 5%; padding: 5px;'>Rp</th>
                    <td style='border-bottom: 1px solid black; width: 10%; padding: 5px;'></td>
                    <th style='text-align: right; border-bottom: 1px solid black; padding: 5px;'>Rp" . number_format($final_price, '0', '', '.') . "</th>
                </tr>
                <tr>
                    <td colspan='9' style='$table_border padding: 5px; text-align: left;'>
                        Barang/Jasa dianggap sudah diserahkan apabila seluruh barang/unit telah diterima
                        di Universitas Pertamina, Jalan Teuku Nyak Arief, Simprug, Jakarta dan tertuang
                        dalam Berita Acara Serah Terima dengan Pengguna yang bersangkutan.
                    </td>
                </tr>
                <tr>
                    <td colspan='5' style='border-right: 1px solid black; text-align: left; vertical-align: top;'>
                        Kami menerima SPMP ini dan menyetujui ketentuan-ketentuan yang tercantum dalam SPMP ini. <br>
                        Tertanda: <br>
                    </td>
                    <td colspan='4' style='text-align: left; vertical-align: bottom'>Disetujui oleh:</td>
                </tr>
                <tr>
                    <td colspan='5' style='$font_bold border-right: 1px solid black; height: 200; text-align: left; vertical-align: top;'>
                        $vendor->name
                    </td>
                    <td colspan='4' style='vertical-align: top;'></td>
                </tr>
                <tr>
                    <td colspan='5' style='$table_border text-align: left;'>
                        Tanggal : 
                        <br>
                        Tempat : 
                    </td>
                    <td colspan='4' style='$table_border'></td>
                </tr>
            </table>
            "
        );

        return $mpdf->Output($doc_name, "I");
    }

    public function generateSpphForm($proc_id, $vendor_id){
        $vendor = \App\Models\Vendor::select('name')->where('id', '=', $vendor_id)->first();
        $procurement = \App\Models\Procurement::select('ref', 'name')->where('id', '=', $proc_id)->get()[0];
        $items = \App\Models\Item::join('quotations', 'quotations.item_sub_category', '=', 'items.sub_category')
            ->select('items.name', 'items.specs')
            ->where('items.procurement', '=', $proc_id)
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

    public function generateBappForm($proc_id){
        $procurement    = \App\Models\Procurement::select('id', 'ref', 'name')->where('id', '=', $proc_id)->first();
        $quotations     = \App\Models\Quotation::join('vendors', 'vendors.id', '=', 'quotations.vendor')
                            ->join('item_sub_categories', 'item_sub_categories.id', '=', 'quotations.item_sub_category')
                            ->join('item_categories', 'item_categories.id', '=', 'item_sub_categories.category')
                            ->select(
                                'quotations.*', 
                                'vendors.id AS vendor_id',
                                'vendors.name AS vendor_name', 
                                'item_categories.name AS cat_name', 
                                'item_sub_categories.name AS sub_name'
                                )
                            ->orderBy('vendors.name', 'ASC')
                            ->where('procurement', '=', $proc_id)
                            ->get();
        $vendors        = \App\Models\Vendor::join('quotations', 'quotations.vendor', '=', 'vendors.id')
                            ->select('vendors.id', 'vendors.name')
                            ->distinct()
                            ->where('quotations.procurement', '=', $proc_id)
                            ->orderBy('name')
                            ->get();
        $items          = \App\Models\Item::where('procurement', '=', $proc_id)->get();
        
        return view('procurement.documents.bapp.form', [
            'proc_id' => $proc_id,
            'procurement' => $procurement,
            'items' => $items,
            'quotations' => $quotations,
            'vendors' => $vendors,
        ]);
    } 

    public function generatePoForm($proc_id, $vendor_id){
        $procurement    = \App\Models\Procurement::select('name')->where('id', '=', $proc_id)->first();
        $items          = \App\Models\Item::join('quotations', 'quotations.item', '=', 'items.id')
                            ->select('items.*', 'quotations.vendor AS vendor')
                            ->where('quotations.vendor', '=', $vendor_id)
                            ->get();

        return view('procurement.documents.po.form', [
            'proc_id' => $proc_id,
            'vendor_id' => $vendor_id,
            'procurement' => $procurement,
            'items' => $items
        ]);
    }
}
