<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\endereco;
use App\Models\encomenda;

class cliente extends Model
{
    protected $fillable = [
        'nome',
        'sexo',
        'cpf',
        'data_nasc'
    ];

    public function rules(){
        return [
            'nome' => 'required',
            'sexo' => 'required',
            'cpf' => 'required|unique:clientes',
            'data_nasc' => 'required'
        ];
    }

    public function endereco(){
        return $this->hasOne(endereco::class, 'cliente_id', 'id');
    }

    public function encomenda(){
        return $this->hasMany(encomenda::class, 'cliente_id', 'id');
    }
}
