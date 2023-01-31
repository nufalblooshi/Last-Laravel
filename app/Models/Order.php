<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const PAGINATION_COUNT = 5;

    public static $rules = [
        "firstname" => "required",
        "lastname" => "required",
        "email" => "required|email",
        "mobile_no" => "required|numeric|max_digits:20",
        "address1" => "required",
        "country" => "required",
        "city" => "required",
        "state" => "required",
        "zip_code" => "required"
    ];

    protected $guarded = ['id'];


    public function users()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }
}