<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;

    protected $table = 'color_options';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
