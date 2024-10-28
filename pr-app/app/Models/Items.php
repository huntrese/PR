<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $table = 'items'; 
    protected $primaryKey = 'item_id';

    public $timestamps = true; // Enable or disable timestamps

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'with_tax',
        'href',
    ];

}
