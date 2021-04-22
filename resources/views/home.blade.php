@extends('layouts.main')

@section('page-title', 'Dashboard')

@section('main-content')
    <h1 class="font-weight-bold">{{ __('Dashboard') }}</h1>

    @if (Auth::user()->role == 1)
        <div class="row">

            <!-- Total OE Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total OE <?= date('Y') ?></div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        echo 'Rp' . number_format($total_value, 2, ',', '.');
                                    @endphp
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Allocated Fund Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Allocated Funds</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp999,999,999,999</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Progress Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Overall Progress</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            @php
                                                $proc_finished = \App\Models\Procurement::where('status', '=', 8)->count();
                                                $proc_total = \App\Models\Procurement::count();
                                                if($proc_total){
                                                    $progress = (int)(($proc_finished / $proc_total) * 100);
                                                }else{
                                                    $progress = 0;
                                                }
                                                echo  $progress. '%'; 
                                            @endphp
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Number of Project Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Number of Project</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($procurements) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    @else
        <div class="row">
            <!-- Menunggu Persetujuan Card -->
            <div class="col mb-4">
                <div class="card bg-gradient-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Menunggu Persetujuan</div>
                                <div class="h5 mb-0 font-weight-bold text-white">{{ $need_approval }}</div>
                                <div class="text-xs text-uppercase mb-1"><a class="font-weight-bold text-white" href="{{ Route('my-procurement', ['stats' => 'approval']) }}">Lihat data ></a></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-upload fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sedang Berjalan Card -->
            <div class="col mb-4">
                <div class="card bg-gradient-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Sedang Berjalan</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $total_progress }}</div>
                                <div class="text-xs text-uppercase mb-1"><a class="font-weight-bold text-dark" href="{{ Route('my-procurement', ['stats' => 'ongoing']) }}">Lihat data ></a></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck-loading fa-2x text-dark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengadaan Selesai Card -->
            <div class="col mb-4">
                <div class="card bg-gradient-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Pengadaan Selesai</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-white">{{ $total_finish }}</div>
                                        <div class="text-xs text-uppercase mb-1"><a class="font-weight-bold text-white" href="{{ Route('my-procurement', ['stats' => 'prev']) }}">Lihat data ></a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="d-flex justify-content-between align-items-center md-4">
        <h1 class="font-weight-bold">Proposed Procurement</h1>
        <a href="{{ Route('my-procurement', ['stats' => 'proposed']) }}" class="btn btn-primary">Show All</a>
    </div>

    {{-- Project content --}}
    <div class="shadow card p-2 mb-4">
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
                @php
                    $i = 1;
                @endphp
                @foreach ($procurements as $procurement)
                    @if ($procurement->stats_id != 3 AND $procurement->stats_id != 8)
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
                    @php
                        $i++;
                    @endphp
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center md-4">
        <h1 class="font-weight-bold">Previous Procurement</h1>
        <a href="{{ Route('my-procurement', ['stats' => 'prev']) }}" class="btn btn-primary">Show All</a>
    </div>

    {{-- Project content --}}
    <div class="shadow card p-2 mb-4">
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
                @php
                    $i = 1;
                @endphp
                @foreach ($procurements_prev as $procurement)
                    @if ($procurement->stats_id == 3 or $procurement->stats_id == 8)
                        <tr>
                            <td class="text-center">@php echo date('d F Y', strtotime($procurement->created_at)) @endphp</td>
                            <td>{{ $procurement->name }}</td>
                            <td class="text-center" style="width: 1%; white-space: nowrap;">
                                <span class="p-2 badge badge-pill badge badge-pill 
                                <?php
                                if($procurement->stats_id == 3){
                                    echo 'badge-danger';
                                }elseif($procurement->stats_id == 8){
                                    echo 'badge-success';
                                }
                                ?>">
                                    {{ $procurement->stats }}
                                </span>
                            </td>
                            <td class="text-center" style="width: 1%; white-space: nowrap;">
                                <a href="{{ Route('show-procurement', ['id' => $procurement->id]) }}"><i class="fas fa-external-link-alt"></i></a>
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endif                    
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
