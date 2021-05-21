<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemImport;
use Auth;
use PDF;

class ProcurementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->get()[0]['name'];
        $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->get()[0]['name'];
        $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->get();

        if (($role == 'Wakil Rektor' And (isset($unit[0]->name) And $unit[0]->name == 'Bidang Keuangan dan Sumber Daya Organisasi')) Or ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas') Or (($role == 'Manajer' Or $role == 'Staf') And $unit[0]->name == 'Fungsi Pengadaan Barang dan Jasa')){
            switch ($_GET['stats']) {
                case 'all':
                    $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->paginate(25);
                    break;
                
                case 'prev':
                    $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('statuses.name', '=', 'Close')
                        ->paginate(25);
                    break;

                case 'proposed':
                    $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('statuses.name', '<>', 'Close')
                        ->paginate(25);
                    break;

                case 'ongoing':
                    $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('statuses.name', '<>', 'Memo')
                        ->where('statuses.name', '<>', 'Close')
                        ->paginate(25);
                    break;

                case 'approval':
                    if ($role == 'Wakil Rektor' Or $role == 'Manajer' Or $role == 'Staf'){
                        $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                            ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                            ->where('approver', '=', Auth::user()->role)
                            ->where('approver_unit', '=', Auth::user()->unit)
                            ->whereYear('procurements.created_at', date('Y'))
                            ->paginate(25);
                    }elseif ($role == 'Direktur'){
                        $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                            ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                            ->where('approver', '=', Auth::user()->role)
                            ->where('approver_origin', '=', Auth::user()->origin)
                            ->whereYear('procurements.created_at', date('Y'))
                            ->paginate(25);                
                    }
                    break;
            }
        }elseif ($role == 'Wakil Rektor'){
            switch ($_GET['stats']) {
                case 'all':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->paginate(25);
                    break;
                
                case 'prev':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->where('statuses.name', '=', 'Close')
                        ->paginate(25);
                    break;

                case 'proposed':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->where('statuses.name', '<>', 'Close')
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;

                case 'ongoing':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->where('statuses.name', '<>', 'Memo')
                        ->where('statuses.name', '<>', 'Close')
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;

                case 'approval':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->where('approval_status', '=', NULL)
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;
            } 
        }elseif ($role == 'Direktur' Or $role == 'Dekan'){
            switch ($_GET['stats']) {
                case 'all':
                    $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('procurements.origin', '=', Auth::user()->origin)
                        ->paginate(25);
                    break;
                
                case 'prev':
                    $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('procurements.origin', '=', Auth::user()->origin)
                        ->where('statuses.name', '=', 'Close')
                        ->paginate(25);
                    break;

                case 'proposed':
                    $procurements = \App\Models\Procurement::join('statuses', 'procurements.status', '=', 'statuses.id')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('procurements.origin', '=', Auth::user()->origin)
                        ->where('statuses.name', '<>', 'Close')
                        ->paginate(25);
                    break;

                case 'ongoing':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('origin', '=', Auth::user()->origin)
                        ->where('statuses.name', '<>', 'Memo')
                        ->where('statuses.name', '<>', 'Close')
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;

                case 'approval':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('approver', '=', Auth::user()->role)
                        ->where('approver_origin', '=', Auth::user()->origin)
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;
            }
        }elseif ($role == 'Kaprodi' Or $role == 'Manajer'){
            switch ($_GET['stats']) {
                case 'all':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;
                
                case 'prev':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->where('statuses.name', '=', 'Close')
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;

                case 'proposed':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->where('statuses.name', '<>', 'Close')
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;

                case 'ongoing':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->where('statuses.name', '<>', 'Memo')
                        ->where('statuses.name', '<>', 'Close')
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;

                case 'approval':
                    $procurements = \App\Models\Procurement::join('statuses', 'statuses.id', '=', 'procurements.status')
                        ->select('procurements.id', 'procurements.name', 'status', 'procurements.created_at', 'statuses.name AS stats', 'statuses.id AS stats_id')
                        ->where('applicant', '=', Auth::user()->id)
                        ->where('approval_status', '=', NULL)
                        ->whereYear('procurements.created_at', date('Y'))
                        ->paginate(25);
                    break;
            }
        }

        return view( "procurement.my.all", [
            'procurements' => $procurements,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mechanisms = \App\Models\ProcMechanism::where('name', '=', 'Tender Terbuka')->get()[0];

        return view('procurement.new', [
            'mechanisms' => $mechanisms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->get()[0]['name'];
        $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->get()[0]['name'];
        $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->get();

        if(isset($_POST['submit'])){
            if ($role == 'Wakil Rektor'){
                if($unit == 'Bidang Keuangan dan Sumber Daya Organisasi'){
                    $approver = 3;
                    $approver_origin = 2;
                    $approver_unit = NULL;
                }else{
                    $approver = 2;
                    $approver_origin = 1;
                    $approver_unit = 2;
                }
            }elseif ($role == 'Direktur' Or $role == 'Dekan'){
                $approver = 2;
                $approver_origin = 1;
                $approver_unit = 2;
            }elseif ($role == 'Kaprodi'){
                $approver = 4;
                $approver_origin = Auth::user()->origin;
                $approver_unit = NULL;
            }elseif ($role == 'Manajer'){
                if ($request->value <= 200000000){
                    $approver = 3;
                    $approver_origin = Auth::user()->origin;
                    $approver_unit = NULL;
                }else{
                    $approver = 2;
                    $approver_origin = 1;
                    $approver_unit = 2;
                }
            }
            
            $proc_id = \App\Models\Procurement::insertGetId([
                'ref' => $request->ref,
                'name' => $request->name,
                'value' => $request->value,
                'applicant' => $request->applicant,
                'origin' => $request->origin,
                'unit' => $request->unit,
                'mechanism' => $request->mechanism,
                'category' => $request->category,
                'status' => 1,
                'approver' => $approver,
                'approver_origin' => $approver_origin,
                'approver_unit' => $approver_unit,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if(isset($approver_unit) And $approver_unit == 2){
                \App\Models\ProcLog::insert([
                    'procurement' => $proc_id, 
                    'message' => "Menunggu persetujuan Wakil Rektor II", 
                    'sender' => Auth::user()->id, 
                    'created_at' => date('Y-m-d H:i:s'), 
                    'updated_at' => date('Y-m-d H:i:s')
                ]);                
            }else{
                $approver = \App\Models\Procurement::join('origins', 'origins.id', '=', 'procurements.approver_origin')
                    ->join('roles', 'roles.id', '=', 'procurements.approver')
                    ->select('roles.name AS role', 'origins.name AS origin')
                    ->where('procurements.id', '=', $proc_id)
                    ->get()[0];

                \App\Models\ProcLog::insert([
                    'procurement' => $proc_id, 
                    'message' => "Menunggu persetujuan $approver->role $approver->origin",                     
                    'sender' => Auth::user()->id, 
                    'created_at' => date('Y-m-d H:i:s'), 
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
                
            $pdo = DB::getPdo();

            $name = $_FILES['tor']['name'];
            $doc_type = $_FILES['tor']['type'];
            $doc = file_get_contents($_FILES['tor']['tmp_name']);
            $date = date('Y-m-d H:i:s');
            $type = "ToR";
    
            $stmt = $pdo->prepare("INSERT INTO documents VALUES(NULL, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $proc_id);
            $stmt->bindParam(2, $name);
            $stmt->bindParam(3, $doc_type);
            $stmt->bindParam(4, $type);
            $stmt->bindParam(5, $date);
            $stmt->bindParam(6, $date);
            $stmt->bindParam(7, $doc);

            $stmt->execute();

            if(strlen($_FILES['spec']['name'])){
                $name = $_FILES['spec']['name'];
                $doc_type = $_FILES['spec']['type'];
                $doc = file_get_contents($_FILES['spec']['tmp_name']);
                $date = date('Y-m-d H:i:s');
                $type = "Spec"; 
        
                $stmt = $pdo->prepare("INSERT INTO documents VALUES(NULL, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bindParam(1, $proc_id);
                $stmt->bindParam(2, $name);
                $stmt->bindParam(3, $doc_type);
                $stmt->bindParam(4, $type);
                $stmt->bindParam(5, $date);
                $stmt->bindParam(6, $date);
                $stmt->bindParam(7, $doc);

                $stmt->execute();
            }

            Excel::import(new ItemImport($proc_id), $request->file('unit-list'));

            return redirect(Route('show-procurement', ['id' => $proc_id]));
        }else{
            dd($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->get()[0]['name'];
        $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->get()[0]['name'];
        $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->get();
        $quotations = \App\Models\Quotation::join('vendors', 'vendors.id', '=', 'quotations.vendor')
            ->select('quotations.*', 'vendors.name AS vendor_name')
            ->orderBy('vendors.name')
            ->where('quotations.procurement', '=', $id)
            ->get();
        $procurement = \App\Models\Procurement::leftJoin('proc_mechanisms', 'proc_mechanisms.id', '=', 'procurements.mechanism')
            ->leftJoin('statuses', 'statuses.id', '=', 'procurements.status')
            ->leftJoin('proc_categories', 'proc_categories.id', '=', 'procurements.category')
            ->select('procurements.*', 'proc_mechanisms.name AS mech_name', 'statuses.name AS status_name', 'proc_categories.name AS category_name')
            ->where('procurements.id', '=', $id)
            ->get()[0];      
        $logs = \App\Models\ProcLog::join('users', 'users.id', '=', 'proc_logs.sender')
            ->select('users.name', 'message', 'proc_logs.created_at')
            ->where('procurement', '=', $id)
            ->orderBy('proc_logs.id', 'DESC')
            ->get();
        $log_dates = \App\Models\ProcLog::select('created_at')->distinct()->orderBy('id', 'DESC')->get();
        $documents = \App\Models\Document::where('procurement', '=', $procurement->id)->get();
        $items = \App\Models\Item::where('procurement', '=', $procurement->id)->get();
        $pic = \App\Models\User::select('name')->where('id', '=', $procurement->pic)->get();
        $category = \App\Models\ProcCategory::select('name')->where('id', '=', $procurement->category)->get();
        $priority = \App\Models\Priority::select('name')->where('id', '=', $procurement->priority)->get();
        $tor = array('available' => false, 'index' => 0);
        $spec = array('available' => false, 'index' => 0);

        foreach ($documents as $index => $doc){
            if ($doc->type == 'ToR'){
                $tor['available'] = true;
                $tor['index'] = $index;
            }elseif ($doc->type == 'Spec'){
                $spec['available'] = true;
                $spec['index'] = $index;
            }
        }

        return view('procurement.my.show', [
            'quotations' => $quotations,
            'procurement' => $procurement,
            'items' => $items,
            'documents' => $documents,
            'log_dates' => $log_dates,
            'category' => $category,
            'priority' => $priority,
            'logs' => $logs,
            'spec' => $spec,
            'pic' => $pic,
            'tor' => $tor,
            'role' => $role,
            'origin' => $origin,
            'unit' => $unit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->get()[0]['name'];
        $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->get()[0]['name'];
        $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->get();

        if ($role == 'Staf' And $origin == 'Fungsi Pengelola Fasilitas Universitas' And $unit[0]->name == 'Fungsi Pengadaan Barang dan Jasa'){            
            $procurement = \App\Models\Procurement::select('id', 'name', 'pic', 'category', 'priority')->where('id', '=', $id)->get()[0];
            $categories = \App\Models\ProcCategory::select('id', 'name')->get();
            $priorities = \App\Models\Priority::select('id', 'name')->get();
            $pic = \App\Models\User::select('id', 'name')->where('origin', '=', 2)->where('unit', '=', 4)->get();

            return view('procurement.my.edit', [
                'procurement' => $procurement,
                'categories' => $categories,
                'priorities' => $priorities,
                'pic' => $pic,
            ]);
        }else{
            $procurement = \App\Models\Procurement::where('id', '=', $id)->get()[0];
            $documents = \App\Models\Document::where('procurement', '=', $procurement->id)->get();            
    
            $tor = array('available' => false, 'index' => 0);
            $spec = array('available' => false, 'index' => 0);
    
            foreach ($documents as $index => $doc){
                if ($doc->type == 'ToR'){
                    $tor['available'] = true;
                    $tor['index'] = $index;
                }elseif ($doc->type == 'Spec'){
                    $spec['available'] = true;
                    $spec['index'] = $index;
                }
            }
    
            return view('procurement.my.edit', [
                'procurement' => $procurement,
                'documents' => $documents,
                'tor' => $tor,
                'spec' => $spec,
                'role' => $role,
                'origin' => $origin,
                'unit' => $unit,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (isset($_POST['update'])){
            \App\Models\Procurement::where('id', '=', $id)
                ->update([
                    'ref' => $request->ref,
                    'name' => $request->name,
                    'value' => $request->value,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        
            if(isset($_FILES['tor'])) {
                $pdo = DB::getPdo();

                $name = $_FILES['tor']['name'];
                $doc_type = $_FILES['tor']['type'];
                $doc = file_get_contents($_FILES['tor']['tmp_name']);
                $date = date('Y-m-d H:i:s');
                $type = "ToR";
        
                $stmt = $pdo->prepare("INSERT INTO documents VALUES(NULL, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bindParam(1, $id);
                $stmt->bindParam(2, $name);
                $stmt->bindParam(3, $doc_type);
                $stmt->bindParam(4, $type);
                $stmt->bindParam(5, $date);
                $stmt->bindParam(6, $date);
                $stmt->bindParam(7, $doc);

                $stmt->execute();          
            }

            if (isset($_FILES['spec'])){
                $pdo = DB::getPdo();

                $name = $_FILES['spec']['name'];
                $doc_type = $_FILES['spec']['type'];
                $doc = file_get_contents($_FILES['spec']['tmp_name']);
                $date = date('Y-m-d H:i:s');
                $type = "Spec"; 
        
                $stmt = $pdo->prepare("INSERT INTO documents VALUES(NULL, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bindParam(1, $id);
                $stmt->bindParam(2, $name);
                $stmt->bindParam(3, $doc_type);
                $stmt->bindParam(4, $type);
                $stmt->bindParam(5, $date);
                $stmt->bindParam(6, $date);
                $stmt->bindParam(7, $doc);

                $stmt->execute();
            }

            \App\Models\ProcLog::insert([
                'procurement' => $id,
                'message' => 'Informasi pengadaan telah diperbaharui',
                'sender' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }elseif (isset($_POST['approve'])){
            $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->get()[0]['name'];
            $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->get()[0]['name'];
            $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->get();
            
            if ($role == 'Wakil Rektor'){
                $next_approver = 3;
                $next_approver_origin = 2;
                $next_approver_unit = NULL;
            }elseif ($role == 'Dekan' Or ($role == 'Direktur' And $origin != 'Fungsi Pengelola Fasilitas Universitas')){
                $next_approver = 2;
                $next_approver_origin = 1;
                $next_approver_unit = 2;
            }elseif ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas'){
                $next_approver = 6;
                $next_approver_origin = 2;
                $next_approver_unit = 4;
            }

            \App\Models\Procurement::where('id', '=', $id)
                ->update([
                    'approver' => $next_approver,
                    'approver_origin' => $next_approver_origin,
                    'approver_unit' => $next_approver_unit,
                    'approval_status' => "Disetujui"
                ]);
            
            if($next_approver == 6 And $next_approver_unit == 4){
                $message = 'Pengadaan sudah didisposisikan ke Manajer Fungsi Pengadaan Barang dan Jasa';
            }else{
                $message = 'Pengadaan disetujui';
            }

            \App\Models\ProcLog::insert([
                'procurement' => $id, 
                'message' => $message, 
                'sender' => Auth::user()->id, 
                'created_at' => date('Y-m-d H:i:s'), 
                'updated_at' => date('Y-m-d H:i:s')
            ]);
                
            if($next_approver != 3 And $next_approver_origin != 2){
                if ($next_approver_unit == 2){
                    \App\Models\ProcLog::insert([
                        'procurement' => $id, 
                        'message' => "Menunggu persetujuan Wakil Rektor II", 
                        'sender' => Auth::user()->id, 
                        'created_at' => date('Y-m-d H:i:s'), 
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }else{
                    $approver = \App\Models\Procurement::join('origins', 'origins.id', '=', 'procurements.approver_origin')
                        ->join('roles', 'roles.id', '=', 'procurements.approver')
                        ->select('roles.name AS role', 'origins.name AS origin')
                        ->get()[0];
    
                    \App\Models\ProcLog::insert([
                        'procurement' => $id, 
                        'message' => "Menunggu persetujuan $approver->role $approver->origin", 
                        'sender' => Auth::user()->id, 
                        'created_at' => date('Y-m-d H:i:s'), 
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }elseif ($next_approver == 3 And $next_approver_origin == 2){
                \App\Models\ProcLog::insert([
                    'procurement' => $id, 
                    'message' => "Menunggu persetujuan Direktur Fungsi Pengelola Fasilitas Universitas", 
                    'sender' => Auth::user()->id, 
                    'created_at' => date('Y-m-d H:i:s'), 
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }elseif (isset($_POST['update_by_staff'])){
            \App\Models\Procurement::where('id', '=', $id)
                ->update([
                    'pic' => $request->pic,
                    'category' => $request->category,
                    'priority' => $request->priority
                ]);

            \App\Models\ProcLog::insert([
                'procurement' => $id,
                'message' => 'Informasi pengadaan telah diperbaharui',
                'sender' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }elseif (isset($_POST['assign'])){
            \App\Models\Procurement::where('id', '=', $id)
                ->update([
                    'pic' => $request->pic
                ]);
            
            $next_approver = 7;
            $next_approver_origin = 2;
            $next_approver_unit = 4;

            \App\Models\Procurement::where('id', '=', $id)
                ->update([
                    'approver' => $next_approver,
                    'approver_origin' => $next_approver_origin,
                    'approver_unit' => $next_approver_unit,
                    'approval_status' => "Disetujui"
                ]);

            \App\Models\ProcLog::insert([
                'procurement' => $id, 
                'message' => 'Pengadaan sudah diteruskan ke Staf Fungsi Pengadaan Barang dan Jasa', 
                'sender' => Auth::user()->id, 
                'created_at' => date('Y-m-d H:i:s'), 
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect(Route('show-procurement', ['id' => $id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function viewDoc($id)
    {
        $pdo = DB::getPdo();

        $row = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
        $row->bindParam(1, $id);

        $row->execute();

        $data = $row->fetch();
        
        return view('procurement.my.doc', [
            'data' => $data,
        ]);
    }

    public function docDestroy($proc_id, $id){
        \App\Models\Document::where('id', '=', $id)->delete();

        $documents = \App\Models\Document::where('procurement', '=', $proc_id)->get();

        return Redirect(Route('edit-procurement', ['id' => $proc_id]));
    }

    public function downloadTemplate(){
        return redirect( Url('/resc/TOR_Unit-List_Template.xlsx') );
    }
}
