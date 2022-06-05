<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ewallet;
use App\Models\Kas_toko;
use App\Models\Payment;
use App\Models\Bank;
use App\Models\SaldoKeluar;
use Carbon\Carbon;

use Auth;
use DataTables;
use DB;
use File;
use Hash;
use Image;
use Response;
use URL;
use PDF;
use Helper;

class EWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $user = Auth::user();
            $roles = $user->getRoleNames();
            $ewallet = Ewallet::all();
            if($roles[0] == "kasir"){
                $data = Ewallet::get();

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
                ->editColumn('jenis_pembayaran', function($row){
                    return $row->kas->bank_uuid;
                })
                ->editColumn('ewallet', function($row){
                    return $row->payment->name;
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('d M Y');
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    ;
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
            }
            $data = Ewallet::get();

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
                ->editColumn('jenis_pembayaran', function($row){
                    return $row->kas->bank_uuid;
                })
                ->editColumn('ewallet', function($row){
                    return $row->payment->name;
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
                ->rawColumns(['action'])
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
        $kas = Kas_toko::all()->pluck('bank_uuid', 'uuid');
        $bank = Bank::all()->pluck('name', 'uuid');
        $payment = Payment::all()->pluck('name', 'uuid');

        return view('ewallet.create', compact('bank', 'payment', 'kas'));

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
            'biaya_admin' => 'required',
            'jenis_pembayaran' => 'required',
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

        $ewallet = new Ewallet();
        $ewallet->no_ref = 'E-WALLET' . '-' . $uniqueCode;
        $ewallet->customer = $request->customer;
        $ewallet->no_hp = $request->no_hp;
        $ewallet->pemilik = $request->pemilik;
        $ewallet->ewallet = $request->ewallet;
        $ewallet->payment_methode = $request->payment_methode;
        $ewallet->bank_uuid = $request->bank_uuid;
        $ewallet->biaya_admin = $formattedbiayaadmin;
        $ewallet->nominal = $formattednominal;
        $ewallet->jenis_pembayaran = $request->jenis_pembayaran;
        $ewallet->keterangan = $request->keterangan;
        $ewallet->created_by = Auth::user()->uuid;

        $ewallet->save();

        $saldokeluar = new SaldoKeluar();
        $saldokeluar->jenis_transaksi = 'E-Wallet';
        $saldokeluar->no_ref = $ewallet->no_ref;
        $saldokeluar->customer = $ewallet->customer;
        $saldokeluar->kas_uuid = $ewallet->jenis_pembayaran;
        $saldokeluar->nominal = $ewallet->nominal;
        $saldokeluar->created_by = Auth::user()->uuid;

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $ewallet->jenis_pembayaran)->value('saldo');
        $kasDB = $kasDB - $ewallet->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $ewallet->jenis_pembayaran)->update(['saldo' => $kasDB]);

        $saldokeluar->save();

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
        $kas = Kas_toko::all()->pluck('bank_uuid', 'uuid');
        $ewallet = Ewallet::uuid($id);

        return view('ewallet.edit', compact('bank', 'payment', 'ewallet', 'kas'));
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
            'biaya_admin' => 'required',
            'jenis_pembayaran' => 'required',
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

        $ewallet = Ewallet::uuid($id);

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $ewallet->jenis_pembayaran)->value('saldo');
        $kasDB = $kasDB + $ewallet->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $ewallet->jenis_pembayaran)->update(['saldo' => $kasDB]);

        $saldokeluarDB = DB::table('saldo_keluars')->where('no_ref', 'like', $ewallet->no_ref)->delete();

        $ewallet->customer = $request->customer;
        $ewallet->no_hp = $request->no_hp;
        $ewallet->pemilik = $request->pemilik;
        $ewallet->ewallet = $request->ewallet;
        $ewallet->payment_methode = $request->payment_methode;
        $ewallet->bank_uuid = $request->bank_uuid;
        $ewallet->biaya_admin = $formattedbiayaadmin;
        $ewallet->nominal = $formattednominal;
        $ewallet->jenis_pembayaran = $request->jenis_pembayaran;
        $ewallet->keterangan = $request->keterangan;
        $ewallet->edited_by = Auth::user()->uuid;

        $ewallet->save();

        $saldokeluar = new SaldoKeluar();
        $saldokeluar->jenis_transaksi = 'E-Wallet';
        $saldokeluar->no_ref = $ewallet->no_ref;
        $saldokeluar->customer = $ewallet->customer;
        $saldokeluar->kas_uuid = $ewallet->jenis_pembayaran;
        $saldokeluar->nominal = $ewallet->nominal;
        $saldokeluar->created_by = Auth::user()->uuid;

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $ewallet->jenis_pembayaran)->value('saldo');
        $kasDB = $kasDB - $ewallet->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $ewallet->jenis_pembayaran)->update(['saldo' => $kasDB]);

        $saldokeluar->save();

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
        $ewallet = Ewallet::uuid($id);
        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $ewallet->jenis_pembayaran)->value('saldo');
        $kasDB = $kasDB + $ewallet->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $ewallet->jenis_pembayaran)->update(['saldo' => $kasDB]);

        $saldokeluarDB = DB::table('saldo_keluars')->where('no_ref', 'like', $ewallet->no_ref)->delete();
        $ewallet->delete();

        toastr()->success('E-Wallet Deleted', 'Success');
        return redirect()->route('ewallet.index');
    }
}
