<?php 

function dateIDN($date){
    $day = date('d', strtotime($date));
    (int) $n_month = date('n', strtotime($date));
    $year = date('Y', strtotime($date));

    $months = array(
        "Januari"   => 1,
        "Februari"  => 2,
        "Maret"     => 3,
        "April"     => 4,
        "Mei"       => 5,
        "Juni"      => 6,
        "Juli"      => 7,
        "Agustus"   => 8,
        "September" => 9,
        "Oktober"   => 10,
        "November"  => 11,
        "Desember"  => 12
    );

    $month = array_search($n_month, $months);

    return $day . " " . $month . " " . $year;
}

function futureDate(int $day){
    $date = dateIDN(date('Ymd', strtotime("+$day days")));
    return $date;
}

?>