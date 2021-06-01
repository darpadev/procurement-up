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

        if (($role == 'Wakil Rektor' And (isset($unit[0]->name) And $unit[0]->name == 'Bidang Keuangan dan Sumber Daya Organisasi Universitas Pertamina')) Or ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas') Or (($role == 'Manajer' Or $role == 'Staf') And $unit[0]->name == 'Fungsi Pengadaan Barang dan Jasa')){
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
        $mechanisms = \App\Models\ProcMechanism::where('name', '=', 'Tender Terbuka')->first();

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
        $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->first()['name'];
        $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->first()['name'];
        $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->first();

        /**
         * Find ID with Role
         * "Direktur"
         */
        $role_direktur = \App\Models\Role::select('id')
                            ->where('name', '=', 'Direktur')
                            ->first()['id'];

        /**
         * Find ID with Role
         * "Wakil Rektor"
         */
        $role_wr = \App\Models\Role::select('id')
                                        ->where('name', '=', 'Wakil Rektor')
                                        ->first()['id'];

        /**
         * Find ID with Origin
         * "Rektorat"
         */
        $origin_wr = \App\Models\Origin::select('id')
                                            ->where('name', '=', 'Rektorat')
                                            ->first()['id'];
        
        /**
         * Find ID with Unit
         * "Bidang Keuangan dan Sumber Daya Organisasi"
         */
        $unit_wr_2 = \App\Models\Unit::select('id')
                                        ->where('name', '=', 'Bidang Keuangan dan Sumber Daya Organisasi Universitas Pertamina')
                                        ->first()['id'];

        if(isset($_POST['submit'])){
            if ($role == 'Wakil Rektor'){
                if($unit->name == 'Bidang Keuangan dan Sumber Daya Organisasi Universitas Pertamina'){
                    /**
                     * Find ID with Origin
                     * "Fungsi Pengelola Fasilitas Universitas"
                     */
                    $direktur_pfu = \App\Models\Origin::select('id')
                                        ->where('name', '=', 'Fungsi Pengelola Fasilitas Universitas')
                                        ->first()['id'];

                    $approver = $role_direktur;
                    $approver_origin = $direktur_pfu;
                    $approver_unit = NULL;
                }else{
                    $approver = $role_wr;
                    $approver_origin = $origin_wr;
                    $approver_unit = $unit_wr_2;
                }
            }elseif ($role == 'Direktur' Or $role == 'Dekan'){
                $approver = $role_wr;
                $approver_origin = $origin_wr;
                $approver_unit = $unit_wr_2;
            }elseif ($role == 'Kaprodi'){
                /**
                 * Find ID with Role
                 * "Dekan"
                 */
                $role_dekan = \App\Models\Role::select('id')
                                                    ->where('name', '=', 'Dekan')
                                                    ->first()['id'];
                $approver = $role_dekan;
                $approver_origin = Auth::user()->origin;
                $approver_unit = NULL;
            }elseif ($role == 'Manajer'){
                if ($request->value <= 200000000){
                    $approver = $role_direktur;
                    $approver_origin = Auth::user()->origin;
                    $approver_unit = NULL;
                }else{
                    $approver = $role_wr;
                    $approver_origin = $origin_wr;
                    $approver_unit = $unit_wr_2;
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

            $log = new \App\Models\ProcLog;

            if(isset($approver_unit) And $approver_unit == $unit_wr_2){
                // If approver == "Wakil Rektor 2"
                $log->procurement   = $proc_id;
                $log->message       = 'Menunggu persetujuan Wakil Rektor II';
                $log->sender        = Auth::user()->id;

                $log->save();               
            }else{
                // If approver is not "Wakil Rektor II"
                $approver = \App\Models\Procurement::join('origins', 'origins.id', '=', 'procurements.approver_origin')
                    ->join('roles', 'roles.id', '=', 'procurements.approver')
                    ->select('roles.name AS role', 'origins.name AS origin')
                    ->where('procurements.id', '=', $proc_id)
                    ->get()[0];

                    $log->procurement   = $proc_id;
                    $log->message       = "Menunggu persetujuan $approver->role $approver->origin";
                    $log->sender        = Auth::user()->id;
    
                    $log->save();
            }
                
            $pdo = DB::getPdo();

            $name = $_FILES['tor']['name'];
            $doc_type = $_FILES['tor']['type'];
            $doc = file_get_contents($_FILES['tor']['tmp_name']);
            $date = date('Y-m-d H:i:s');
            $type = "tor";
    
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
        $log_dates = \App\Models\ProcLog::select('created_at')->distinct()->orderBy('id', 'DESC')->get();
        $logs = \App\Models\ProcLog::join('users', 'users.id', '=', 'proc_logs.sender')
            ->select('users.name', 'message', 'proc_logs.created_at')
            ->where('procurement', '=', $id)
            ->orderBy('proc_logs.id', 'DESC')
            ->get();
        $item_categories = \App\Models\ItemCategory::select('id', 'name')->orderBy('name')->get();
        $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->first()['name'];
        $procurement = \App\Models\Procurement::leftJoin('proc_mechanisms', 'proc_mechanisms.id', '=', 'procurements.mechanism')
            ->leftJoin('statuses', 'statuses.id', '=', 'procurements.status')
            ->leftJoin('proc_categories', 'proc_categories.id', '=', 'procurements.category')
            ->select('procurements.*', 'proc_mechanisms.name AS mech_name', 'statuses.name AS status_name', 'proc_categories.name AS category_name')
            ->where('procurements.id', '=', $id)
            ->get()[0];      
        $quotations = \App\Models\Quotation::join('vendors', 'vendors.id', '=', 'quotations.vendor')
            ->select('quotations.*', 'vendors.name AS vendor_name')
            ->orderBy('quotations.winner', 'DESC')
            ->orderBy('vendors.name', 'ASC')
            ->where('quotations.procurement', '=', $id)
            ->get();
        $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->first()['name'];
        $spec = array('available' => false, 'index' => 0);
        $tor = array('available' => false, 'index' => 0);
        $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->first();
        $vendor_docs = \App\Models\VendorDoc::where('procurement', '=', $id)->get();
        $vendors = \App\Models\Vendor::join('vendor_categories', 'vendor_categories.vendor', '=', 'vendors.id')->select('id', 'name', 'category', 'sub_category')->orderBy('name')->get();
        $category = \App\Models\ProcCategory::select('name')->where('id', '=', $procurement->category)->get();
        $documents = \App\Models\Document::where('procurement', '=', $procurement->id)->get();
        $items = \App\Models\Item::leftJoin('item_categories', 'item_categories.id', '=', 'items.category')
            ->leftJoin('item_sub_categories', 'item_sub_categories.id', '=', 'items.sub_category')
            ->select('items.*')
            ->where('procurement', '=', $procurement->id)->get();
        $pic = \App\Models\User::select('name')->where('id', '=', $procurement->pic)->get();
        $priority = \App\Models\Priority::select('name')->where('id', '=', $procurement->priority)->get();

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
            'category' => $category,
            'documents' => $documents,
            'log_dates' => $log_dates,
            'logs' => $logs,
            'item_categories' => $item_categories,
            'items' => $items,
            'origin' => $origin,
            'pic' => $pic,
            'priority' => $priority,
            'procurement' => $procurement,
            'quotations' => $quotations,
            'role' => $role,
            'spec' => $spec,
            'tor' => $tor,
            'unit' => $unit,
            'vendor_docs' => $vendor_docs,
            'vendors' => $vendors,
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
        $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->first()['name'];
        $origin = \App\Models\Origin::select('id', 'name')->where('id', '=', Auth::user()->origin)->first()['name'];
        $unit = \App\Models\Unit::select('name')->where('id', '=', Auth::user()->unit)->first();

        if ($role == 'Staf' And $origin == 'Fungsi Pengelola Fasilitas Universitas' And $unit->name == 'Fungsi Pengadaan Barang dan Jasa'){            
            $procurement = \App\Models\Procurement::select('id', 'name', 'pic', 'category', 'priority')->where('id', '=', $id)->first();
            $categories = \App\Models\ProcCategory::select('id', 'name')->get();
            $priorities = \App\Models\Priority::select('id', 'name')->get();
            $pic = \App\Models\User::select('id', 'name')->where('origin', '=', $role->id)->where('unit', '=', 4)->get();

            return view('procurement.my.edit', [
                'procurement' => $procurement,
                'categories' => $categories,
                'priorities' => $priorities,
                'pic' => $pic,
            ]);
        }else{
            $procurement = \App\Models\Procurement::where('id', '=', $id)->first();
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
            $procurement = \App\Models\Procurement::find($id);

            $procurement->ref   = $request->ref;
            $procurement->name  = $request->name;
            $procurement->value = $request->value;

            $procurement->save();
        
            if (!$_FILES['tor']['error']) {
                $pdo = DB::getPdo();

                $name = $_FILES['tor']['name'];
                $doc_type = $_FILES['tor']['type'];
                $doc = file_get_contents($_FILES['tor']['tmp_name']);
                $date = date('Y-m-d H:i:s');
                $type = "tor";
        
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

            if (!$_FILES['spec']['error']){
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

            $log = new \App\Models\ProcLog;

            $log->procurement   = $id;
            $log->message       = 'Informasi pengadaan telah diperbaharui';
            $log->sender        = Auth::user()->id;

            $log->save();

        }elseif (isset($_POST['approve'])){
            $role = \App\Models\Role::select('name')->where('id', '=', Auth::user()->role)->first()['name'];
            $origin = \App\Models\Origin::select('name')->where('id', '=', Auth::user()->origin)->first()['name'];

            /**
             * Find ID with Role
             * "Direktur"
             */
            $role_direktur = \App\Models\Role::select('id')
                                ->where('name', '=', 'Direktur')
                                ->first()['id'];

            /**
             * Find ID with Origin 
             * "Fungsi Pengelola Fasilitas Universitas"
             */
            $direktur_pfu = \App\Models\Origin::select('id')
                                ->where('name', '=', 'Fungsi Pengelola Fasilitas Universitas')
                                ->first()['id'];

            /**
             * Find ID with Role 
             * "Wakil Rektor"
             */
            $role_wr = \App\Models\Role::select('id')
                            ->where('name', '=', 'Wakil Rektor')
                            ->first()['id'];
            
            /**
             * Find ID with Origin 
             * "Rektorat"
             */
            $origin_wr = \App\Models\Origin::select('id')
                            ->where('name', '=', 'Rektorat')
                            ->first()['id'];
            
            /**
             * Find ID with Unit
             * "Bidang Keuangan dan Sumber Daya Organisasi Universitas Pertamina" 
             * (Wakil Rektor 2)
             */
            $unit_wr_2 = \App\Models\Unit::select('id')
                        ->where('name', '=', 'Bidang Keuangan dan Sumber Daya Organisasi Universitas Pertamina')
                        ->first()['id'];

            /**
             * Find ID with Role
             * "Manajer"
             */
            $role_manajer = \App\Models\Role::select('id')
                                ->where('name', '=', 'Manajer')
                                ->first()['id'];

            /**
             * Find ID with Origin
             * "Fungsi Pengelola Fasilitas Universitas"
             */
            $origin_pfu = \App\Models\Origin::select('id')
                            ->where('name', '=', 'Fungsi Pengelola Fasilitas Universitas')
                            ->first()['id'];

            /**
             * Find ID wiht Unit
             * "Fungsi Pengadaan Barang dan Jasa"
             */
            $unit_pengadaan = \App\Models\Unit::select('id')
                                ->where('name', '=', 'Fungsi Pengadaan Barang dan Jasa')
                                ->first()['id'];
            
            if ($role == 'Wakil Rektor'){
                /** 
                 * If current approver is "Wakil Rektor", 
                 * then next approver should be "Direktur Fungsi Pengelola Fasilitas Universitas"
                */
                $next_approver = $role_direktur;
                $next_approver_origin = $direktur_pfu;
                $next_approver_unit = NULL;
            }elseif ($role == 'Dekan' Or ($role == 'Direktur' And $origin != 'Fungsi Pengelola Fasilitas Universitas')){
                /**
                 * If current approver is "Dekan" or "Direktur", 
                 * then next approver should be "Wakil Rektor 2"
                 */
                $next_approver = $role_wr;
                $next_approver_origin = $origin_wr;
                $next_approver_unit = $unit_wr_2;
            }elseif ($role == 'Direktur' And $origin == 'Fungsi Pengelola Fasilitas Universitas'){
                /**
                 * If current approver is "Direktur Fungsi Pengelola Fasilitas Universitas", 
                 * then next approver should be "Manajer Pengadaan Barang dan Jasa
                 */
                $next_approver = $role_manajer;
                $next_approver_origin = $origin_pfu;
                $next_approver_unit = $unit_pengadaan;
            }

            $procurement = \App\Models\Procurement::find($id);

            $procurement->approver          = $next_approver;
            $procurement->approver_origin   = $next_approver_origin;
            $procurement->approver_unit     = $next_approver_unit;
            $procurement->editable          = false;

            $procurement->save();

            $log = new \App\Models\ProcLog;

            $log->procurement   = $id;
            $log->sender        = Auth::user()->id;

            if ($next_approver_unit == $unit_wr_2){
                $log->message   = 'Pengadaan disetujui. Menunggu persetujuan Wakil Rektor II';
            }elseif ($next_approver == $role_direktur And $next_approver_origin == $direktur_pfu){
                $log->message   = 'Pengadaan sudah didisposisikan ke Direktur Fungsi Pengelola Fasilitas Universitas';
            }elseif ($next_approver == $role_manajer And $next_approver_unit == $unit_pengadaan){
                $log->message   = 'Pengadaan sudah didisposisikan ke Manajer Fungsi Pengadaan Barang dan Jasa';
            }

            $log->save();

        }elseif (isset($_POST['update_by_staff'])){
            $procurement = \App\Models\Procurement::find($id);

            $procurement->category  = $request->category;
            $procurement->priority  = $request->priority;

            $procurement->save();

            $log = new \App\Models\ProcLog;

            $log->procurement   = $id;
            $log->message       = 'Informasi pengadaan telah diperbaharui';
            $log->sender        = Auth::user()->id;

            $log->save();
        }elseif (isset($_POST['assign'])){
            $procurement = \App\Models\Procurement::find($id);

            $procurement->pic = $request->pic;

            $procurement->save();
            
            /**
             * Find ID with Role
             * "Staf"
             */
            $role_staf = \App\Models\Role::select('id')
                            ->where('name', '=', 'Staf')
                            ->first()['id'];
            
            /**
             * Find ID with Origin
             * "Fungsi Pengelola Fasilitas Universitas"
             */
            $origin_staf = \App\Models\Origin::select('id')
                                ->where('name', '=', 'Fungsi Pengelola Fasilitas Universitas')
                                ->first()['id'];
            
            /**
             * Find ID with Unit
             * "Fungsi Pengadaan Barang dan Jasa"
             */
            $unit_staf = \App\Models\Unit::select('id')
                            ->where('name', '=', 'Fungsi Pengadaan Barang dan Jasa')
                            ->first()['id'];

            $procurement->approver          = $role_staf;
            $procurement->approver_origin   = $origin_staf;
            $procurement->approver_unit     = $unit_staf;

            $procurement->save();

            $log = new \App\Models\ProcLog;

            $log->procurement   = $id;
            $log->message       = 'Pengadaan sudah diteruskan ke Staf Fungsi Pengadaan Barang dan Jasa';
            $log->sender        = Auth::user()->id;

            $log->save();
        }

        return Redirect(Route('show-procurement', ['id' => $id]));
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

    public function addItemCategory(Request $request, $id){
        $item = \App\Models\Item::find($id);

        $item->category = $request->category;
        $item->sub_category = $request->sub_category;

        $item->save();

        return redirect()->back();
    }

    public function addItemVendor(Request $request){
        $quotation = new \App\Models\Quotation;

        $quotation->procurement = $request->procurement_id;
        $quotation->item        = $request->item_id;
        $quotation->vendor      = $request->vendor;

        $quotation->save();

        $log = new \App\Models\ProcLog;

        $log->procurement   = $request->procurement_id;
        $log->message       = 'Vendor baru telah ditambahkan';
        $log->sender        = Auth::user()->id;

        $log->save();

        $procurement = \App\Models\Procurement::find($request->procurement_id);

        $procurement->updated_at = date('Y-m-d H:i:s');

        $procurement->save();
        
        return Redirect()->back();
    }

    public function deleteItemVendor(Request $request){
        $quotation = \App\Models\Quotation::find($request->id);

        $quotation->delete();

        $log = new \App\Models\ProcLog;

        $log->procurement   = $request->procurement_id;
        $log->message       = 'Vendor terdaftar telah dihapus';
        $log->sender        = Auth::user()->id;

        $log->save();

        $procurement = \App\Models\Procurement::find($request->procurement_id);

        $procurement->updated_at = date('Y-m-d H:i:s');

        $procurement->save();

        return Redirect()->Back();
    }
}
