<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'province_id', 'name', 'type'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
