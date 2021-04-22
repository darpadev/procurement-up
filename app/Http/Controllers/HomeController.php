<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    public function index()
    {
        $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->get()[0]['name'];
        $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->get()[0]['name'];
        $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->get();

        if (($role == 'Wakil Rektor' And (isset($unit[0]->name) And $unit[0]->name == 'Wakil Rektor 2')) Or ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas') Or (($role == 'Manajer' Or $role == 'Staf') And $unit[0]->name == 'Fungsi Pengadaan Barang dan Jasa')){
            if ($role == 'Wakil Rektor' Or $role == 'Manajer' Or $role == 'Staf'){
                $need_approval = \App\Models\Procurement::where('approver', '=', Auth::user()->role)
                    ->where('approver_unit', '=', Auth::user()->unit)
                    ->whereYear('created_at', date('Y'))
                    ->count();
            }elseif ($role == 'Direktur'){
                $need_approval = \App\Models\Procurement::where('approver', '=', Auth::user()->role)
                    ->where('approver_origin', '=', Auth::user()->origin)
                    ->whereYear('created_at', date('Y'))
                    ->count();                
            }
            $total_progress = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->where('statuses.name', '<>', 'Memo')
                ->where('statuses.name', '<>', 'Close')
                ->whereYear('procurements.created_at', date('Y'))
                ->count();
            $total_finish = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->where('statuses.name', '=', 'Close')
                ->whereYear('procurements.created_at', date('Y'))
                ->count();
            $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                ->where('statuses.name', '<>', 'Close')
                ->limit(10)
                ->get();
            $procurements_prev = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                ->where('statuses.name', '=', 'Close')
                ->limit(10)
                ->get();
            return view('home', [
                'need_approval' => $need_approval,
                'total_progress' => $total_progress,
                'total_finish' => $total_finish,
                'procurements' => $procurements,
                'procurements_prev' => $procurements_prev,
            ]); 
        }elseif ($role == 'Wakil Rektor'){
            $need_approval = \App\Models\Procurement::where('applicant', '=', Auth::user()->id)
                ->where('approval_status', '=', NULL)
                ->whereYear('created_at', date('Y'))
                ->count();
            $total_progress = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->where('applicant', '=', Auth::user()->id)
                ->where('statuses.name', '<>', 'Memo')
                ->where('statuses.name', '<>', 'Close')
                ->whereYear('procurements.created_at', date('Y'))
                ->count();
            $total_finish = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->where('applicant', '=', Auth::user()->id)
                ->where('statuses.name', '=', 'Close')
                ->whereYear('procurements.created_at', date('Y'))
                ->count();
            $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                ->where('applicant', '=', Auth::user()->id)
                ->where('statuses.name', '<>', 'Close')
                ->limit(10)
                ->get();
            $procurements_prev = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                ->where('applicant', '=', Auth::user()->id)
                ->where('statuses.name', '=', 'Close')
                ->limit(10)
                ->get();
            
            return view('home', [
                'need_approval' => $need_approval,
                'total_progress' => $total_progress,
                'total_finish' => $total_finish,
                'procurements' => $procurements,
                'procurements_prev' => $procurements_prev,
            ]);    
        }elseif ($role == 'Direktur' Or $role == 'Dekan'){
            $need_approval = \App\Models\Procurement::where('approver', '=', Auth::user()->role)
                ->where('approver_origin', '=', Auth::user()->origin)
                ->whereYear('created_at', date('Y'))
                ->count();
            $total_progress = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->where('origin', '=', Auth::user()->origin)
                ->where('statuses.name', '<>', 'Memo')
                ->where('statuses.name', '<>', 'Close')
                ->whereYear('procurements.created_at', date('Y'))
                ->count();
            $total_finish = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->where('origin', '=', Auth::user()->origin)
                ->where('statuses.name', '=', 'Close')
                ->whereYear('procurements.created_at', date('Y'))
                ->count();
            $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                ->where('procurements.origin', '=', Auth::user()->origin)
                ->where('statuses.name', '<>', 'Close')
                ->limit(10)
                ->get();
            $procurements_prev = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                ->where('procurements.origin', '=', Auth::user()->origin)
                ->where('statuses.name', '=', 'Close')
                ->limit(10)
                ->get();

            return view('home', [
                'need_approval' => $need_approval,
                'total_progress' => $total_progress,
                'total_finish' => $total_finish,
                'procurements' => $procurements,
                'procurements_prev' => $procurements_prev,
            ]);
        }elseif ($role == 'Kaprodi' Or $role == 'Manajer'){
            $need_approval = \App\Models\Procurement::where('applicant', '=', Auth::user()->id)
                ->where('approval_status', '=', NULL)
                ->whereYear('created_at', date('Y'))
                ->count();
            $total_progress = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->where('applicant', '=', Auth::user()->id)
                ->where('statuses.name', '<>', 'Memo')
                ->where('statuses.name', '<>', 'Close')
                ->whereYear('procurements.created_at', date('Y'))
                ->count();
            $total_finish = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->where('applicant', '=', Auth::user()->id)
                ->where('statuses.name', '=', 'Close')
                ->whereYear('procurements.created_at', date('Y'))
                ->count();
            $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                ->where('applicant', '=', Auth::user()->id)
                ->where('statuses.name', '<>', 'Close')
                ->limit(10)
                ->get();
            $procurements_prev = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                ->where('applicant', '=', Auth::user()->id)
                ->where('statuses.name', '=', 'Close')
                ->limit(10)
                ->get();

            return view('home', [
                'need_approval' => $need_approval,
                'total_progress' => $total_progress,
                'total_finish' => $total_finish,
                'procurements' => $procurements,
                'procurements_prev' => $procurements_prev,
            ]);
        }
    }
}
