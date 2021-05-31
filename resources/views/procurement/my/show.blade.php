@extends('layouts.main')

@section('page-title', $procurement->name)

@section('main-content')
<div class="container">
    <a href="{{ Route('home') }}" class="btn btn-sm btn-danger mb-3">Back</a>
    <h3 class="mb-2 font-weight-bold">Current Status: <span class="badge badge-pill badge-primary p-2">{{ $procurement->status_name }}</span></h3>
    <div class="d-flex justify-content-between align-items-start mb-2">
        <p>Last Update: @php echo date('d F Y - H:i:s', strtotime($procurement->updated_at)) @endphp</p>
        @if ((!strpos($procurement->approval_status, 'setuju') And ($role == 'Kaprodi' Or $role == 'Manajer')) Or ($role == 'Staf' And $unit[0]->name == 'Fungsi Pengadaan Barang dan Jasa'))
            <a href="{{ Route('edit-procurement', ['id' => $procurement->id]) }}" class="btn btn-warning font-weight-bold text-dark">Edit</a>
        @endif
    </div>

    {{-- Information --}}
    <div class="card shadow p-4 mb-4">                
        <div class="row g-3">
            <div class="col-md-6 mb-3">
                <label for="priority" class="form-label font-weight-bold">Prioritas</label>
                @if ($procurement->priority)
                    <input type="text" class="form-control-plaintext" id="priority" name="priority" value="{{ $priority[0]->name }}" required>
                @else
                    <br>
                    <p class="p-2 badge badge-pill badge-danger">Not Available</p>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label for="pic" class="form-label font-weight-bold">PIC Pengadaan</label>
                @if ($procurement->pic)
                    <input type="text" class="form-control-plaintext" id="pic" name="pic" value="{{ $pic[0]->name }}" required>
                @else
                    <br>
                    <p class="p-2 badge badge-pill badge-danger">Not Available</p>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label for="ref" class="form-label font-weight-bold">Referensi</label>
                <input type="text" class="form-control-plaintext" id="ref" name="ref" placeholder="000/---/MEMO/---/yyyy" value="{{ $procurement->ref }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label font-weight-bold">Nama Pengadaan</label>
                <input type="text" class="form-control-plaintext" id="name" name="name" value="{{ $procurement->name }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="value" class="form-label font-weight-bold">OE</label>
                <input type="text" class="form-control-plaintext" id="value" name="value" value="@php echo 'Rp' . number_format($procurement->value, 2, ',', '.'); @endphp" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="date" class="form-label font-weight-bold">Tanggal Pengajuan</label>
                <input type="text" class="form-control-plaintext" id="date" name="date" value="@php echo date('d F Y - H:i:s', strtotime($procurement->created_at)) @endphp" required readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label for="mechanism" class="form-label font-weight-bold">Mekanisme</label>
                <input type="text" class="form-control-plaintext" id="mechanism" name="mechanism" value="{{ $procurement->mech_name }}" required readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label for="category" class="form-label font-weight-bold">Kategori</label>
                @if ($procurement->category)
                    <input type="text" class="form-control-plaintext" id="category" name="category" value="{{ $category[0]->name }}" required readonly>
                @else
                    <br>
                    <p class="p-2 badge badge-pill badge-danger">Not Available</p>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label for="tor" class="form-label font-weight-bold">ToR</label>
                @if ($tor['available'])
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <td class="text-break w-75"><a href="{{ Route('view-document', ['id' => $documents[$tor['index']]->id]) }}" target="_blank">{{ $documents[$tor['index']]->name }}</a></td>
                                <td class="w-25 text-center align-top"><a class="btn btn-sm btn-primary" href="{{ Route('view-document', ['id' => $documents[$tor['index']]->id]) }}" target="_blank">Download</a></td>
                            </tr>
                        </tbody>
                    </table>
                @else
                <br>
                <p class="p-2 badge badge-pill badge-danger">Not Available</p>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label for="spec" class="form-label font-weight-bold">Specs</label>
                @if ($spec['available'])
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <td class="text-break w-75"><a href="{{ Route('view-document', ['id' => $documents[$spec['index']]->id]) }}" target="_blank">{{ $documents[$spec['index']]->name }}</a></td>
                                <td class="w-25 text-center align-top"><a class="btn btn-sm btn-primary" href="{{ Route('view-document', ['id' => $documents[$spec['index']]->id]) }}" target="_blank">Download</a></td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <br>
                    <p class="p-2 badge badge-pill badge-danger">Not Available</p>
                @endif                    
            </div>
        </div>
        @if ($procurement->approver == Auth::user()->role Or ($role == 'Staf' And $unit[0]->name == 'Fungsi Pengadaan Barang dan Jasa'))
            <div class="d-flex justify-content-center">
                <form action="{{ Route('update-procurement', ['id' => $procurement->id]) }}" method="post">
                    @csrf
                    @if ($role == 'Manajer' And $unit[0]->name == 'Fungsi Pengadaan Barang dan Jasa')
                        @php
                            $pic_list = \App\Models\User::join('units', 'units.id', '=', 'users.unit')
                                            ->select('users.id', 'users.name')
                                            ->where('units.name', 'LIKE', '%Pengadaan%')
                                            ->orderBy('users.role', 'ASC')
                                            ->get()
                        @endphp
                        <div class="form-group row align-items-center">
                            <label class="col-md-auto" for="pic">PIC:</label>
                            <select class="form-control col mb-3" name="pic" id="pic" required>
                                <option></option>
                                @foreach ($pic_list as $pic)
                                    <option value="{{ $pic->id }}">{{ $pic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row justify-content-center">
                            <button class="btn btn-primary" name="assign">Kirim ke Staf</button>
                        </div>
                    @elseif (($role == 'Wakil Rektor' And $unit[0]->name == 'Bidang Keuangan dan Sumber Daya Organisasi Universitas Pertamina') Or ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas'))
                        <button class="btn btn-primary" name="approve">Disposisi</button>
                    @else
                        <button class="btn btn-primary" name="approve">Approve</button>
                    @endif
                </form>
            </div>
        @endif
    </div>

    <nav class="nav nav-pills nav-justified mb-2">
        <a id="item-tab" class="tab-active nav-link active" href="#">Daftar barang yang diajukan</a>
        <a id="bidder-tab" class="nav-link" href="#">Bidder List</a>
        <a id="log-tab" class="nav-link" href="#">Logs</a>
    </nav>

    {{-- Unit List --}}
    <div id="item-content" class="content-active card shadow p-4 mb-4">
        <table class="table table-hover table-bordered text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Barang</th>
                    <th style="width: 20%;">Harga Satuan (oe)</th>
                    <th style="white-space: nowrap; width: 1%;">Jumlah</th>
                    <th style="width: 20%;">Total Harga</th>
                    <th style="white-space: nowrap; width: 1%;">#</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0 @endphp
                @foreach ($items as $item)
                    @php $total += $item->oe * $item->qty @endphp
                    <tr>
                        <th style="white-space: nowrap; width: 1%;">{{ str_pad($loop->iteration, strlen(count($items)), 0, STR_PAD_LEFT) }}</th>
                        <td class="text-left">
                            <p class="mb-2">{{ $item->name }}</p>
                            <div class="more-info" style="display: none;">
                                <p>Spesifikasi: <br>{{ $item->specs }}</p>
                                <div class="row">
                                    <div class="col">
                                        <p class="font-weight-bold">Harga Penawaran:</p>
                                        <div class="d-flex justify-content-between">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->quotation_price, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <p class="font-weight-bold">Harga Final:</p>
                                        <div class="d-flex justify-content-between">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->nego_price, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between">
                                <span>Rp</span>
                                <span>{{ number_format($item->oe, 2, ',', '.') }}</span>
                            </div>
                        </td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td>
                            <div class="d-flex justify-content-between">
                                <span>Rp</span>
                                <span>{{ number_format($item->qty * $item->oe, 2, ',', '.') }}</span>
                            </div>
                        </td>
                        <td>
                            <a href="" class="more-info-btn"><i class="fas fa-fw fa-caret-square-down"></i></a>
                        </td>
                    </tr>
                @endforeach
                    <tr>
                        <td colspan="2" class="text-right font-weight-bold">Total (OE)</td>
                        <td colspan="3" class="font-weight-bold">
                            <div class="d-flex justify-content-between">
                                <span>Rp</span>
                                <span>{{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>

    {{-- Bidder List --}}
    <div id="bidder-content" class="card shadow p-4 mb-4" style="display: none;">
        @foreach ($items as $item)
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="font-weight-bold" style="font-size: 15pt;">{{ $item->name }}</h3>
                @if ($item->category)
                    <a href="" class="add-vendor-btn btn btn-sm btn-primary">Daftarkan Vendor</a>
                @else
                    <form action="{{ Route('add-item-category', ['id' => $item->id]) }}" method="post">
                        @csrf
                        <div class="d-flex justify-content-around align-items-center">
                            <select name="category" class="add-item-category form-control mx-2" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($item_categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <select name="sub_category" class="add-item-sub-category form-control mx-2" required>
                                <option value="">Pilih Sub Kategori</option>
                            </select>
                            <button class="btn btn-primary btn-sm">Submit</button>
                        </div>
                    </form>
                @endif
            </div>
            @if ($item->category)
                <div class="add-vendor" style="display: none;">
                    <form action="" class="add-vendor-form">
                        <div class="d-flex justify-content-start align-items-center mb-3"> 
                            <input type="hidden" name="procurement_id" value="{{ $procurement->id }}">
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <select name="vendor" id="vendor" class="form-control mr-3" style="width: 30%" required>
                                <option value="" selected>Pilih vendor yang akan ditambahkan</option>
                                @foreach ($vendors as $vendor)
                                    @if ($vendor->category == $item->category And $vendor->sub_category == $item->sub_category)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button name="add-vendor" class="btn btn-sm btn-primary mr-2">Add Vendor</button>
                            <a href="" class="add-vendor-close btn btn-sm btn-danger">&times;</a>
                        </div>
                    </form>
                </div>
            @endif
            <table class="{{ "quotation-$item->id" }} table table-hover table-bordered text-center">
                <thead>
                    <tr>
                        <th style="white-space: nowrap; width: 1%;">#</th>
                        <th>Nama Vendor</th>
                        <th class="w-25">Berkas</th>
                        @if ($role == 'Staf')
                            <th class="w-25">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1 @endphp
                    @foreach ($quotations as $quotation)
                        @if ($quotation->item == $item->id)
                            <tr>
                                <th>{{ $counter }}</th>
                                <td>{{ $quotation->vendor_name }}</td>
                                <td class="text-left">
                                    @if (count($vendor_docs))
                                        @foreach ($vendor_docs as $doc)
                                            @if ($doc->type == 'spph')
                                                SPPH: <br>
                                                <a href="{{ Route('view-document-vendor', ['id' => $doc->id, 'table' => 'vendor_docs']) }}" target="_blank">{{ $doc->name . ".pdf"}}</a>
                                            @endif
                                        @endforeach
                                    @else
                                        Tidak ada berkas
                                    @endif
                                </td>
                                @if ($role == 'Staf')
                                    <td>
                                        <div class="d-flex justify-content-around">
                                            @if (!count($vendor_docs))
                                                <a href="{{ Route('generate-spph-form', ['proc_id' => $procurement->id, 'vendor_id' => $quotation->vendor]) }}" class="btn btn-sm btn-primary">Generate SPPH</a>
                                                <a href="" class="upload-spph btn btn-sm btn-success">Upload SPPH</a>
                                            @endif
                                        </div>
                                        <form action="{{ Route('upload', ['name' => 'spph']) }}" method="post" enctype="multipart/form-data" class="spph-form mt-2" style="display: none">
                                            @csrf
                                            <input type="hidden" name="procurement" value="{{ $procurement->id }}">
                                            <input type="hidden" name="vendor" value="{{ $quotation->vendor }}">
                                            <input type="hidden" name="item" value="{{ $item->id }}">
                                            <input type="text" id="spph_ref" name="spph_ref" class="form-control mb-2" placeholder="Nomor Surat" required>
                                            <input type="file" name="spph" id="spph" class="form-control-file mb-2" required>
                                            <button class="btn btn-sm btn-primary">Upload</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endif
                        @php $counter += 1 @endphp
                    @endforeach
                </tbody>
            </table>
            <hr>
        @endforeach
    </div>

    {{-- Logs --}}
    <div id="log-content" class="card shadow p-4 mb-4" style="display: none;">
        <table>
            <tbody>                
                @foreach ($log_dates as $index => $date)
                    @if ($index < 1 Or ($index > 1 And date('Y-m-d', strtotime($date[$index])) != date('Y-m-d', strtotime($date[$index-1]))))
                        <tr>
                            <td class="text-center pb-2">
                                <span class="p-2 badge badge-pill badge-primary">
                                    {{ date('d F Y', strtotime($date->created_at)) }}
                                </span>
                            </td>
                        </tr>
                    @endif
                    @foreach ($logs as $log)
                        @if ($log->created_at == $date->created_at)
                            <tr>
                                <td class="text-center align-baseline pb-2">{{ date('H:i:s', strtotime($log->created_at)) }}</td>
                                <td class="pb-2">
                                    {{ $log->name }}
                                    <br>
                                    {{ $log->message }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset("js/my-procurement-show.js") }}"></script>
@endpush