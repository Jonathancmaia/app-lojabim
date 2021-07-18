<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\item;
use App\Models\encomenda;
use App\Models\cliente;
use App\Models\encomenda_has_item;
use FlyingLuscas\Correios\Client;
use FlyingLuscas\Correios\Service;



\PagSeguro\Library::initialize();
\PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
\PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");
try {
    \PagSeguro\Library::initialize();
} catch (Exception $e) {
    die($e);
}

\PagSeguro\Configuration\Configure::setEnvironment('sandbox'); //production or sandbox

\PagSeguro\Configuration\Configure::setAccountCredentials(
    /**
     * @see https://devpagseguro.readme.io/v1/docs/autenticacao#section-obtendo-suas-credenciais-de-conta
     *
     * @var string $accountEmail
     */
    env('PAGSEGURO_EMAIL'),
    /**
     * @see https://devpagseguro.readme.io/v1/docs/autenticacao#section-obtendo-suas-credenciais-de-conta
     *
     * @var string $accountToken
     */
    env('PAGSEGURO_TOKEN')
);

/**
 *
 * @see https://devpagseguro.readme.io/docs/endpoints-da-api#section-formato-de-dados-para-envio-e-resposta
 *
 * @var string $charset
 * @options=['UTF-8', 'ISO-8859-1']
 */
\PagSeguro\Configuration\Configure::setCharset('UTF-8');

/**
 * Path do arquivo de log, tenha certeza de que o php terá permissão para escrever no arquivo
 *
 * @var string $logPath
 */
\PagSeguro\Configuration\Configure::setLog(true, 'storage/logs');

class PagseguroController extends Controller
{

    public function __Construct (Item $item, Encomenda $encomenda, Cliente $cliente, Encomenda_has_item $encomenda_has_item){

        //Método construtor chama as classes que serão usadas no controller
        $this->Item = $item;
        $this->Encomenda = $encomenda;
        $this->Cliente = $cliente;
        $this->Encomenda_has_item = $encomenda_has_item;

    }

    public function frete(Request $request){

        $correios = new Client;

        return $correios->freight()
            ->origin('25071120')
            ->destination($request->cep)
            ->services(Service::SEDEX, Service::PAC)
            ->item(40, 15, 40, 2.5, 1) // largura, altura, comprimento, peso e quantidade
            ->calculate();
    }


    public function checkout(Request $request){

        $this->middleware('auth:api', ['except' => ['login']]);

        try {
            $response = \PagSeguro\Services\Session::create(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
        } catch (Exception $e) {
            die($e->getMessage());
        }

        $valor = '0';
        $payment = new \PagSeguro\Domains\Requests\Payment();
        $pedido = json_decode($request->items);
        $endereco = json_decode($request->endereco);
        $item = new \PagSeguro\Domains\Item();
        $payment->setItems($pedido);

        foreach ($pedido as $id => $produto) {

            //Verifica se o produto existe
            if (!$data = $this->Item->find($produto->itemId)){

                //Exibe erro caso o produto não exista
                return response()->json(['error' => 'Registro não encontrado.', 404]);
                die();

            } else {

                //Recupera o ID e PRECO para enviar ao PAGSEGURO
                $desc = $data->desc;
                $preco = $data->preco;

                //Incrementa o preço de cada produto no valor total
                $valor = $valor + ($data->preco * $produto->itemQuantity);
            }

            $payment->addItems()->withParameters(
                $produto->itemId,
                $desc." x Tamanho: ".$produto->itemTam,
                $produto->itemQuantity,
                $preco
            );
        }

        $payment->setCurrency("BRL");
        $payment->setReference("LIBPHP000001");

        //Seta o endereço para a API PAGSEGURO
        $payment->setShipping()->setAddress()->withParameters(
            $endereco->rua,
            $endereco->numero,
            $endereco->bairro,
            $endereco->cep,
            $endereco->cidade,
            $endereco->estado,
            'BRA',
            $endereco->complemento
        );
        
        //Seta detalhes do cliente para a API PAGSEGURO
        $payment->setSender()->setName(auth()->user()->name);
        $payment->setSender()->setEmail(auth()->user()->email);
        $payment->setSender()->setPhone()->withParameters(
            substr(auth()->user()->tel, 0, 2),
            substr(auth()->user()->tel, 2, 11)
        );
        $payment->setSender()->setDocument()->withParameters(
            'CPF',
            auth()->user()->cpf
        );


        $tipoEnvio = '';

        if ($endereco->tipo === 'sedex'){
            $tipoEnvio = \PagSeguro\Enum\Shipping\Type::SEDEX;
        } else {
            $tipoEnvio = \PagSeguro\Enum\Shipping\Type::PAC;
        }

        $payment->setShipping()->setType()->withParameters(
            $tipoEnvio
        );

        $correios = new Client;

        $tipoEnvio2 = '';
        if ($endereco->tipo === 'sedex'){
            $tipoEnvio2 = 0;
        } else if ($endereco->tipo === 'pac') {
            $tipoEnvio2 = 1;
        }

        $precoEnvio = $correios->freight()
        ->origin('25071120')
        ->destination($endereco->cep)
        ->services(Service::SEDEX, Service::PAC)
        ->item(40, 15, 40, 2.5, 1) // largura, altura, comprimento, peso e quantidade
        ->calculate()[$tipoEnvio2];

        if (!$precoEnvio['price']){
            return ('Erro ao cobrar o frete');
            die();
        } else {
            //Adição do valor do envio na compra
            $payment->addItems()->withParameters(
                'Envio',
                "Método de envio: ".$endereco->tipo,
                1,
                $precoEnvio['price']
            );

            $valor = $valor + $precoEnvio['price'];
        }

        try {

            $result = $payment->register(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
            

            if(auth()->check()){

                $dados = [
                    'valor' => $valor,
                    'cliente_id' => auth()->user()->id
                ];

                $encomenda_id = $this->Encomenda->create($dados)->id;

                foreach ($pedido as $id => $produto) {

                    $this->Encomenda_has_item->create([
                        'encomenda_id' => $encomenda_id,
                        'item_id' => $produto->itemId,
                        'item_qtd' => $produto->itemQuantity,
                        'item_tam' => $produto->itemTam
                    ]);

                }

                $this->Endereco->create([
                    'cliente_id' => auth()->user()->id,
                    'cep' => $endereco->cep,
                    'rua' => $endereco->rua,
                    'bairro' => $endereco->bairro,
                    'cidade' => $endereco->cidade,
                    'estado' => $endereco->estado,
                    'numero' => $endereco->numero,
                    'complemento' => $endereco->complemento,
                    'referencia' => $endereco->referencia
                ]);

                return response()->json($result, 201);

            } else {

                return ('Cliente não está logado.');
                die();

            }

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}