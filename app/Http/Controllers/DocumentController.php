<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function generateSpph(Request $request){
        $procurement = \App\Models\Procurement::where('id', '=', $request->proc_id)->get()[0];
        $vendor = \App\Models\Vendor::where('id', '=', $request->vendor_id)->get()[0];
        $items = \App\Models\Quotation::join('items', 'items.id', '=', 'quotations.item')
            ->select('items.name', 'items.specs')
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
        $font_size_footer = "font-size: 7pt;";
        $text_justify = "text-align: justify; text-justify: inter-word;";

        $mpdf = new \Mpdf\Mpdf([
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ]);

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
                    <td>" . $request->ref . $request->date . "</td>
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

        $doc_name = "SPPH_" . $request->vendor . "_" . $procurement->name . "_" . date('Ymd-His') . ".pdf";

        return $mpdf->Output($doc_name, "I");
    }
}
