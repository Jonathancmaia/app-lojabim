<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\encomenda;
use App\Models\encomenda_has_item;
use GuzzleHttp\Client as GuzzleClient;
use App\User;


class PagseguroNotificationService extends Controller
{

    public function __construct(Encomenda $encomenda, User $user, Encomenda_has_item $encomenda_has_item){

        $this->Encomenda = $encomenda;
        $this->User = $user;
        $this->Encomenda_has_item = $encomenda_has_item;

    } 

    public function index(Request $request){

        $client = new GuzzleClient(['http_errors' => false]);
        $host = $request->header('Host');

        //Testa se o Hots é o pagseguro
        if (/*$host = 'pagseguro.uol.com.br'*/ true){
            
            //Pega o código de notificação enviado pelo serviço de notificação do pagseguro
            $notificationCode = $request->notificationCode;

            //Consulta dos dados da encomenda relacionado a notificação
            $notificationRequest = $client->get('https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?email=nozestrump@hotmail.com&token=5F8DE25C8AFC4260B29EB8AADA30A3A2');

            $xml = simplexml_load_string($notificationRequest->getBody()->getContents());

            $existEncomenda = Encomenda::where('transaction_code','=',(string)$xml->code)->get();

            //Caso a encomenda ainda não exista no banco de dados, é registrada no sistema aasim como os itens do pedido
            if($existEncomenda == '[]'){

                $cpf = $xml->sender->documents->document->value;
                $userId = User::where('cpf','=',$cpf)->get()[0]->id;

                $dados = [
                    'valor' => (double)$xml->grossAmount,
                    'cliente_id' => $userId,
                    'transaction_code' => (string)$xml->code
                ];

                //Linha que cria encomenda no banco de dadosss
                $encomenda_id = $this->Encomenda->create($dados)->id;

                $produtos = (array)$xml->items;
                $qtdProdutos = count($produtos['item']);

                //For que adiciona itens no encomenda_has_item
                for($i = 0; $i <= $qtdProdutos-1; $i++){
                    
                    $this->Encomenda_has_item->create([
                        'encomenda_id' => $encomenda_id,
                        'item_id' => (int)$xml->items->item[$i]->id,
                        'item_qtd' => (int)$xml->items->item[$i]->quantity,
                        'item_tam' => str_replace(':','',
                            str_replace(' ','',
                                substr((string)$xml->items->item[$i]->description, -3)
                            )
                        )
                    ]);
                
                }
            } else {

                if (Encomenda::where('transaction_code','=',(string)$xml->code)->update(['status'=>(string)$xml->status])){
                    return 'success';
                } else {
                    return 'failed';
                }

            }

            return 'success';
        } else {
            return 'failed';
        }
    }
}
