@extends('layouts.main')

@section('page-title', 'My Bidder List')

@section('main-content')
    <div class="container">
        {{-- Header --}}
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-danger mb-2">Back</a>
            <div class="d-flex justify-content-start align-items-center">
                <h1>Bidder List</h1>
                <a href="" class="btn btn-sm btn-warning text-dark ml-3">Collapse All</a>
                <a href="" class="btn btn-sm btn-primary ml-3" style="display: none">Expand All</a>
            </div>
            <p class="font-italic">*Click the category or sub-category badge to expand or collapse each item</p>
        </div>

        {{-- Main Content --}}
        <div class="mb-4">
            @foreach ($categories as $category)
                <a href="" class="badge badge-pill badge-primary p-2 font-weight-bold mb-2">{{ $category->name }}</a>
                @foreach ($sub_categories as $sub)
                @if ($sub->category == $category->id)
                    <div class="row ml-3 category-content">
                        <a href="" class="badge badge-pill badge-primary p-2 font-weight-bold mb-2">{{ $sub->name }}</a>
                    </div>
                    @foreach ($vendors as $vendor)
                        @if ($vendor->category == $category->id And $vendor->sub_category == $sub->id)
                            <div class="row ml-5">
                                <div class="border border-primary rounded shadow p-2 mb-4 w-75">
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <h5><a href="" class="font-weight-bold">{{ $vendor->name }}</a></h5>
                                        <span class="btn btn-sm btn-success">{{ $vendor->trecord }}</span>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <span class="form-label font-weight-bold">Phone:</span>
                                            <span class="form-control-plaintext">{{ $vendor->phone }}</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="form-label font-weight-bold">Email:</span>
                                            <span class="form-control-plaintext">{{ $vendor->email }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <span class="form-label font-weight-bold">Bank Account</span>
                                            <span class="form-control-plaintext">{{ $vendor->bank_account . " - " . $vendor->name_account }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @endforeach
        </div>
    </div>
@endsection

@push('js')
<script src="{{ asset("js/vendor.list.js") }}"></script>
@endpush