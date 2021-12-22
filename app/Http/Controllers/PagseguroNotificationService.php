<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\encomenda;

class PagseguroNotificationService extends Controller
{

    public function __construct(Encomenda $encomenda){

        $this->Encomenda = $encomenda;

    } 

    public function index(Request $request){

        $valor = 0;
        $id = 1;
        $transaction_code = $request->notificationCode;

        $dados = [
            'valor' => $valor,
            'cliente_id' => $id,
            'transaction_code' => $transaction_code
        ];

        return $this->Encomenda->create($dados)->id;
    }
}
