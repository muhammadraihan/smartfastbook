<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Kas_toko;
use App\Models\Saldo;
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

class SaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $saldo = Saldo::all();
        if (request()->ajax()) {
            $data = Saldo::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('nominal', function($row){
                    return $row->nominal ? 'Rp.'.' '.number_format($row->nominal,2) : '';
                })
                ->editColumn('kas_uuid', function($row){
                    return $row->kas->name;
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('Y-m-d');
                })
                ->addColumn('action', function ($row) {
                    return '
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('saldo.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('saldo.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('saldo.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kas = Kas_toko::all()->pluck('bank_uuid', 'uuid');
        return view('saldo.create', compact('kas'));
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

        $uniqueCode = Helper::GenerateReportNumber(13);

        $saldo = new Saldo();
        $saldo->no_ref = 'SALDO' . '-' . $uniqueCode;
        $saldo->kas_uuid = $request->kas_uuid;
        $saldo->nominal = $formattednominal;
        $saldo->keterangan = $request->keterangan;
        $saldo->created_by = Auth::user()->uuid;

        $saldo->save();

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $saldo->kas_uuid)->value('saldo');
        $kasDB = $kasDB + (int)$formattednominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $saldo->kas_uuid)->update(['saldo' => $kasDB]);
    
        toastr()->success('New Saldo Masuk Added', 'Success');
        return redirect()->route('saldo.index');
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
        $saldo = Saldo::uuid($id);
        return view('saldo.edit', compact('kas', 'saldo'));
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

        $uniqueCode = Helper::GenerateReportNumber(13);

        $saldo = Saldo::uuid($id);

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $saldo->kas_uuid)->value('saldo');
        $kasDB = $kasDB - $saldo->nominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $saldo->kas_uuid)->update(['saldo' => $kasDB]);
        
        $saldo->no_ref = 'SALDO' . '-' . $uniqueCode;
        $saldo->kas_uuid = $request->kas_uuid;
        $saldo->nominal = $formattednominal;
        $saldo->keterangan = $request->keterangan;
        $saldo->edited_by = Auth::user()->uuid;

        $saldo->save();

        $kasDB = DB::table('kas_tokos')->where('uuid', 'like', $saldo->kas_uuid)->value('saldo');
        $kasDB = $kasDB + (int)$formattednominal;
        $kasTotal = DB::table('kas_tokos')->where('uuid', 'like', $saldo->kas_uuid)->update(['saldo' => $kasDB]);


        toastr()->success('Saldo Masuk Edited', 'Success');
        return redirect()->route('saldo.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $saldo = Saldo::uuid($id);
        $saldo->delete();

        toastr()->success('Saldo Masuk Deleted', 'Success');
        return redirect()->route('saldo.index');
    }

    public function filter(Request $request)
    {
        if (request()->all()) {
            $start_date = Carbon::parse(request()->start_date)->toDateTimeString();
            $end_date = Carbon::parse(request()->end_date)->toDateTimeString();
            $data = Saldo::whereBetween('created_at',[$start_date,$end_date])->get();   

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('nominal', function($row){
                    return $row->nominal ? 'Rp.'.' '.number_format($row->nominal,2) : '';
                })
                ->editColumn('kas_uuid', function($row){
                    return $row->kas->name;
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('Y-m-d');
                })
                ->addColumn('action', function ($row) {
                    return '
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('saldo.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('saldo.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
