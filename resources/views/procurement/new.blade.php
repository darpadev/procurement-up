@extends('layouts.main')

@section('page-title', 'New Procurement')

@section('main-content')
    <div class="container">
        <div class="card shadow p-4 mb-4">            
            <form action="{{ Route('store-procurement') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="number" name="applicant" hidden value="{{ Auth::user()->id }}">
                <input type="number" name="origin" hidden value="{{ Auth::user()->origin }}">
                <input type="number" name="unit" hidden value="{{ Auth::user()->unit }}">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="ref" class="form-label font-weight-bold">Referensi<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="text" class="form-control" id="ref" name="ref" placeholder="000/---/MEMO/---/yyyy" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label font-weight-bold">Nama Pengadaan<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="value" class="form-label font-weight-bold">OE<span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        <input type="number" class="form-control" id="value" name="value" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label font-weight-bold">Tanggal Pengajuan</label>
                        <input type="datetime" class="form-control-plaintext" id="date" name="date" value="{{ date('Y-m-d H:i:s') }}" required readonly>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="mechanism" class="form-label font-weight-bold">Mekanisme</label>
                        <select name="mechanism" id="mechanism" class="form-control-plaintext">
                            <option value="{{ $mechanisms->id }}" selected>{{ $mechanisms->name }}</option>
                        </select>
                    </div>                    
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="tor" class="form-label font-weight-bold">ToR<span class="badge badge-pill badge-danger ml-2">PDF</span><span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                        </div>
                        <input type="file" class="form-control-plaintext" id="tor" name="tor" accept="application/pdf" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="spec" class="form-label font-weight-bold">Specs</label>
                        </div>
                        <input type="file" class="form-control-plaintext" id="spec" name="spec" accept="application/pdf">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="unit-list" class="form-label font-weight-bold">Unit List<span class="badge badge-pill badge-success ml-2">.xls | .xlsx</span><span class="badge badge-pill badge-danger ml-2">wajib</span></label>
                            <a href="{{ Route('download-template') }}" class="btn btn-sm btn-primary">Download Template</a>
                        </div>
                        <input type="file" class="form-control-plaintext" id="unit-list" name="unit-list" accept=".xls, .xlsx" required>
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