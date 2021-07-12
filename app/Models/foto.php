<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\item;

class foto extends Model
{
    protected $fillable = [
        'img',
        'item_id'
    ];

    public function rules(){
        return [
            'img' => 'required',
            'item_id' => 'required'
        ];
    }

    public function item(){
        return $this->belongsTo(foto::class, 'item_id', 'id');
    }
}
