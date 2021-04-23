<?php

namespace Modules\Search\Entities;

use Illuminate\Database\Eloquent\Model;

class SearchModule extends Model
{
    protected $fillable = [
        'name',
        'models'
    ];

    protected $casts = [
        'models' => 'array'
    ];
}
