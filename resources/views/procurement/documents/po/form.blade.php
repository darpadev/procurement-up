@extends('layouts.main')

@section('page-title', 'Request PO')

@section('main-content')
    <div class="container">
        <div class="card shadow p-4 mb-4">     
            <h1 class="font-weight-bold text-center">Formulir Pembuatan Dokumen <i>Purchase Order</i> (PO)</h1> 
            <hr>      
            <form action="{{ Route('generate-po') }}" method="post" target="_blank">
                @csrf
                <input type="number" name="proc_id" hidden value="{{ $proc_id }}">
                <input type="number" name="vendor_id" hidden value="{{ $vendor_id }}">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="ref" class="form-label font-weight-bold">Nomor Surat<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ref" name="ref" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="procurement_name" class="form-label font-weight-bold">Nama Pengadaan</label>
                        <input type="text" id="procurement_name" class="form-control" value="{{ $procurement->name }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="row">
                            <div class="col-6">
                                <label for="delivery_time" class="form-label font-weight-bold">Waktu Pengiriman<span class="text-danger">*</span></label>
                                <input type="number" name="delivery_time" id="delivery_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label for="time_unit" class="form-label font-weight-bold">Satuan Waktu<span class="text-danger">*</span></label>
                                <select name="time_unit" id="time_unit" class="form-control" required>
                                    <option value="">Pilih satuan waktu pengiriman</option>
                                    <option value="Hari">Hari</option>
                                    <option value="Minggu">Minggu</option>
                                    <option value="Bulan">Bulan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <h5 class="font-weight-bold">Daftar barang yang akan disertakan dalam PO:</h5>
                <div class="mb-3">
                    @foreach ($items as $item)
                        <div class="mb-2">
                            <div class="row">
                                <div class="col-12">
                                    <input class="item_id" type="hidden" name="item[]" value="{{ $item->id }}" disabled>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" name="vendor[]" id="item" value="{{ $item->vendor }}">
                                            </div>
                                        </div>
                                        <input type="text" class="form-control bg-white" value="{{ $item->name . "-" . $item->specs }}" required disabled>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <input type="text" name="item_note[]" class="item-note form-control" placeholder="Catatan (opsional)" style="display: none">
                                </div>
                                <div class="col-4">
                                    <input type="text" name="item_unit[]" class="item-unit form-control" placeholder="Satuan barang (wajib)" style="display: none">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    <a href="{{ url()->previous() }}" class="btn btn-danger mr-3">Back</a>
                    <button class="btn btn-primary" name="generate">Generate</button>
                </div> 
            </form>
        </div>
    </div>

@endsection

@push('js')
<script src="{{ asset("js/po.form.js") }}"></script>
@endpush