@extends('layouts.main')

@section('page-title', 'My Bidder List')

@section('main-content')
    <div class="container">
        {{-- Header --}}
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-danger mb-2">Back</a>
            <h1 class="font-weight-bold">Bidder List</h1>
        </div>

        {{-- Main Content --}}
        <div class="mb-4">
            @if (count($vendors_unknown))
                <h2 class="font-weight-bold">Belum dikategorikan</h2>
                <div class="card shadow p-3 mb-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center">
                            <thead>
                                <tr>
                                    <th style="white-space: nowrap; width: 1%;">#</th>
                                    <th>Nama Vendor</th>
                                    <th class="w-25">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendors_unknown as $vendor)
                                <tr>
                                    <th style="white-space: nowrap; width: 1%;">{{ str_pad($loop->iteration, strlen(count($vendors_unknown)), 0, STR_PAD_LEFT) }}</th>
                                    <td class="text-left">
                                        <span class="font-weight-bold">{{ $vendor->name }}</span>
                                        <div class="more-info" style="display: none">
                                            <hr>
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Bidang Usaha</span>
                                                        <br>
                                                        {{ $vendor->business_field }}
                                                    </p>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Alamat</span>
                                                        <br>
                                                        {{ $vendor->address }}
                                                    </p>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Nomor Telepon</span>
                                                        <br>
                                                        {{ $vendor->phone }}
                                                    </p>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">E-mail</span>
                                                        <br>
                                                        {{ $vendor->email }}
                                                    </p>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Rekening</span>
                                                        <br>
                                                        {{ $vendor->name_account }} - {{ $vendor->bank_account }}
                                                    </p>
                                                </div>
                                                <div class="category-container col-12 mb-2">
                                                    <p class="m-0 mb-2">
                                                        <span class="font-weight-bold">Tambahkan Kategori</span>
                                                    </p>
                                                    <form action="{{ Route('update-vendor', ['id' => $vendor->id]) }}" method="post"> 
                                                        @csrf                                                       
                                                        <div class="d-flex justify-content-between">
                                                            <select name="category" id="" class="add-category form-control form-control-sm w-50 mx-2 mb-2" required>
                                                                <option value="">Pilih Kategori</option>
                                                                @foreach ($list_categories as $category)
                                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <select name="sub_category" id="" class="add-sub-category form-control form-control-sm w-50 mx-2 mb-2" required>
                                                                <option value="">Pilih Sub Kategori</option>
                                                            </select>
                                                        </div>
                                                        <div class="d-flex justify-content-center">
                                                            <button name="submit-category" class="btn btn-sm btn-primary">Submit</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><a href="" class="more-info-btn"><i class="fas fa-fw fa-caret-square-down"></i></a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="card shadow p-3 mb-3">
                <div class="d-flex justify-content-center mb-3">
                    {{ $vendors->links() }}
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap; width: 1%;">#</th>
                                <th>Nama Vendor</th>
                                <th style="white-space: nowrap; width: 1%;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="vendor-container">
                            @foreach ($vendors as $vendor)
                                <tr>
                                    <th style="white-space: nowrap; width: 1%;">{{ str_pad($loop->iteration, strlen(count($vendors_unknown)), 0, STR_PAD_LEFT) }}</th>
                                    <td class="text-left">
                                        <span class="font-weight-bold">{{ $vendor->name }}</span>
                                        <div class="more-info" style="display: none">
                                            <hr>
                                            <div class="row">
                                                <div class="col-8 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Bidang Usaha</span>
                                                        <br>
                                                        {{ $vendor->business_field }}
                                                    </p>
                                                </div>
                                                <div class="col-4 mb2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Rekam Jejak</span>
                                                        <br>
                                                        @if ($vendor->trecord == 'Good')
                                                            <span class="badge badge-pill badge-success">{{ $vendor->trecord }}</span>
                                                        @elseif ($vendor->trecord == 'Blacklist')
                                                            <span class="badge badge-pill badge-dark">{{ $vendor->trecord }}</span>
                                                        @else
                                                            {{ $vendor->trecord }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Alamat</span>
                                                        <br>
                                                        {{ $vendor->address }}
                                                    </p>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Nomor Telepon</span>
                                                        <br>
                                                        {{ $vendor->phone }}
                                                    </p>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">E-mail</span>
                                                        <br>
                                                        {{ $vendor->email }}
                                                    </p>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    <p class="m-0">
                                                        <span class="font-weight-bold">Rekening</span>
                                                        <br>
                                                        {{ $vendor->name_account }} - {{ $vendor->bank_account }}
                                                    </p>
                                                </div>
                                                <div class="category-container col-12 mb-2">
                                                    <p class="m-0 mb-2">
                                                        <span class="font-weight-bold">Kategori Terdaftar</span>
                                                    </p>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-bordered mb-2">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Kategori</th>
                                                                    <th class="text-center">Sub Kategori</th>
                                                                    <th class="text-center" style="white-space: nowrap; width: 1%;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($vendor_categories as $item)
                                                                    @if ($item->vendor == $vendor->id)
                                                                        <tr>
                                                                            <td>{{ $item->category }}</td>
                                                                            <td>{{ $item->sub_category }}</td>
                                                                            <td class="text-center">
                                                                                <a 
                                                                                    href="{{ Route('destroy-vendor-category', [
                                                                                        'vendor' => $vendor->id, 
                                                                                        'category' => $item->id_cat, 
                                                                                        'sub_category' => $item->id_sub]) }}" 
                                                                                    class="badge badge-danger">
                                                                                        &times;
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <form action="{{ Route('update-vendor', ['id' => $vendor->id]) }}" method="post"> 
                                                        @csrf                                                       
                                                        <div class="d-flex justify-content-between">
                                                            <select name="category" id="" class="add-category form-control form-control-sm w-50 mx-2 mb-2" required>
                                                                <option value="">Pilih Kategori</option>
                                                                @foreach ($list_categories as $category)
                                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <select name="sub_category" id="" class="add-sub-category form-control form-control-sm w-50 mx-2 mb-2" required>
                                                                <option value="">Pilih Sub Kategori</option>
                                                            </select>
                                                        </div>
                                                        <div class="d-flex justify-content-center">
                                                            <button name="submit-category" class="btn btn-sm btn-primary">Tambah Kategori</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><a href="" class="more-info-btn"><i class="fas fa-fw fa-caret-square-down"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="{{ asset("js/vendor.list.js") }}"></script>
@endpush