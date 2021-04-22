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
                    @if (($role == 'Wakil Rektor' And $unit[0]->name == 'Wakil Rektor 2') Or ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas') Or ($role == 'Manajer' And $unit[0]->name == 'Fungsi Pengadaan Barang dan Jasa'))
                        <button class="btn btn-primary" name="approve">Disposisi</button>
                    @else
                        <button class="btn btn-primary" name="approve">Approve</button>
                    @endif
                </form>
            </div>
        @endif
    </div>

    {{-- Unit List --}}
    <div class="card shadow p-4 mb-4">
        <h1 class="font-weight-bold">Daftar Barang yang diajukan</h1>
        <hr>
        <div class="table-responsive">
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">Spesifikasi</th>
                        <th class="text-center">Harga Satuan</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0 @endphp
                    @foreach ($unit_lists as $unit)
                        @php
                            $total += ($unit->price * $unit->qty)
                        @endphp
                        <tr>
                            <td class="align-baseline text-center" style="white-space: nowrap; width: 1%;">{{ $loop->iteration }}</td>
                            <td class="align-baseline text-left">{{ $unit->name }}</td>
                            <td class="align-baseline text-left">{{ $unit->specs }}</td>
                            <td class="align-baseline text-center">
                                <div class="d-flex justify-content-between">
                                    <span>Rp</span>
                                    <span>{{ number_format($unit->price, 2, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="align-baseline text-center">{{ $unit->qty }}</td>
                            <td class="align-baseline text-center">
                                <div class="d-flex justify-content-between">
                                    <span>Rp</span>
                                    <span>{{ number_format($unit->qty * $unit->price, 2, ',', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-primary">
                        <td colspan="3" class="text-right"><h3 class="font-weight-bold">Total</h3></td>
                        <td colspan="3">
                            <div class="d-flex justify-content-between">
                                <h3 class="font-weight-bold">Rp</h3>
                                <h3 class="font-weight-bold">{{ number_format($total, 2, ',', '.') }}</h3>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Logs --}}
    <div class="card shadow p-4 mb-4">
        <h1 class="font-weight-bold">Logs</h1>
        <hr>
        <table>
            <tbody>                
                @foreach ($log_dates as $index => $date)
                    @if ($index < 1 Or ($index > 1 And date('Y-m-d', strtotime($date[$index])) != date('Y-m-d', strtotime($date[$index-1]))))
                        <tr>
                            <td class="pb-2">
                                <span class="p-2 badge badge-pill badge-primary">
                                    {{ date('d F Y', strtotime($date->created_at)) }}
                                </span>
                            </td>
                        </tr>
                    @endif
                    @foreach ($logs as $log)
                        @if ($log->created_at == $date->created_at)
                            <tr>
                                <td class="pb-2">{{ date('H:i:s', strtotime($log->created_at)) }}</td>
                                <td class="pb-2">{{ $log->name }}</td>
                                <td class="pb-2">{{ $log->message }}</td>
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