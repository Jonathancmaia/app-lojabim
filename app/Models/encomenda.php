<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\cliente;
use App\Models\encomenda_has_item;
use App\Models\item;

class encomenda extends Model
{
    protected $fillable = [
        'cliente_id',
        'valor',
        'transaction_code'
    ];

    public function rules(){
        return [
            'cliente_id' => 'required',
            'valor' => 'required',
            'transaction_code' => 'required'
        ];
    }

    public function cliente(){
        return $this->belongsTo(encomenda::class, 'cliente_id', 'id');
    }

    public function encomenda(){
        return $this->hasMany(encomenda_has_item::class, 'encomenda_id', 'id');
    }

    public function items(){
        return $this->belongsToMany(item::class, 'encomenda_has_items');
    }
}
