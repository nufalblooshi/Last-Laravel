<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public const PAGINATION_COUNT = 5;

    public static $rules = [
        'review' => 'required',
        'rating' => 'required|numeric|min:0|max:5',
        'name' => 'required',
        'email' => 'required|email'
    ];

    protected $guarded = ['id'];

    function product() {
        return $this->belongsTo(Product::class);
    }

    function user() {
        return $this->belongsTo(User::class);
    }

    function getFormattedDateTime() {
        $datetime = strtotime($this->created_at);
        return date('d-M-Y H:i a', $datetime);
    }

}