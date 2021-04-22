@php
    $filename = $data['name'];
    $type = $data['doc_type'];
    header("Content-length: " . strlen($data['doc']));
    header("Content-type: $type");
    header("Content-Disposition:attachment;filename=$filename");

    echo $data['doc'];
@endphp