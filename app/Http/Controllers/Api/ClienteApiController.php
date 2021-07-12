<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\cliente;
use App\Models\endereco;

class ClienteApiController extends Controller
{
    public function __Construct (Cliente $cliente, Request $request, Endereco $endereco){
        $this->Cliente = $cliente;
        $this->Request = $request;
        $this->Endereco = $endereco;
    }
    
    public function index()
    {
        $data = $this->Cliente->all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->Cliente->rules());
        $dataForm = $request->all();
        $data = $this->Cliente->create($dataForm);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        if (!$data = $this->Cliente->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            return response()->json($data);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$data = $this->Cliente->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            $dataForm = $request->all();
            $data->update($dataForm);
            return response($data, 201);
        }

    }

    public function destroy($id)
    {
        if (!$data = $this->Cliente->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            $data->delete();
            return response()->json(['success' => 'Registro apagado.']);
        }
    }

    public function encomenda($id){

        $data = $this->Cliente->with('encomenda')->find($id);
        
        if (!$data){
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        } else {
            return response()->json($data);
        }
    }
}
