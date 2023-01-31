<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public const PAGINATION_COUNT = 5;

    public static $rules = [
        "name" => "required|alpha_num",
        "image" => "required|image|max:2048"
    ];

    protected $guarded = ['id'];
    public $timestamps = false;


    public function products() {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}