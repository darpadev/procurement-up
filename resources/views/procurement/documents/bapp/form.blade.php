@extends('layouts.main')

@section('page-title', 'Request BAPP')

@section('main-content')
    <div class="container">
        <div class="card shadow p-4 mb-4">            
            <form action="{{ Route('generate-bapp') }}" method="post" target="_blank">
                @csrf
                <input type="number" name="proc_id" hidden value="{{ $proc_id }}">
                <input type="number" name="vendor_id" hidden value="{{ $vendor_id }}">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="ref" class="form-label font-weight-bold">Nomor Surat<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="ref" name="ref" placeholder="0000/XX-XX.X.X.X" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control-plaintext" id="date" name="date" value="{{ $date }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h5 class="font-weight-bold">Pengadaan dan barang yang disertakan dalam SPPH:</h5>
                <p class="mb-4">{{ $procurement->name }}</p>
                <h5 class="font-weight-bold">Daftar barang:</h5>
                @foreach ($items as $item)
                    <input class="item-id" type="hidden" name="item[]" value="{{ $item->id }}" disabled>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" name="vendor[]" id="item" value="{{ $item->vendor }}">
                            </div>
                        </div>
                        <input type="text" class="form-control bg-white" value="{{ $item->name . " - " . $item->specs }}" required disabled>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="number" class="form-control price-input" id="quotation_price" name="quotation_price[]" placeholder="Harga penawaran" disabled>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" class="form-control price-input" id="nego_price" name="nego_price[]" placeholder="Harga didapatkan" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-center">
                    <a href="{{ url()->previous() }}" class="btn btn-danger mr-3">Back</a>
                    <button class="btn btn-primary" name="generate">Generate</button>
                </div> 

            </form>
        </div>
    </div>

@endsection

@push('js')
<script src="{{ asset("js/bapp.form.js") }}"></script>
@endpush