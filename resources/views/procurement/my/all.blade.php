@extends('layouts.main')

@section('page-title', 'My Procurement')

@section('main-content')
    <div class="container">
        <a href="{{ url()->previous() }}" class="btn btn-danger mb-2">Back</a>
        <div class="card shadow p-4 mb-4">            
            <table class="table table-hover w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="white-space: nowrap; width: 1%;">Tanggal Pengajuan</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($procurements as $procurement)
                    <tr>
                        <td class="text-center">@php echo date('d F Y', strtotime($procurement->created_at)) @endphp</td>
                        <td>{{ $procurement->name }}</td>
                        <td class="text-center" style="width: 1%; white-space: nowrap;">
                            <span class="p-2 badge badge-pill badge badge-pill 
                            <?php
                            if($procurement->stats_id == 1){
                                echo 'badge-warning';
                            }elseif($procurement->stats_id == 2 or $procurement->stats_id == 7){
                                echo 'badge-primary';
                            }elseif($procurement->stats_id == 3){
                                    echo 'badge-danger';
                            }elseif($procurement->stats_id == 8){
                                echo 'badge-success';
                            }else{
                                echo 'badge-info';
                            }
                            ?>">
                                {{ $procurement->stats }}
                            </span>
                        </td>
                        <td class="text-center" style="width: 1%; white-space: nowrap;">
                            <a href="{{ Route('show-procurement', ['id' => $procurement->id]) }}"><i class="fas fa-external-link-alt"></i></a>
                        </td>
                    </tr>                 
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $procurements->links() }}
        </div>
    </div>
@endsection