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
                <label for="ref" class="form-label font-weight-bold">Prioritas</label>
                @if ($procurement->priority)
                    <input type="text" class="form-control-plaintext" id="ref" name="ref" placeholder="000/---/MEMO/---/yyyy" value="{{ $priority[0]->name }}" required>
                @else
                    <br>
                    <p class="p-2 badge badge-pill badge-danger">Not Available</p>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label for="ref" class="form-label font-weight-bold">PIC Pengadaan</label>
                @if ($procurement->pic)
                    <input type="text" class="form-control-plaintext" id="ref" name="ref" placeholder="000/---/MEMO/---/yyyy" value="{{ $pic[0]->name }}" required>
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
                    @elseif (($role == 'Wakil Rektor' And $unit[0]->name == 'Wakil Rektor 2') Or ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas'))
                        <button class="btn btn-primary" name="approve">Disposisi</button>
                    @else
                        <button class="btn btn-primary" name="approve">Approve</button>
                    @endif
                </form>
            </div>
        @endif
    </div>

    <nav class="nav nav-pills nav-justified mb-2">
        <a id="unitLists_btn" class="nav-link active" href="#">Daftar barang yang diajukan</a>
        <a id="logs_btn" class="nav-link" href="#">Logs</a>
    </nav>

    {{-- Unit List --}}
    <div id="unitLists" class="card shadow p-4 mb-4">
        @php $total = 0 @endphp
        @foreach ($items as $item)
            @php
                $total += ($item->price * $item->qty)
            @endphp
            <div class="row">
                <div class="col-md-auto"><h5 class="font-weight-bold">{{ str_pad($loop->iteration, strlen(count($items)), 0, STR_PAD_LEFT) }}</h5></div>
                <div class="col">
                    {{-- Item information --}}
                    <div class="row">
                        <div class="col">
                            <h5 class="font-weight-bold">{{ $item->name }}</h5>
                            <div class="unit-content" style="display: none;">
                                <h6 class="badge badge-pill <?= $item->category ? 'badge-primary' : 'badge-danger' ?>">
                                    @if ($item->category)
                                        {{ $item->category }}
                                    @else
                                        Kategori belum ditentukan
                                    @endif
                                </h6>
                                <h6>Spesifikasi:</h6>
                                <p>{{ $item->specs }}</p>
                                <div class="d-flex justify-content-center table-responsive">
                                    <table class="table w-75">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Harga Satuan</th>
                                                <th class="text-center" style="white-space: nowrap; width: 1%;">Jumlah</th>
                                                <th class="text-center">Total Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex justify-content-between">
                                                        <span>Rp</span>
                                                        <span>{{ number_format($item->price, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $item->qty }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-between">
                                                        <span>Rp</span>
                                                        <span>{{ number_format($item->qty * $item->price, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <a href="#" class="btn btn-sm btn-info quotation-btn">Quotation Available: 
                                @php
                                    $count = \App\Models\Quotation::where('item', '=', $item->id)->where('doc', '<>', NULL)->count();
                                @endphp
                                <span class="badge badge-light"><?= $count ?></span>
                            </a>
                            <a href="#" class="btn btn-sm btn-primary expand-unit-btn">Expand</a>
                            <span class="expanded-unit-btn" style="display: none;">
                                <a href="#" class="btn btn-sm btn-primary">Review</a>
                                <a href="#" class="btn btn-sm btn-danger close-unit-btn">&times;</a>
                            </span>
                        </div>   
                    </div>
                    {{-- Item Quotation --}}
                    <div class="row quotation-content" style="display: none">
                        <div class="col-12">
                            <hr>
                            <div class="row">
                                <div class="col">
                                    <h5 class="font-weight-bold">Daftar Vendor</h5>
                                </div>
                                <div class="col-md-auto">
                                    <a href="#" class="btn btn-sm btn-danger close-quotation-btn">&times;</a>
                                </div>                                
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 1%; white-space: nowrap;">#</th>
                                            <th class="text-center">Vendor Name</th>
                                            <th class="text-center">Quotation</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quotations as $quotation)
                                            @if ($quotation->item == $item->id)
                                                <tr>
                                                    <td class="text-right">{{ $loop->iteration }}</td>
                                                    <td>{{ $quotation->vendor_name }}</td>
                                                    <td class="text-center w-25">
                                                        @if ($quotation->doc != NULL)
                                                            <a href="">{{ $quotation->name }}</a>
                                                        @else
                                                            <span class="badge badge-danger p-2">Not Available</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center" style="width: 1%; white-space: nowrap;">
                                                        <a 
                                                            href="{{ Route('generate-spph-form', ['proc_id' => $procurement->id, 'vendor_id' => $quotation->vendor]) }}" 
                                                            target="_blank"
                                                            class="badge badge-primary mb-2 p-2">
                                                            Generate SPPH
                                                        </a>
                                                        <a href="" class="badge badge-warning text-dark mb-2 p-2">Buat PO</a>
                                                        <br>
                                                        <a href="" class="badge badge-success mb-2 p-2">Pemenang Tender</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>             
            </div>
            @if (!$loop->last)
                <hr>
            @endif
        @endforeach
    </div>

    {{-- Logs --}}
    <div id="logs" class="card shadow p-4 mb-4" style="display: none;">
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