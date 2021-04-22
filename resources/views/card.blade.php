@extends('home')

@section('card')
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
@elseif (Auth::user()->role == 6 Or Auth::user()->role == 7)
    <div class="row">
        <!-- Pengajuan Pengadaan Card -->
        <div class="col mb-4">
            <div class="card bg-gradient-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Pengajuan Pengadaan <?= date('Y') ?></div>
                            <div class="h5 mb-0 font-weight-bold text-white">{{ $total_procurement }}</div>
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
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Pengadaan {{ date('Y') }} Selesai</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-white">{{ $total_finish }}</div>
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
@elseif (Auth::user()->role == 4 Or Auth::user()->role == 5)
    <div class="row">
        <!-- Pengajuan Pengadaan Card -->
        <div class="col mb-4">
            <div class="card bg-gradient-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Pengajuan Pengadaan <?= date('Y') ?></div>
                            <div class="h5 mb-0 font-weight-bold text-white">{{ $need_approval }}</div>
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
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Pengadaan {{ date('Y') }} Selesai</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-white">{{ $total_finish }}</div>
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
@endsection