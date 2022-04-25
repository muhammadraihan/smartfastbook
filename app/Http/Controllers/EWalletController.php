<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EWallet;
use App\Models\Bank;
use App\Models\Payment;

use Auth;
use DataTables;
use URL;
use Helper;
use Image;

class EWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ewallet = EWallet::all();
        if (request()->ajax()) {
            $data = EWallet::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('nominal', function($row){
                    return $row->nominal ? 'Rp.'.' '.number_format($row->nominal,2) : '';
                })
                ->editColumn('biaya_admin', function($row){
                    return $row->biaya_admin ? 'Rp.'.' '.number_format($row->biaya_admin,2) : '';
                })
                ->editColumn('bank_uuid', function($row){
                    return $row->bank->name ?? null;
                })
                ->editColumn('ewallet', function($row){
                    return $row->payment->name;
                })
                ->editColumn('payment_methode', function($row){
                    switch ($row->payment_methode) {
                        case '0' :
                            return '<span class="badge badge-danger">Tunai</span>';
                            break;
                        case '1' :
                            return '<span class="badge badge-primary">Bank</span>';
                            break;
                    }
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    return '
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('ewallet.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('ewallet.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action','payment_methode'])
                ->make(true);
        }

        return view('ewallet.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $bank = Bank::all()->pluck('name', 'uuid');
        $payment = Payment::all()->pluck('name', 'uuid');

        return view('ewallet.create', compact('bank', 'payment'));

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
            'customer' => 'required',
            'no_hp' => 'required',
            'pemilik' => 'required',
            'ewallet' => 'required',
            'payment_methode' => 'required',
            'biaya_admin' => 'required',
            'nominal' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $nominal = $request->nominal;
        $formattednominal = str_replace(',', '', $nominal);

        $biaya_admin = $request->biaya_admin;
        $formattedbiayaadmin = str_replace(',', '', $biaya_admin);


        $uniqueCode = Helper::GenerateReportNumber(13);

        $ewallet = new EWallet();
        $ewallet->no_ref = 'EWL' . '-' . $uniqueCode;
        $ewallet->customer = $request->customer;
        $ewallet->no_hp = $request->no_hp;
        $ewallet->pemilik = $request->pemilik;
        $ewallet->ewallet = $request->ewallet;
        $ewallet->payment_methode = $request->payment_methode;
        $ewallet->bank_uuid = $request->bank_uuid;
        $ewallet->biaya_admin = $formattedbiayaadmin;
        $ewallet->nominal = $formattednominal;
        $ewallet->keterangan = $request->keterangan;
        $ewallet->created_by = Auth::user()->uuid;

        $ewallet->save();

        toastr()->success('New E-Wallet Added', 'Success');
        return redirect()->route('ewallet.index');
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
        $bank = Bank::all()->pluck('name', 'uuid');
        $payment = Payment::all()->pluck('name', 'uuid');
        $ewallet = EWallet::uuid($id);

        return view('ewallet.edit', compact('bank', 'payment', 'ewallet'));
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
            'customer' => 'required',
            'no_hp' => 'required',
            'pemilik' => 'required',
            'ewallet' => 'required',
            'payment_methode' => 'required',
            'biaya_admin' => 'required',
            'nominal' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $nominal = $request->nominal;
        $formattednominal = str_replace(',', '', $nominal);

        $biaya_admin = $request->biaya_admin;
        $formattedbiayaadmin = str_replace(',', '', $biaya_admin);


        $uniqueCode = Helper::GenerateReportNumber(13);

        $ewallet = EWallet::uuid($id);
        $ewallet->no_ref = 'EWL' . '-' . $uniqueCode;
        $ewallet->customer = $request->customer;
        $ewallet->no_hp = $request->no_hp;
        $ewallet->pemilik = $request->pemilik;
        $ewallet->ewallet = $request->ewallet;
        $ewallet->payment_methode = $request->payment_methode;
        $ewallet->bank_uuid = $request->bank_uuid;
        $ewallet->biaya_admin = $formattedbiayaadmin;
        $ewallet->nominal = $formattednominal;
        $ewallet->keterangan = $request->keterangan;
        $ewallet->edited_by = Auth::user()->uuid;

        $ewallet->save();

        toastr()->success('E-Wallet Edited', 'Success');
        return redirect()->route('ewallet.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ewallet = EWallet::uuid($id);
        $ewallet->delete();

        toastr()->success('E-Wallet Deleted', 'Success');
        return redirect()->route('ewallet.index');
    }
}
