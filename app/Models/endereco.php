<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\cliente;

class endereco extends Model
{
    protected $fillable = [
        'cliente_id',
        'cep',
        'rua',
        'bairro',
        'cidade',
        'estado',
        'numero',
        'complemento',
        'referencia'
    ];

    public function rules(){
        return [
            'cliente_id' => 'required',
            'cep' => 'required',
            'rua' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            'estado' => 'required',
            'numero' => 'required'
        ];
    }

    public function cliente(){
        return $this->belongsTo(endereco::class, 'cliente_id', 'id');
    }
}
