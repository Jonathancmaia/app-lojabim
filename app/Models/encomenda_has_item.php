<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\item;

class encomenda_has_item extends Model
{
    protected $fillable = [
        'encomenda_id',
        'item_id',
        'item_qtd',
        'item_tam'
    ];

    public function rules(){
        return [
            'encomenda_id' => 'required',
            'item_id' => 'required',
            'items_qtd' => 'required',
            'item_tam' => 'required'
        ];
    }
}
