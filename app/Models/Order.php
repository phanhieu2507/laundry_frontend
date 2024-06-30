<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'user_id',
        'service',
        'detail',
        'status',
        'payment_status',
        'order_date',
        'total_amount',
        'promo_code', 
        'discount_amount'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function reviews()
{
    return $this->hasMany(Review::class);
}


}
