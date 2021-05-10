@php
    $mpdf = new \Mpdf\Mpdf();

    $mpdf->WriteHTML(
        '<p>Jakarta, ' . date('d F Y') .  '</p>'
    );

    return $mpdf->Output('Test.pdf', "I");
@endphp