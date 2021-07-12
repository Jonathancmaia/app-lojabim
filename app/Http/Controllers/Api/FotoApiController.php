<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\foto;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

class FotoApiController extends Controller
{
    public function __Construct (Foto $foto, Request $request){
        $this->Foto = $foto;
        $this->Request = $request;
    }

    public function index()
    {
        $data = $this->Foto->all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->Foto->rules());
        $dataForm = $request->all();

        if($request->hasFile('img') && $request->file('img')->isValid()){

            $extension = $request->img->extension();
            $name = uniqid(date('His'));
            $nameFile = "{$name}.{$extension}";
            
            $upload = Image::make($dataForm['img'])->resize(360, 540)->save(storage_path("app/public/itens-img/$nameFile", 70));

            if(!$upload){
                return response()->json(['error'=>'Falha ao fazer o upload'], 500);
            } else {
                $dataForm['img'] = $nameFile;
            }
        } else {
            return response()->json(['error'=>'Arquivo não é uma imagem.']);
        }

        $data = $this->Foto->create($dataForm);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        $data = $this->Foto->all();
        return response()->json($data);
    }

    public function destroy($id)
    {
        if (!$data = $this->Foto->find($id)){
            return response()->json(['error' => 'Registro não encontrado.', 300]);
        } else {
            if ($data->img){
                $data->delete();
                Storage::disk('public')->delete("/itens-img/$data->img");
            }
            
            return response()->json(['success' => 'Arquivo apagado com sucesso', 200]);
            
        }
    }
}
