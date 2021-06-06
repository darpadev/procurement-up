<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function destroyVendorCategory($vendor, $category, $sub_category){
        \App\Models\VendorCategory::where('vendor', '=', $vendor)
            ->where('category', '=', $category)
            ->where('sub_category', '=', $sub_category)
            ->delete();

        return redirect()->back();
    }
}
