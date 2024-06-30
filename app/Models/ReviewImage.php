<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    use HasFactory;

    protected $fillable = ['image_path', 'review_id'];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }

}
