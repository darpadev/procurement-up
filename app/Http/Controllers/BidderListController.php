<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class BidderListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendors = \App\Models\Vendor::join('vendor_trecords', 'vendor_trecords.id', '=', 'vendors.track_record')
            ->join('vendor_categories', 'vendor_categories.vendor', '=', 'vendors.id')
            ->join('item_categories', 'item_categories.id', '=', 'vendor_categories.category')
            ->join('item_sub_categories', 'item_sub_categories.id', '=', 'vendor_categories.sub_category')
            ->select(
                'vendors.*',
                'vendor_trecords.name AS trecord'
                )
            ->orderBy('vendors.name')
            ->distinct()
            ->paginate(25);
        $list_categories = \App\Models\ItemCategory::select('id', 'name')->orderBy('name')->get();
        $vendors_unknown = \App\Models\Vendor::leftJoin('vendor_categories', 'vendor_categories.vendor', '=', 'vendors.id')
            ->select('vendors.*')
            ->whereNull('vendor_categories.vendor')
            ->orderBy('vendors.name')
            ->get();
        $vendor_categories = \App\Models\VendorCategory::join('item_categories', 'item_categories.id', '=', 'vendor_categories.category')
            ->join('item_sub_categories', 'item_sub_categories.id', '=', 'vendor_categories.sub_category')
            ->select(
                'item_categories.id AS id_cat', 
                'item_sub_categories.id AS id_sub', 
                'item_categories.name AS category', 
                'item_sub_categories.name AS sub_category', 
                'vendor'
            )
            ->get();

        return view('vendor.list', [
            'list_categories' => $list_categories,
            'vendors' => $vendors,
            'vendors_unknown' => $vendors_unknown,
            'vendor_categories' => $vendor_categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vendor.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $track_records = \App\Models\VendorTrecord::select('id')->where('name', '=', 'Good')->get()[0];

        $vendor = new \App\Models\Vendor;

        $vendor->reg_code = $request->reg_code;
        $vendor->name = $request->name;
        $vendor->address = $request->address;
        $vendor->phone = $request->phone;
        $vendor->email = $request->email;
        $vendor->business_field = $request->business_field;
        $vendor->bank_account = $request->bank_account;
        $vendor->name_account = $request->name_account;
        $vendor->tin = $request->tin;
        $vendor->track_record = $track_records->id;

        $vendor->save();

        return Redirect(Route('bidder-list'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        if (isset($_POST['submit-category'])){
            $vendor = new \App\Models\VendorCategory;

            $vendor->vendor = $id;
            $vendor->category = $request->category;
            $vendor->sub_category = $request->sub_category;

            $vendor->save();
        }

        return Redirect(Route('bidder-list'));
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

    public function destroyVendorCategory($vendor, $category, $sub_category)
    {
        \App\Models\VendorCategory::where('vendor', '=', $vendor)
            ->where('category', '=', $category)
            ->where('sub_category', '=', $sub_category)
            ->delete();

        return redirect()->back();
    }

    public function setTenderWinner($procurement, $vendor)
    {
        $items          = \App\Models\Item::join('quotations', 'quotations.item_sub_category', 'items.sub_category')
                            ->select('items.*')
                            ->where('items.procurement', '=', $procurement)
                            ->where('quotations.vendor', '=', $vendor)
                            ->get();
        $quotation      = \App\Models\Quotation::join('vendors', 'vendors.id', '=', 'quotations.vendor')
                            ->select('quotations.*', 'vendors.name AS vendor_name')
                            ->where('quotations.procurement', '=', $procurement)
                            ->where('quotations.vendor', '=', $vendor)
                            ->first();

        return view('procurement.documents.bapp.set-winner', [
            'items' => $items,
            'quotation' => $quotation,
        ]);
    }

    public function setItemFinalPrice(Request $request)
    {
        $quotation = \App\Models\Quotation::where('procurement', '=', $request->procurement)
                        ->where('vendor', '=', $request->vendor)
                        ->update(['winner' => true]);

        $item = \App\Models\Item::find($request->items);

        $item->quotation_price  = $request->bidder_price;
        $item->nego_price       = $request->nego_price;
        $item->vendor_specs     = $request->vendor_specs;

        $item->save();            

        $vendor = \App\Models\Vendor::find($request->vendor);

        $log = new \App\Models\ProcLog;

        $log->procurement   = $request->procurement;
        $log->message       = "Pemenang Tender: $vendor->name";
        $log->sender        = Auth::user()->id;

        $log->save();

        $procurement = \App\Models\Procurement::find($request->procurement);

        $procurement->updated_at = date('Y-m-d H:i:s');

        $procurement->save();

        return Redirect(Route('show-procurement', ['id' => $request->procurement]));
    }

    public function setNotSuitable($procurement, $vendor)
    {
        $quotation = \App\Models\Quotation::where('procurement', '=', $procurement)
                        ->where('vendor', '=', $vendor)
                        ->update(['isSuitable' => false]);

        $vendor = \App\Models\Vendor::find($vendor);

        $log = new \App\Models\ProcLog;

        $log->procurement   = $procurement;
        $log->message       = "Penawaran tidak sesuai: $vendor->name";
        $log->sender        = Auth::user()->id;

        $log->save();

        $proc_id = $procurement;

        $procurement = \App\Models\Procurement::find($proc_id);

        $procurement->updated_at = date('Y-m-d H:i:s');

        $procurement->save();
        
        return redirect()->back();
    }
}
