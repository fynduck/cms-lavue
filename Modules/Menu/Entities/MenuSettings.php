<?php

namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;

class MenuSettings extends Model
{
    protected $fillable = [
        'sizes',
        'resize'
    ];

    protected $casts = [
        'sizes' => 'array'
    ];
}
