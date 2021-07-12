<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\encomenda_has_item;

class EncomendaHasItemApiController extends Controller
{
    public function __Construct (Encomenda_has_item $encomendaHasItem, Request $request){
        $this->Encomenda_has_item = $encomendaHasItem;
        $this->Request = $request;
    }
    
    public function index()
    {
        $data = $this->Encomenda_has_item->all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->Encomenda_has_item->rules());
        $dataForm = $request->all();
        $data = $this->Encomenda_has_item->create($dataForm);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        if (!$data = $this->Encomenda_has_item->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            return response()->json($data);
        }
    }

    public function destroy($id)
    {
        if (!$data = $this->Encomenda_has_item->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            $data->delete();
            return response()->json(['success' => 'Registro apagado.']);
        }
    }
}
