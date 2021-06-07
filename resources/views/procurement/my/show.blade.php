@extends('layouts.main')

@section('page-title', $procurement->name)

@section('main-content')
<div class="container">
    <a href="{{ Route('home') }}" class="btn btn-sm btn-danger mb-3">Back</a>
    <h3 class="mb-2 font-weight-bold">Current Status: <span class="badge badge-pill badge-primary p-2">{{ $procurement->status_name }}</span></h3>
    <div class="d-flex justify-content-between align-items-start mb-2">
        <p>Last Update: @php echo date('d F Y - H:i:s', strtotime($procurement->updated_at)) @endphp</p>
        @if ($role == 'Staf' And $procurement->pic != NULL Or ($procurement->applicant == Auth::user()->id And $procurement->editable))
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
                @foreach ($documents as $doc)
                    @if ($doc->type == 'tor')
                        <div class="d-flex justify-content-between align-items-baseline">
                            <a href="{{ Route('view-document', ['id' => $doc->id]) }}" target="_blank" id="tor">{{ $doc->name }}</a>
                            <a class="btn btn-sm btn-primary" href="{{ Route('view-document', ['id' => $doc->id]) }}" target="_blank">Download</a>
                        </div>                        
                    @else
                        <br>
                        <p class="p-2 badge badge-pill badge-danger" id="tor">Not Available</p>                        
                    @endif
                @endforeach
            </div>
            <div class="col-md-6 mb-3">
                <label for="spec" class="form-label font-weight-bold">Specs</label>
                @foreach ($documents as $doc)
                    @if ($doc->type == 'spec')
                        <div class="d-flex justify-content-between align-items-start">
                            <a href="{{ Route('view-document', ['id' => $doc->id]) }}" target="_blank" id="spec">{{ $doc->name }}</a>
                            <a class="btn btn-sm btn-primary" href="{{ Route('view-document', ['id' => $doc->id]) }}" target="_blank">Download</a>
                        </div>
                    @else
                        <br>
                        <p class="p-2 badge badge-pill badge-danger" id="spec">Not Available</p>
                    @endif
                @endforeach                    
            </div>
            <div class="col-md-6 mb-3">
                <label for="bapp" class="form-label font-weight-bold">BAPP - Berita Acara Penjunjukan Pemenang</label>
                @foreach ($documents as $doc)
                    @if ($doc->type == 'bapp')
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between">
                                <a href="{{ Route('view-document', ['id' => $doc->id]) }}" target="_blank" id="bapp">{{ $doc->name }}</a>
                                <a class="btn btn-sm btn-primary" href="{{ Route('view-document', ['id' => $doc->id]) }}" target="_blank">Download</a>
                            </div>
                        </div>
                    @else
                        <br>
                        <p class="p-2 badge badge-pill badge-danger" id="bapp">Not Available</p>
                    @endif
                @endforeach
            </div>
            <div class="col-md-6 mb-3">
                <label for="bast" class="form-label font-weight-bold">BAST - Berita Acara Serah Terima</label>
                @foreach ($documents as $doc)
                    @if ($doc->type == 'bast')
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between">
                                <a href="{{ Route('view-document', ['id' => $doc->id]) }}" target="_blank" id="bast">{{ $doc->name }}</a>
                                <a class="btn btn-sm btn-primary" href="{{ Route('view-document', ['id' => $doc->id]) }}" target="_blank">Download</a>
                            </div>
                        </div>
                    @else
                        <br>
                        <p class="p-2 badge badge-pill badge-danger" id="bast">Not Available</p>
                    @endif
                @endforeach
            </div>
        </div>
        @if ($procurement->approver == Auth::user()->role)
            <div class="d-flex justify-content-center">
                <form action="{{ Route('update-procurement', ['id' => $procurement->id]) }}" method="post">
                    @csrf
                    @if ($role == 'Manajer' And $unit->name == 'Fungsi Pengadaan Barang dan Jasa')
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
                    @elseif (($role == 'Wakil Rektor' And $unit->name == 'Bidang Keuangan dan Sumber Daya Organisasi Universitas Pertamina') Or ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas'))
                        <button class="btn btn-primary" name="approve">Disposisi</button>
                    @elseif ($role != 'Staf')
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
        {{-- Count uncategorized items --}}
        @php $counter = 0 @endphp
        @foreach ($items as $item)
            @if ($item->category == NULL) @php $counter += 1 @endphp @endif
        @endforeach
        {{-- End of count uncategorized items --}}

        {{-- Show uncategorized items --}}
        @if($counter)
            <h2 class="font-weight-bold" style="font-size: 24px;">Daftar barang/unit yang belum dikategorikan</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap; width: 1%;">#</th>
                            <th>Nama Barang</th>
                            <th style="width: 50%">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $counter = 1 @endphp
                        @foreach ($items as $item)
                            @if ($item->category == NULL)
                                <tr>
                                    <th>{{ $counter }}</th>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <a href="" class="add-item-category-btn btn btn-sm btn-primary">
                                            Tambahkan Kategori
                                        </a>
                                        <div class="add-item-category-form" style="display: none">
                                            <hr>
                                            <form action="{{ Route('add-item-category', ['id' => $item->id]) }}" method="post">
                                                @csrf
                                                <div class="row mb-2">
                                                    <div class="col-6">
                                                        <select name="category" class="add-item-category form-control" required>
                                                            <option value="">Pilih Kategori</option>
                                                            @foreach ($item_categories as $item)
                                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <select name="sub_category" class="add-item-sub-category form-control" required>
                                                            <option value="">Pilih Sub Kategori</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-primary">Kirim</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @php $counter += 1 @endphp
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        {{-- End of uncategorized items --}}

        {{-- Show items value breakdown --}}
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
                                        <p class="font-weight-bold badge badge-warning text-dark p-2">Harga Penawaran:</p>
                                        <div class="d-flex justify-content-between badge badge-warning text-dark p-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->quotation_price, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <p class="font-weight-bold badge badge-success p-2">Diskon:</p>
                                        <div class="d-flex justify-content-between badge badge-success p-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->discount, 2, ',', '.') }}</span>
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
        {{-- End of items value breakdown --}}
    </div>

    {{-- Bidder List --}}
    <div id="bidder-content" class="card shadow p-4 mb-4" style="display: none;">
        {{-- Show all categories that have items --}}
        @foreach ($registered_item as $category)
            <h2 class="font-weight-bold" style="font-size: 24px;">{{ $category->cat_name }}</h2>
            {{-- Show all sub categories that have items --}}
            @foreach ($registered_item as $sub_category)
                @if ($sub_category->cat_id == $category->cat_id)
                <div class="d-flex justify-content-between ml-5 mb-2">
                    <h3 class="font-weight-bold" style="font-size: 18px;">{{ $sub_category->sub_name }}</h3>
                    <a href="" class="add-vendor-btn btn btn-sm btn-primary">Daftarkan Vendor</a>
                </div>
                {{-- Field to add new vendor --}}
                <div class="add-vendor" style="display: none;">
                    <form action="{{ Route('add-item-vendor') }}" method="POST" class="add-vendor-form">
                        @csrf
                        <div class="d-flex justify-content-start align-items-center mb-3"> 
                            <input type="hidden" name="procurement_id" value="{{ $procurement->id }}">
                            <input type="hidden" name="sub_cat" value="{{ $sub_category->sub_id }}">
                            <select name="vendor" id="vendor" class="form-control mr-3" style="width: 30%" required>
                                <option value="" selected>Pilih vendor yang akan ditambahkan</option>
                                @foreach ($vendors as $vendor)
                                    {{-- Check if there is quotation and vendor exist --}}
                                    @php $vendorExist = false @endphp
                                    @if (count($quotations))
                                        @foreach ($quotations as $quotation)
                                            @if ($vendor->id == $quotation->vendor And $quotation->item_sub_category == $sub_category->sub_id) @php $vendorExist = true; break; @endphp @endif
                                        @endforeach
                                    @endif
                                    @if ($vendor->sub_category == $sub_category->sub_id And !$vendorExist)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button name="add-vendor" class="btn btn-sm btn-primary mr-2">Add Vendor</button>
                            <a href="" class="add-vendor-close btn btn-sm btn-danger">&times;</a>
                        </div>
                    </form>
                </div>
                {{-- Show all vendors registered to correspond sub category --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap; width: 1%;">#</th>
                                <th>Nama Vendor</th>
                                <th class="w-25">Berkas</th>
                                @if (($role == 'Staf' And $procurement->pic != NULL) Or (Auth::user()->id == $procurement->applicant))
                                    <th class="w-25">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1 @endphp
                            @foreach ($quotations as $quotation)
                                @if ($quotation->item_sub_category == $sub_category->sub_id)
                                    <tr>
                                        <th style="{{ $quotation->winner ? "background-color: #a6ffad" : "" }}">{{ $counter }}</th>
                                        <td>{{ $quotation->vendor_name }}</td>
                                        {{-- Show available documents on correspond vendor --}}
                                        <td class="text-left">
                                            @php $doc_count = 0 @endphp
                                            {{-- Count for available documents --}}
                                            @foreach ($vendor_docs as $doc)
                                                @if ($doc->item == $quotation->item)
                                                    @php $doc_count += 1 @endphp
                                                @endif
                                            @endforeach
                                            {{-- If documents exist, then show badge for each documents --}}
                                            @if ($doc_count)
                                                <div class="d-flex justify-content-center">
                                                    {{-- Show "SPPH" document badge --}}
                                                    @foreach ($vendor_docs as $doc)
                                                        @if ($doc->vendor == $quotation->vendor And $doc->item == $quotation->item)
                                                            @if ($doc->type == 'spph')
                                                                <span class="badge badge-pill badge-primary mx-2">SPPH</span>
                                                            @endif
                                                        @endif
                                                    @endforeach

                                                    {{-- Show "Quotation" document badge --}}
                                                    @if (strlen($quotation->name))
                                                        <span class="badge badge-pill badge-primary mx-2">Penawaran</span>
                                                    @endif

                                                    {{-- Show "PO" document badge --}}
                                                    @foreach ($vendor_docs as $doc)
                                                        @if ($doc->vendor == $quotation->vendor And $doc->item == $quotation->item)
                                                            @if ($doc->type == 'po')
                                                                <span class="badge badge-pill badge-primary mx-2">PO</span>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="more-document" style="display: none">
                                                    <hr>
                                                    {{-- Show "SPPH" document --}}
                                                    @foreach ($vendor_docs as $doc)
                                                        @if ($doc->vendor == $quotation->vendor And $doc->item == $quotation->item)
                                                            @if ($doc->type == 'spph')
                                                                SPPH: <br>
                                                                <a href="{{ Route('view-document-vendor', ['id' => $doc->id, 'table' => 'vendor_docs']) }}" target="_blank">{{ $doc->name }}</a>
                                                                <br><br>
                                                            @endif
                                                        @endif
                                                    @endforeach

                                                    {{-- Show "Quotation" document --}}
                                                    @if (strlen($quotation->name))
                                                        Penawaran: <br>
                                                        <a href="{{ Route('view-document-vendor', ['id' => $quotation->id, 'table' => 'vendor_docs']) }}" target="_blank">{{ $quotation->name }}</a>
                                                        <br><br>
                                                    @endif

                                                    {{-- Show "PO" document --}}
                                                    @foreach ($vendor_docs as $doc)
                                                        @if ($doc->vendor == $quotation->vendor And $doc->item == $quotation->item)
                                                            @if ($doc->type == 'po')
                                                                PO: <br>
                                                                <a href="{{ Route('view-document-vendor', ['id' => $doc->id, 'table' => 'vendor_docs']) }}" target="_blank">{{ $doc->name }}</a>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            {{-- If no document exist, show the following badge --}}
                                            @else
                                                <div class="d-flex justify-content-center">
                                                    <span class="badge badge-danger">Tidak ada berkas</span>
                                                </div>
                                            @endif
                                        </td>
                                        {{-- End of available documents on correspons vendor --}}
                                        @if (($role == 'Staf' And $procurement->pic != NULL) Or (Auth::user()->id == $procurement->applicant))
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <a href="" class="more-action-btn btn btn-sm btn-primary mx-2">Detail</a>
                                                    {{-- Find any document related to the vendor --}}
                                                    @php $documentExist = false @endphp
                                                    @foreach ($vendor_docs as $doc)
                                                        @if ($doc->item == $quotation->item And $doc->vendor == $quotation->vendor) 
                                                            @php $documentExist = true; break; @endphp
                                                        @endif
                                                    @endforeach
                                                    {{-- If no document exist, then show button to delete vendor --}}
                                                    @if (!$documentExist)
                                                        <form action="{{ Route('delete-item-vendor') }}" method="post" class="mx-2">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $quotation->id }}">
                                                            <input type="hidden" name="procurement_id" value="{{ $procurement->id }}">
                                                            <button class="btn btn-sm btn-danger">Hapus Vendor</button>
                                                        </form>
                                                    @endif
                                                </div>
                                                <div class="document-action mt-2" style="display: none">
                                                    <div class="d-flex justify-content-around">
                                                        {{-- Find for SPPH and PO --}}
                                                        @php $spphExist = false; $poExist = false @endphp
                                                        @if (count($vendor_docs))
                                                            @foreach ($vendor_docs as $doc)
                                                                @if ($doc->vendor == $quotation->vendor And $doc->item == $quotation->item)
                                                                    @if ($doc->type == 'spph') @php $spphExist = true @endphp @endif
                                                                    @if ($doc->type == 'po') @php $poExist = true @endphp @endif
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        {{-- End of find for SPPH and PO --}}

                                                        {{-- Count vendor for each item and look for the winner --}}
                                                        @php $quotation_counter = 0; $winner_available = false; @endphp
                                                        @foreach ($quotations as $item)
                                                            @if ($item->item_sub_category == $sub_category->sub_id) @php $quotation_counter += 1 @endphp @endif

                                                            @if ($item->winner) @php $winner_available = true @endphp @endif
                                                        @endforeach
                                                        {{-- End of count vendor for each item and look for the winner --}}

                                                        {{-- If SPPH not exist, then show button to upload SPPH --}}
                                                        @if (!$spphExist)
                                                            {{-- If vendor is below minimum, do not allow to create SPPH --}}
                                                            @if ($quotation_counter >= 5)
                                                                <a href="{{ Route('generate-spph-form', ['proc_id' => $procurement->id, 'vendor_id' => $quotation->vendor]) }}" class="btn btn-sm btn-primary">Generate SPPH</a>
                                                                <a href="" class="upload-spph btn btn-sm btn-success">Upload SPPH</a>                                                        
                                                            @else
                                                                <span class="badge badge-danger">Jumlah vendor masih di bawah minimum (5)</span>                                                        
                                                            @endif
                                                        @endif

                                                        {{-- If SPPH exist and no quoation uploaded, then show button to upload quotation --}}
                                                        @if ($spphExist And !strlen($quotation->name))
                                                            <a href="" class="upload-quotation btn btn-sm btn-primary">Unggah Penawaran</a>
                                                        @endif

                                                        {{-- If winner is not set --}}
                                                        @if (!$winner_available)
                                                            {{-- If quotation is available, then show button to choose the winner --}}
                                                            @if ($quotation->doc !== NULL)
                                                                <a href="" class="set-winner btn btn-sm btn-primary">Pemenang Tender</a>
                                                            @endif
                                                        @endif
                                            
                                                        {{-- If PO not exist and quotation declared as winner --}}
                                                        @if (!$poExist And $quotation->winner)
                                                            <a href="{{ Route('generate-po-form', ['proc_id' => $procurement->id, 'vendor_id' => $quotation->vendor]) }}" class="btn btn-sm btn-primary">Generate PO</a>
                                                            <a href="" class="upload-po btn btn-sm btn-success">Unggah PO</a>
                                                        @endif
                                                    </div>
                                                    {{-- Form - Upload SPPH --}}
                                                    @if (!$spphExist)                                                        
                                                        <form action="{{ Route('upload', ['name' => 'spph']) }}" method="post" enctype="multipart/form-data" class="spph-form mt-2" style="display: none">
                                                            @csrf
                                                            <input type="hidden" name="procurement" value="{{ $procurement->id }}">
                                                            <input type="hidden" name="vendor" value="{{ $quotation->vendor }}">
                                                            <input type="hidden" name="sub_category" value="{{ $sub_category->sub_id }}">
                                                            <input type="text" id="ref" name="ref" class="form-control mb-2" placeholder="Nomor Surat" required>
                                                            <input type="file" name="spph" id="spph" class="form-control-file mb-2" accept="application/pdf" required>
                                                            <button class="btn btn-sm btn-primary">Upload</button>
                                                        </form>
                                                    @endif

                                                    @if ($spphExist And !strlen($quotation->name))                                                        
                                                        {{-- Form - Upload Quotation --}}
                                                        <form action="{{ Route('upload', ['name' => 'quotation']) }}" method="post" enctype="multipart/form-data" class="quotation-form mt-2" style="display: none">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $quotation->id }}">
                                                            <input type="text" id="ref" name="ref" class="form-control mb-2" placeholder="Nomor Surat" required>
                                                            <input type="file" name="quotation" id="quotation" class="form-control-file mb-2" accept="application/pdf" required>
                                                            <button class="btn btn-sm btn-primary">Upload</button>
                                                        </form>
                                                    @endif

                                                    @if (!$winner_available)                                                        
                                                        {{-- Form - Set Tender Winner --}}
                                                        <form action="{{ Route('set-winner') }}" method="post" class="set-winner-form mt-2" style="display: none">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $quotation->id }}">
                                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                            <input type="hidden" name="procurement_id" value="{{ $procurement->id }}">
                                                            <input type="number" name="offering_price" placeholder="Harga Penawaran" class="form-control mt-2">
                                                            <input type="number" name="discount" placeholder="Harga Final" class="form-control mt-2">
                                                            <button class="btn btn-sm btn-success mt-2">Kirim</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                    @php $counter += 1 @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            @endforeach
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