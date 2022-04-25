<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

use Auth;
use DataTables;
use URL;
use Helper;
use Image;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer = Customer::all();
        if (request()->ajax()) {
            $data = Customer::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('piutang', function($row){
                    return $row->piutang ? 'Rp.'.' '.number_format($row->piutang,2) : '';
                })
                ->editColumn('status', function($row){
                    switch ($row->status) {
                        case 'piutang' :
                            return '<span class="badge badge-danger">Piutang</span>';
                            break;
                        case 'lunas' :
                            return '<span class="badge badge-primary">Lunas</span>';
                            break;
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('customer.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('customer.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action','status'])
                ->make(true);
        }

        return view('customer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'telephone' => 'required',
            'alamat' => 'required',
            'status' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $piutang = $request->piutang;
        $formattedpiutang = str_replace(',', '', $piutang);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->telephone = $request->telephone;
        $customer->alamat = $request->alamat;
        $customer->piutang = $formattedpiutang;
        $customer->status = $request->status;

        $customer->save();

        toastr()->success('New Customer Added', 'Success');
        return redirect()->route('customer.index');
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
        $customer = Customer::uuid($id);
        return view('customer.edit', compact('customer'));
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
        $rules = [
            'name' => 'required',
            'telephone' => 'required',
            'alamat' => 'required',
            'status' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $piutang = $request->piutang;
        $formattedpiutang = str_replace(',', '', $piutang);

        $customer = Customer::uuid($id);
        $customer->name = $request->name;
        $customer->telephone = $request->telephone;
        $customer->alamat = $request->alamat;
        $customer->piutang = $formattedpiutang;
        $customer->status = $request->status;

        $customer->save();

        toastr()->success('Customer Edited', 'Success');
        return redirect()->route('customer.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::uuid($id);
        $customer->delete();

        toastr()->success('Customer Deleted', 'Success');
        return redirect()->route('customer.index');
    }
}
