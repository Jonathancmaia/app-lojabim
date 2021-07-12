<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\foto;
use App\Models\encomenda_has_item;

class item extends Model
{
    protected $fillable = [
        'nome',
        'desc',
        'preco'
    ];

    public function rules(){
        return [
            'nome' => 'required',
            'desc' => 'required',
            'preco' => 'required',
            'tipo' => 'requred'
        ];
    }

    public function foto(){
        return $this->hasMany(foto::class, 'item_id', 'id');
    }

    public function encomenda(){
        return $this->hasMany(encomenda_has_item::class, 'item_id', 'id');
    }

    public function item(){
        return $this->belongsToMany(item::class, 'encomenda_has_items');
    }
}
