<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestOrder extends Model
{
    use HasFactory;
    protected $primaryKey = 'request_order_id';

    protected $fillable = [
        'user_id',
        'service',
        'detail',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
