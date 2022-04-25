<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarikTunai;
use App\Models\Bank;

use Auth;
use DataTables;
use URL;
use Helper;
use Image;

class TarikTunaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tariktunai = TarikTunai::all();
        if (request()->ajax()) {
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
        $bank = Bank::all()->pluck('name', 'uuid');
        return view('tariktunai.create', compact('bank'));
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
            'keterangan' => 'required',
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
        $tariktunai->keterangan = $request->keterangan;
        $tariktunai->created_by = Auth::user()->uuid;

        $tariktunai->save();

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
        $bank = Bank::all()->pluck('name', 'uuid');
        $tariktunai = TarikTunai::uuid($id);

        return view('tariktunai.edit', compact('bank','tariktunai'));
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
            'keterangan' => 'required',
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
        $tariktunai->no_ref = 'TUNAI' . '-' . $uniqueCode;
        $tariktunai->customer = $request->customer;
        $tariktunai->nominal = $formattednominal;
        $tariktunai->bank_uuid = $request->bank_uuid;
        $tariktunai->no_kartu = $request->no_kartu;
        $tariktunai->biaya_admin = $formattedbiayaadmin;
        $tariktunai->keterangan = $request->keterangan;
        $tariktunai->edited_by = Auth::user()->uuid;

        $tariktunai->save();

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
        $tariktunai->delete();

        toastr()->success('Tarik Tunai Deleted', 'Success');
        return redirect()->route('tariktunai.index');
    }
}
