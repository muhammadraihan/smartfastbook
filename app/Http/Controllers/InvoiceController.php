<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Perusahaan;

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

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoice = Invoice::all();
        if (request()->ajax()) {
            $data = Invoice::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('harga_satuan', function($row){
                    return $row->harga_satuan ? 'Rp.'.' '.number_format($row->harga_satuan,2) : '';
                })
                ->editColumn('perusahaan_uuid', function($row){
                    return $row->perusahaan->name;
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
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('invoice.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('invoice.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('invoice.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $perusahaan = Perusahaan::all()->pluck('name', 'uuid');
        return view('invoice.create', compact('perusahaan'));
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
            'perusahaan_uuid' => 'required',
            'tujuan' => 'required',
            'alamat' => 'required',
            'deskripsi' => 'required',
            'jumlah' => 'required',
            'harga_satuan' => 'required',
            'total' => 'required',
            'pajak' => 'required',
            'sub_total' => 'required',
            'catatan' => 'required',
            'tanggal_invoice' => 'required',
            'jatuh_tempo' => 'required'


        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $harga_satuan = $request->harga_satuan;
        $formattedharga = str_replace(',', '', $harga_satuan);

        $uniqueCode = Helper::GenerateInvoiceNumber(3);

        $invoice = new Invoice();
        $invoice->no_invoice = 'INV' . '/' . '2022' . '/' . $uniqueCode;
        $invoice->perusahaan_uuid = $request->perusahaan_uuid;
        $invoice->tujuan = $request->tujuan;
        $invoice->alamat = $request->alamat;
        
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

    public function print($id)
    {
        $invoice = Invoice::uuid($id);
        $perusahaan = Perusahaan::uuid($id);
        $logo = Perusahaan::select('logo')->where('uuid', 'like', $invoice->perusahaan_uuid)->first();

        return view('perusahaan.cetak', compact('invoice', 'perusahaan', 'logo'));
    }
}
