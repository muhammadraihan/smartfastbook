<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Rekening;

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

class RekeningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rekening = Rekening::all();
        if (request()->ajax()) {
            $data = Rekening::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('bank_uuid', function($row){
                    return $row->Bank->name;
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    return '
                            <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('rekening.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('rekening.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('rekening.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $bank = Bank::all()->pluck('name', 'uuid');
        return view('rekening.create', compact('bank'));
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
            'no_rekening' => 'required',
            'nama' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $rekening = new Rekening();
        $rekening->nama = $request->nama;
        $rekening->bank_uuid = $request->bank_uuid;
        $rekening->no_rekening = $request->no_rekening;
        $rekening->created_by = Auth::user()->uuid;

        $rekening->save();

        toastr()->success('New Rekening Added', 'Success');
        return redirect()->route('rekening.index');
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
        $rekening = Rekening::uuid($id);
        $bank = Bank::all()->pluck('name', 'uuid');
        return view('rekening.edit', compact('rekening', 'bank'));
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
            'no_rekening' => 'required',
            'nama' => 'required',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
            '*.min' => 'Nama tidak boleh kurang dari 2 karakter !',
        ];

        $this->validate($request, $rules, $messages);
        // dd($request->all());

        $rekening = Rekening::uuid($id);
        $rekening->nama = $request->nama;
        $rekening->bank_uuid = $request->bank_uuid;
        $rekening->no_rekening = $request->no_rekening;
        $rekening->created_by = Auth::user()->uuid;

        $rekening->save();

        toastr()->success('Rekening Edited', 'Success');
        return redirect()->route('rekening.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rekening = Rekening::uuid($id);
        $rekening->delete();

        toastr()->success('Rekening Deleted', 'Success');
        return redirect()->route('rekening.index');
    }
}
