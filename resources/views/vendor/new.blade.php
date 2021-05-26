@extends('layouts.main')

@section('page-title', 'New vendor')

@section('main-content')
    <div class="container">
        <div class="card shadow p-4 mb-4">            
            <form action="{{ Route('store-vendor') }}" method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="reg_code" class="form-label font-weight-bold">Nomor Vendor</label>
                        <input type="text" class="form-control" id="reg_code" name="reg_code">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label font-weight-bold">Nama Vendor<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label font-weight-bold">Alamat<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label font-weight-bold">No. Telfon<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label font-weight-bold">Alamat Email<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>                   
                    <div class="col-md-6 mb-3">
                        <label for="business_field" class="form-label font-weight-bold">Bidang Usaha<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="text" class="form-control" id="business_field" name="business_field" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bank_account" class="form-label font-weight-bold">
                            Nomor Rekening
                            <span class="badge badge-pill badge-danger ml-2">wajib</span>
                            <span class="badge badge-pill badge-danger ml-2">hanya angka</span>
                        </label>
                        <input type="text" class="form-control" id="bank_account" name="bank_account" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="name_account" class="form-label font-weight-bold">Nama Bank<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="text" class="form-control" id="name_account" name="name_account" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tin" class="form-label font-weight-bold">NPWP / TIN <span class="font-italic">(Tax Identification Number)</span><span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="text" class="form-control" id="tin" name="tin" required>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center">
                    <a href="{{ url()->previous() }}" class="btn btn-danger mr-3">Cancel</a>
                    <button class="btn btn-primary" name="submit">Submit</button>
                </div> 

            </form>
        </div>
    </div>

@endsection

@push('js')
    <script src="{{asset('./js/vendor.new.js')}}"></script>
@endpush