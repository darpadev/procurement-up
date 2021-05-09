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
        $categories = \App\Models\ItemCategory::join('vendor_categories', 'vendor_categories.category', '=', 'item_categories.id')
            ->select('id', 'name')->distinct()->orderBy('name')->get();
        $sub_categories = \App\Models\ItemSubCategory::join('vendor_categories', 'vendor_categories.sub_category', '=', 'item_sub_categories.id')
            ->select('id', 'name', 'item_sub_categories.category')->distinct()->orderBy('name')->get();
        $vendors = \App\Models\Vendor::join('vendor_trecords', 'vendor_trecords.id', '=', 'vendors.track_record')
            ->join('vendor_categories', 'vendor_categories.vendor', '=', 'vendors.id')
            ->join('item_categories', 'item_categories.id', '=', 'vendor_categories.category')
            ->join('item_sub_categories', 'item_sub_categories.id', '=', 'vendor_categories.sub_category')
            ->select(
                'vendors.id', 
                'vendors.name',
                'vendors.phone',
                'vendors.email',
                'vendors.bank_account',
                'vendors.name_account',
                'vendor_categories.category',
                'vendor_categories.sub_category',
                'vendor_trecords.name AS trecord'
                )
            ->get();
        return view('vendor.list', [
            'categories' => $categories,
            'sub_categories' => $sub_categories,
            'vendors' => $vendors,
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
        //
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
        //
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
}
