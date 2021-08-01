<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\encomenda;

class EncomendaApiController extends Controller
{
    public function __Construct (Encomenda $encomenda, Request $request){
        $this->Encomenda = $encomenda;
        $this->Request = $request;
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    
    public function index()
    {
        $data = $this->Encomenda->all()->
            where('cliente_id', '=', auth()->user()->id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->Encomenda->rules());
        $dataForm = $request->all();
        $data = $this->Encomenda->create($dataForm);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        if (!$data = $this->Encomenda->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            return response()->json($data);
        }
    }

    public function destroy($id)
    {
        if (!$data = $this->Encomenda->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            $data->delete();
            return response()->json(['success' => 'Registro apagado.']);
        }
    }

    public function items($id){

        $data = $this->Encomenda->with('items')->find($id);
        
        if (!$data){
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        } else {
            return response()->json($data);
        }
    }
}
