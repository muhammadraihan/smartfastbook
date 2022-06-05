<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kas_toko;
use App\Models\Bank;
use Carbon\Carbon;

use Auth;
use DataTables;
use URL;
use Helper;
use Image;

class KasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $kas = Kas_toko::all();
            $data = Kas_toko::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('saldo', function($row){
                    return $row->saldo ? 'Rp.'.' '.number_format($row->saldo,2) : '';
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('Y-m-d');
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    return '
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('kas.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('kas.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('kas.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $bank = Bank::all()->pluck('name', 'name');
        return view('kas.create', compact('bank'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function cekrekening(Request $request)
    {
        $norek = $request->norek;
        $bank = $request->bank;

        // dd($norek,$bank);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://irfan.co.id/nama-rek/api");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"nomer=".$norek."&code=".$bank);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close ($ch);

        print_r($result);
    }

    
    public function store(Request $request)
    {
        $rules = [
            'bank_uuid' => 'required',
            'name' => 'required',
            'saldo'=>'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $saldo = $request->saldo;
        $formattedsaldo = str_replace(',', '', $saldo);

        $kas = new Kas_toko();
        $kas->bank_uuid = $request->bank_uuid;
        $kas->name = $request->name;
        $kas->no_rek = $request->norek;
        $kas->nama_rek = $request->nama;
        $kas->saldo = $formattedsaldo;
        $kas->created_by = Auth::user()->uuid;

        $kas->save();

        toastr()->success('New Kas Added', 'Success');
        return redirect()->route('kas.index');
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
        $kas = Kas_toko::uuid($id);
        $bank = Bank::all()->pluck('name', 'name');

        return view('kas.edit', compact('kas', 'bank'));
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
            'name' => 'required',
            'saldo'=>'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $saldo = $request->saldo;
        $formattedsaldo = str_replace(',', '', $saldo);

        $kas = Kas_toko::uuid($id);
        $kas->bank_uuid = $request->bank_uuid;
        $kas->name = $request->name;
        $kas->no_rek = $request->norek;
        $kas->nama_rek = $request->nama;
        $kas->saldo = $formattedsaldo;
        $kas->edited_by = Auth::user()->uuid;

        $kas->save();

        toastr()->success('Kas Edited', 'Success');
        return redirect()->route('kas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kas = Kas_toko::uuid($id);
        $kas->delete();

        toastr()->success('Kas Deleted', 'Success');
        return redirect()->route('kas.index');
    }
}