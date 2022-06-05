<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarikTunai;
use App\Models\Bank;
use App\Models\Kas_toko;
use App\Models\Saldokeluar;

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

class TarikTunaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $tariktunai = TarikTunai::all();
            $user = Auth::user();
            $roles = $user->getRoleNames();
            if($roles[0] == "kasir"){
                $data = TarikTunai::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('nominal', function($row){
                    return $row->nominal ? 'Rp.'.' '.number_format($row->nominal,2) : '';
                })
                ->editColumn('biaya_admin', function($row){
                    return $row->biaya_admin ? 'Rp.'.' '.number_format($row->biaya_admin,2) : '';
                })
                ->editColumn('admin_bank', function($row){
                    return $row->admin_bank ? 'Rp.'.' '.number_format($row->admin_bank,2) : '';
                })
                ->editColumn('bank_uuid', function($row){
                    return $row->bank->name;
                })
                ->editColumn('jenis_pembayaran', function($row){
                    return $row->kas->bank_uuid;
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
            $data = TarikTunai::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('nominal', function($row){
                    return $row->nominal ? 'Rp.'.' '.number_format($row->nominal,2) : '';
                })
                ->editColumn('biaya_admin', function($row){
                    return $row->biaya_admin ? 'Rp.'.' '.number_format($row->biaya_admin,2) : '';
                })
                ->editColumn('admin_bank', function($row){
                    return $row->admin_bank ? 'Rp.'.' '.number_format($row->admin_bank,2) : '';
                })
                ->editColumn('bank_uuid', function($row){
                    return $row->bank->name;
                })
                ->editColumn('jenis_pembayaran', function($row){
                    return $row->kas->bank_uuid;
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    return '
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('tariktunai.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('tariktunai.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('tariktunai.index');
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
        return view('tariktunai.create', compact('bank','kas'));
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
            'no_kartu' => 'required',
            'bank_uuid' => 'required',
            'biaya_admin' => 'required',
            'nominal' => 'required'

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

        $tariktunai = new TarikTunai();
        $tariktunai->no_ref = 'TUNAI' . '-' . $uniqueCode;
        $tariktunai->customer = $request->customer;
        $tariktunai->nominal = $formattednominal;
        $tariktunai->bank_uuid = $request->bank_uuid;
        $tariktunai->no_kartu = $request->no_kartu;
        $tariktunai->biaya_admin = $formattedbiayaadmin;
        $tariktunai->jenis_pembayaran = $request->jenis_pembayaran;
        $tariktunai->keterangan = $request->keterangan;
        $tariktunai->created_by = Auth::user()->uuid;

        $tariktunai->save();

        $saldokeluar = new SaldoKeluar();
        $saldokeluar->jenis_transaksi = 'Tarik Tunai';
        $saldokeluar->no_ref = $tariktunai->no_ref;
        $saldokeluar->customer = $tariktunai->customer;
        $saldokeluar->kas_uuid = $tariktunai->jenis_pembayaran;
        $saldokeluar->nominal = $tariktunai->nominal;
        $saldokeluar->created_by = Auth::user()->uuid;

        $saldokeluar->save();


        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $tariktunai->jenis_pembayaran)->value('saldo');
        $kasDB = $kasDB - $tariktunai->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $tariktunai->jenis_pembayaran)->update(['saldo' => $kasDB]);

        toastr()->success('New Tarik Tunai Added', 'Success');
        return redirect()->route('tariktunai.index');
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
        $kas = Kas_toko::all()->pluck('bank_uuid', 'uuid');
        $bank = Bank::all()->pluck('name', 'uuid');

        $tariktunai = TarikTunai::uuid($id);

        return view('tariktunai.edit', compact('bank','tariktunai', 'kas'));
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
            'no_kartu' => 'required',
            'bank_uuid' => 'required',
            'biaya_admin' => 'required',
            'jenis_pembayaran' => 'required',
            'nominal' => 'required'

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

        $tariktunai = TarikTunai::uuid($id);
        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $tariktunai->jenis_pembayaran)->value('saldo');
        $kasDB = $kasDB + $tariktunai->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $tariktunai->jenis_pembayaran)->update(['saldo' => $kasDB]);

        $saldokeluarDB = DB::table('saldo_keluars')->where('no_ref', 'like', $tariktunai->no_ref)->delete();

        $tariktunai->customer = $request->customer;
        $tariktunai->nominal = $formattednominal;
        $tariktunai->bank_uuid = $request->bank_uuid;
        $tariktunai->no_kartu = $request->no_kartu;
        $tariktunai->biaya_admin = $formattedbiayaadmin;
        $tariktunai->jenis_pembayaran = $request->jenis_pembayaran;
        $tariktunai->keterangan = $request->keterangan;
        $tariktunai->edited_by = Auth::user()->uuid;

        $tariktunai->save();

        $saldokeluar = new SaldoKeluar();
        $saldokeluar->jenis_transaksi = 'Tarik Tunai';
        $saldokeluar->no_ref = $tariktunai->no_ref;
        $saldokeluar->customer = $tariktunai->customer;
        $saldokeluar->kas_uuid = $tariktunai->jenis_pembayaran;
        $saldokeluar->nominal = $tariktunai->nominal;
        $saldokeluar->created_by = Auth::user()->uuid;

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $tariktunai->jenis_pembayaran)->value('saldo');
        $kasDB = $kasDB - $tariktunai->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $tariktunai->jenis_pembayaran)->update(['saldo' => $kasDB]);

        $saldokeluar->save();

        toastr()->success('Tarik Tunai Edited', 'Success');
        return redirect()->route('tariktunai.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tariktunai = TarikTunai::uuid($id);
        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $tariktunai->jenis_pembayaran)->value('saldo');
        $kasDB = $kasDB + $tariktunai->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $tariktunai->jenis_pembayaran)->update(['saldo' => $kasDB]);

        $saldokeluarDB = DB::table('saldo_keluars')->where('no_ref', '=', $tariktunai->no_ref)->delete();
        $tariktunai->delete();

        toastr()->success('Tarik Tunai Deleted', 'Success');
        return redirect()->route('tariktunai.index');
    }
}
