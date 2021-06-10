@extends('layouts.main')

@section('page-title', 'Request BAPP')

@section('main-content')
    <div class="container">
        <div class="card shadow p-4 mb-4">            
            <h1 class="font-weight-bold text-center">Penetapan Pemenang Tender</h1>
            <hr>
            <form action="{{ Route('generate-bapp') }}" method="post">
            @csrf
            <input type="hidden" name="procurement" value="{{ $procurement->id }}">
            <div class="row align-items-center mb-3">
                <div class="col-md-auto font-weight-bold">Nomor Surat</div>
                <div class="col-4">
                    <input type="text" name="ref" class="form-control" required>
                </div>
            </div>
                @foreach ($vendors as $vendor)
                    <h2 class="font-weight-bold" style="font-size: 20px">{{ $vendor->name }}</h2>
                        @foreach ($quotations as $quotation)
                            @if ($quotation->vendor_id == $vendor->id)
                                <h3 style="font-size: 18px">
                                    <span class="badge badge-pill badge-primary">{{ $quotation->cat_name }}</span>
                                </h3>
                                <h4 class="ml-4" style="font-size: 16px">
                                    <span class="badge badge-pill badge-primary">{{ $quotation->sub_name }}</span>
                                </h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover text-center w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama Barang</th>
                                                <th>Spesifikasi Vendor</th>
                                                <th>Referensi</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $counter = 1 @endphp
                                            @foreach ($items as $item)
                                                @if ($item->sub_category == $quotation->item_sub_category)
                                                    <tr>
                                                        @if ($quotation->name != NULL)
                                                            <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                                        @endif
                                                        <th style="width: 5%">{{ $counter }}</th>
                                                        <td>{{ $item->name }}</td>
                                                        <td>
                                                            <textarea 
                                                                name="vendor_spec[]" 
                                                                cols="5" rows="5" 
                                                                class="form-control" 
                                                                style="resize: none" 
                                                                {{ $quotation->name != NULL ? '' : 'disabled'}}
                                                                required></textarea>
                                                        </td>
                                                        <td style="width: 20%">
                                                            @if ($quotation->name != NULL)
                                                                <a 
                                                                    href="{{ Route('view-document-vendor', [
                                                                        'id' => $quotation->id, 
                                                                        'table' => 'quotations']) }}" 
                                                                    target="_blank">
                                                                        {{ $quotation->name }}
                                                                </a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td style="width: 15%">
                                                            @if ($quotation->name != NULL And $quotation->isSuitable)
                                                                @if ($quotation->winner)
                                                                    <span class="badge badge-success mx-2">Pemenang Tender</span>
                                                                @endif
                                                                <span class="badge badge-primary mx-2">Sesuai</span>
                                                            @elseif ($quotation->name != NULL And !$quotation->isSuitable)
                                                                <span class="badge badge-danger">Tidak Sesuai</span>
                                                            @endif
                                                        </td>
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
            <div class="d-flex justify-content-center">
                <button class="btn btn-primary" id="generate-bapp">Buat BAPP</button>
            </div>
            </form>
        </div>
    </div>

@endsection

{{-- @push('js')
<script src="{{ asset("js/bapp.form.js") }}"></script>
@endpush --}}