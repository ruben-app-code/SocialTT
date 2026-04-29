<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nombre', 'icon', 'bg', 'color'];

    protected $attributes = [
        'bg'    => '#1E85FF',
        'color' => '#ffffff',
    ];
}
