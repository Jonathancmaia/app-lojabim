<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\endereco;

class EnderecoApiController extends Controller
{
    public function __Construct (Endereco $endereco, Request $request){
        $this->Endereco = $endereco;
        $this->Request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->Endereco->all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->Endereco->rules());
        $dataForm = $request->all();
        $data = $this->Endereco->create($dataForm);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        if (!$data = $this->Endereco->where('cliente_id', '=', $id)->get()){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            return response()->json($data);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$data = $this->Endereco->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            $dataForm = $request->all();
            $data->update($dataForm);
            return response($data, 201);
        }
    }

    public function destroy($id)
    {
        if (!$data = $this->Endereco->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            $data->delete();
            return response()->json(['success' => 'Registro apagado.']);
        }
    }
}
