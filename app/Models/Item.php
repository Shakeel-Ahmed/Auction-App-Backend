<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $primaryKey = 'item';

    protected $fillable = ['publish','status'];

    public function bids()
    {
        return $this->hasMany(Bid::class, 'user');
    }
}

