<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Transaksi;
use App\Models\SaldoKeluar;
use App\Models\Kas_toko;
use App\Models\Rekening;
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

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $transaksi = Transaksi::all();
            $user = Auth::user();
            $roles = $user->getRoleNames();
            if($roles[0] == "kasir"){
                $data = Transaksi::get();

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
                ->editColumn('created_at',function($row){
                    return Carbon::parse($row->created_at)->format('j F Y');
                })
                ->editColumn('bank_uuid', function($row){
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
            $data = Transaksi::get();

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
                ->editColumn('created_at',function($row){
                    return Carbon::parse($row->created_at)->format('j F Y');
                })
                ->editColumn('bank_uuid', function($row){
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
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('transaksi.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('transaksi.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('transaksi.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tujuan = Bank::all()->pluck('name', 'name');
        $bank = Kas_toko::all()->pluck('bank_uuid', 'uuid');

        return view('transaksi.create', compact('tujuan', 'bank'));
    }

    public function cekRekeningTransaksi(Request $request)
    {
        $norek = $request->norek;
        $tujuan = $request->tujuan;

        // dd($norek,$bank);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://irfan.co.id/nama-rek/api");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"nomer=".$norek."&code=".$tujuan);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close ($ch);

        print_r($result);
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
            'bank_uuid' => 'required',
            'customer' => 'required',
            'norek' => 'required',
            'nama' => 'required',
            'customer' => 'required',
            'nominal'=>'required',
            'bank_tujuan' => 'required',
            'biaya_admin' => 'required',
            'admin_bank' => 'required',
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

        $admin_bank = $request->admin_bank;
        $formattedadminbank = str_replace(',', '', $admin_bank);

        $uniqueCode = Helper::GenerateReportNumber(13);

        $transaksi = new Transaksi();
        $transaksi->no_ref = 'TRK' . '-' . $uniqueCode;
        $transaksi->customer = $request->customer;
        $transaksi->bank_tujuan = $request->bank_tujuan;
        $transaksi->no_rek = $request->norek;
        $transaksi->nama_rekening = $request->nama;
        $transaksi->bank_uuid = $request->bank_uuid;
        $transaksi->biaya_admin = $formattedbiayaadmin;
        $transaksi->admin_bank = $formattedadminbank;
        $transaksi->keterangan = $request->keterangan;
        $transaksi->nominal = $formattednominal;
        $transaksi->created_by = Auth::user()->uuid;

        $transaksi->save();

        $saldokeluar = new SaldoKeluar();
        $saldokeluar->jenis_transaksi = 'Transfer';
        $saldokeluar->no_ref = $transaksi->no_ref;
        $saldokeluar->customer = $transaksi->customer;
        $saldokeluar->kas_uuid = $transaksi->bank_uuid;
        $saldokeluar->nominal = $transaksi->nominal;
        $saldokeluar->created_by = Auth::user()->uuid;

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $transaksi->bank_uuid)->value('saldo');
        $kasDB = $kasDB - $transaksi->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $transaksi->bank_uuid)->update(['saldo' => $kasDB]);

        $saldokeluar->save();

        toastr()->success('New Transaksi Added', 'Success');
        return redirect()->route('transaksi.index');
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
        $transaksi = Transaksi::uuid($id);
        $tujuan = Bank::all()->pluck('name', 'name');
        $bank = Kas_toko::all()->pluck('bank_uuid', 'uuid');

        return view('transaksi.edit', compact('transaksi', 'tujuan', 'bank'));
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
            'bank_uuid' => 'required',
            'customer' => 'required',
            'norek' => 'required',
            'nama' => 'required',
            'customer' => 'required',
            'nominal'=>'required',
            'bank_tujuan' => 'required',
            'biaya_admin' => 'required',
            'admin_bank' => 'required',
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

        $admin_bank = $request->admin_bank;
        $formattedadminbank = str_replace(',', '', $admin_bank);

        $uniqueCode = Helper::GenerateReportNumber(13);

        $transaksi = Transaksi::uuid($id);

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $transaksi->bank_uuid)->value('saldo');
        $kasDB = $kasDB + $transaksi->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $transaksi->bank_uuid)->update(['saldo' => $kasDB]);

        $saldokeluarDB = DB::table('saldo_keluars')->where('no_ref', 'like', $transaksi->no_ref)->delete();

        $transaksi->no_ref = 'TRK' . '-' . $uniqueCode;
        $transaksi->customer = $request->customer;
        $transaksi->bank_tujuan = $request->bank_tujuan;
        $transaksi->no_rek = $request->norek;
        $transaksi->nama_rekening = $request->nama;
        $transaksi->bank_uuid = $request->bank_uuid;
        $transaksi->biaya_admin = $formattedbiayaadmin;
        $transaksi->admin_bank = $formattedadminbank;
        $transaksi->keterangan = $request->keterangan;
        $transaksi->nominal = $formattednominal;
        $transaksi->edited_by = Auth::user()->uuid;

        $transaksi->save();

        $saldokeluar = new SaldoKeluar();
        $saldokeluar->jenis_transaksi = 'Transfer';
        $saldokeluar->no_ref = $transaksi->no_ref;
        $saldokeluar->customer = $transaksi->customer;
        $saldokeluar->kas_uuid = $transaksi->bank_uuid;
        $saldokeluar->nominal = $transaksi->nominal;
        $saldokeluar->created_by = Auth::user()->uuid;

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $transaksi->bank_uuid)->value('saldo');
        $kasDB = $kasDB - $transaksi->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $transaksi->bank_uuid)->update(['saldo' => $kasDB]);

        $saldokeluar->save();

        toastr()->success('Transaksi Edited', 'Success');
        return redirect()->route('transaksi.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaksi = Transaksi::uuid($id);
        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $transaksi->bank_uuid)->value('saldo');
        $kasDB = $kasDB + $transaksi->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $transaksi->bank_uuid)->update(['saldo' => $kasDB]);

        $saldokeluarDB = DB::table('saldo_keluars')->where('no_ref', 'like', $transaksi->no_ref)->delete();
        $transaksi->delete();

        toastr()->success('Transaksi Deleted', 'Success');
        return redirect()->route('transaksi.index');
    }
}
