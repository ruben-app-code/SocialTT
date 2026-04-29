<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';

    protected $fillable = ['nombre', 'icon', 'bg', 'color'];

    protected $attributes = [
        'bg'    => '#6b7280',
        'color' => '#ffffff',
    ];
}
