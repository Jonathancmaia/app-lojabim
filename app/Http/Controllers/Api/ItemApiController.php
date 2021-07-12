<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\item;
use Illuminate\Support\Facades\Storage;

class ItemApiController extends Controller
{
    public function __Construct (Item $item, Request $request){
        $this->Item = $item;
        $this->Request = $request;
    }

    public function index($type)
    {
        $type = intval($type);

        if ($type == 0) {

            $data = $this->Item->all();
            return response()->json($data);

        } else if ($type >= 1 && $type <= 5 ){

            $data = $this->Item->all()->where('tipo', $type);

            if ($data == '' || $data == 'undefined' || $data == '[]'){
                return ('Nenhum item cadastrado nessa categoria.');
            } else {
                return response()->json($data->values());
            }

        } else {
            return ('Classe de produtos não cadastrada.');
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->Item->rules());
        $dataForm = $request->all();
        $data = $this->Item->create($dataForm);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        if (!$data = $this->Item->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            return response()->json($data);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$data = $this->Item->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            $dataForm = $request->all();
            $data->update($dataForm);
            return response($data, 201);
        }
    }

    public function destroy($id)
    {
        if (!$data = $this->Item->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 404]);
        } else {
            $data->delete();
            return response()->json(['success' => 'Registro apagado.']);
        }
    }

    public function foto($id_arr){

        $data_response = '';

        foreach (json_decode($id_arr) as $id){

            $data = $this->Item->with('foto')->find($id);
        
            if (!$data){
                return response()->json(['error' => 'Registro não encontrado.'], 404);
            } else {

                foreach ($data->foto as $item => $foto){
                    $url = Storage::url("itens-img/".$foto->img);

                    if ($data_response === ''){
                        $dataIsEmpty = true;
                    } else {
                        $dataIsEmpty = false;
                    }

                    $data_response = $data_response.($dataIsEmpty ? '' : ', ').'{"id": '.$id.', "img": '.'"'.$url.'"'.'}';
                }
                
            }

        }

        return "[".$data_response."]";
        
    }
}
