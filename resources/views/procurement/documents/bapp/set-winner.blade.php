@extends('layouts.main')

@section('page-title', 'Penentuan Pemenang Tender')

@section('main-content')
    <div class="container">
        <div class="card shadow p-4 mb-4">            
            <h1 class="font-weight-bold text-center">Penetapan Pemenang Tender</h1>
            <hr>
            <h2 class="font-weight-bold" style="font-size: 24px">{{ $quotation->vendor_name }}</h2>
            <table class="mb-3">
                <tr>
                    <th style="white-space: nowrap; width: 1%; padding-right: 16px">Dokumen Penawaran</th>
                    <th style="white-space: nowrap; width: 1%">:</th>
                    <td>
                        <a href="{{ Route('view-document-vendor', ['id' => $quotation->id, 'table' => 'quotations']) }}" target="_blank">{{ $quotation->name }}</a>
                    </td>
                </tr>
                <tr>
                    <th style="white-space: nowrap; width: 1%">Diunggah tanggal</th>
                    <th style="white-space: nowrap; width: 1%">:</th>
                    <td>{{ dateIDN($quotation->created_at) }}</td>
                </tr>
            </table>
            <form action="{{ Route('set-winner') }}" method="post">
                @csrf
                <input type="hidden" name="vendor" value="{{ $quotation->vendor }}">
                <input type="hidden" name="procurement" value="{{ $quotation->procurement }}">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-center align-items-top">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th style="width: 20%">Harga Penawaran</th>
                                <th style="width: 20%">Harga Final</th>
                                <th style="width: 20%">Diskon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td rowspan="2">
                                        <input type="hidden" name="items[]" value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </td>
                                    <td>
                                        <input type="number" name="bidder_price[]" class="bidder-price form-control" placeholder="Harga Penawaran" required>
                                    </td>
                                    <td>
                                        <input type="number" name="final_price[]" class="final-price form-control" placeholder="Harga Final" required>
                                    </td>
                                    <td rowspan="2">
                                        <div class="d-flex justify-content-between">
                                            <span>Rp</span>
                                            <span class="discount-price"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <textarea 
                                            name="vendor_specs[]" 
                                            rows="5" 
                                            placeholder="Spesifikasi dari vendor" 
                                            style="resize: none"
                                            class="form-control"
                                            required
                                        ></textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary" id="set-winner-btn">Kirim</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('js')
    <script src="{{ asset('/js/set-winner.js') }}"></script>
@endpush