<?php

Route::group([

  'middleware' => 'api'

], function ($router) {

  Route::post('login', 'Auth\AuthenticateController@login');
  Route::post('logout', 'Auth\AuthenticateController@logout');
  Route::post('refresh', 'Auth\AuthenticateController@refresh');
  Route::get('me', 'Auth\AuthenticateController@me');
  Route::post('signup', 'Auth\RegisterController@create');
  Route::post('problem', 'EmailSenderController@sendProblem');

  $this->get('consultaEncomenda/{token}', 'PagseguroController@consultaEncomenda');
  $this->get('item/{type}', 'Api\ItemApiController@index');
  $this->apiResource('foto', 'Api\FotoApiController');
  $this->get('item/{id}/foto', 'Api\ItemApiController@foto');
  $this->post('frete', 'PagseguroController@frete');
  Route::post('pagseguro', 'PagseguroNotificationService@index');

  Route::post('checkout', 'PagseguroController@checkout');
});

$this->group(['namespace' => 'Api', 'middleware' => 'auth:api'], function (){
  $this->get('cliente/{id}/encomenda', 'ClienteApiController@encomenda');
  $this->get('encomenda/{id}/items', 'EncomendaApiController@items');
  $this->get('cliente/{id}/endereco', 'EnderecoApiController@show');
  $this->apiResource('cliente', 'ClienteApiController');
  $this->apiResource('encomenda', 'EncomendaApiController');
  $this->apiResource('encomendaHasItem', 'EncomendaHasItemApiController');
  $this->apiResource('endereco', 'EnderecoApiController');
});

