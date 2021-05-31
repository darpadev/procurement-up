@extends('layouts.main')

@section('page-title', $procurement->name)

@section('main-content')
<div class="container">
    <div class="card shadow p-4 mb-4">                
        @if (Auth::user()->role != 8 And Auth::user()->origin != 2 And Auth::user()->unit != 4)
            <form action="{{ Route('update-procurement', ['id' => $procurement->id]) }}" method="post" enctype="multipart/form-data">   
                @csrf
                <input type="number" name="applicant" hidden value="{{ Auth::user()->id }}">
                <input type="number" name="unit" hidden value="{{ Auth::user()->unit }}">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="ref" class="form-label font-weight-bold">Ref</label>
                        <input type="text" class="form-control" id="ref" name="ref" placeholder="000/---/MEMO/---/yyyy" value="{{ $procurement->ref }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label font-weight-bold">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $procurement->name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="value" class="form-label font-weight-bold">OE</label>
                        <input type="number" class="form-control" id="value" name="value" value="{{ $procurement->value }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label font-weight-bold">Date</label>
                        <input type="text" class="form-control-plaintext" id="date" name="date" value="@php echo date('d F Y - H:i:s', strtotime($procurement->created_at)) @endphp" required readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tor" class="form-label font-weight-bold">ToR</label>
                        @if ($tor['available'])
                            <table class="w-100">
                                <tbody>
                                    <tr>
                                        <td class="text-break w-75"><a href="{{ Route('view-document', ['id' => $documents[$tor['index']]->id]) }}" target="_blank">{{ $documents[$tor['index']]->name }}</a></td>
                                        <td class="w-25 text-center align-top"><a class="btn btn-sm btn-danger" href="{{ Route('doc-destroy', ['proc' => $procurement->id, 'id' => $documents[$tor['index']]->id]) }}">Delete</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <input type="file" class="form-control-plaintext" id="tor" name="tor" accept="application/pdf">
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="spec" class="form-label font-weight-bold">Specs</label>
                        @if ($spec['available'])
                            <table class="w-100">
                                <tbody>
                                    <tr>
                                        <td class="text-break w-75"><a href="{{ Route('view-document', ['id' => $documents[$spec['index']]->id]) }}" target="_blank">{{ $documents[$spec['index']]->name }}</a></td>
                                        <td class="w-25 text-center align-top"><a class="btn btn-sm btn-danger" href="{{ Route('doc-destroy', ['proc' => $procurement->id, 'id' => $documents[$spec['index']]->id]) }}">Delete</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <input type="file" class="form-control-plaintext" id="spec" name="spec" accept="application/pdf">
                        @endif                    
                    </div>
                </div>  
                <div class="d-flex justify-content-center">
                    <a href="{{ Route('show-procurement', ['id' => $procurement->id]) }}" class="btn btn-danger mr-2">Back</a>
                    <button class="btn btn-primary" name="update">Update</button>
                </div>
            </form>
        @else
            <form action="{{ Route('update-procurement', ['id' => $procurement->id]) }}" method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="priority" class="form-label font-weight-bold">Prioritas</label>
                        <select class="form-control" name="priority" id="priority">
                            <option selected disabled>Piilh Prioritas</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}" {{ $priority->id == $procurement->priority ? 'selected' : NULL }}>{{ $priority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label font-weight-bold">Kategori</label>
                        <select class="form-control" name="category" id="category">
                            <option selected disabled>Piilh Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $procurement->category ? 'selected' : NULL }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <a href="{{ Route('show-procurement', ['id' => $procurement->id]) }}" class="btn btn-danger mr-2">Back</a>
                    <button class="btn btn-primary" name="update_by_staff">Update</button>
                </div>
            </form>
        @endif
    </div>

</div>
@endsection

@push('js')
<script src="{{ asset("js/my-procurement-show.js") }}"></script>
@endpush