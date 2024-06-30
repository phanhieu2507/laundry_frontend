<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'promo_code_id',
        'is_used'
    ];
    
    public function user()
{
    return $this->belongsTo(User::class);
}

public function promoCodes()
{
    return $this->belongsToMany(PromoCode::class, 'user_promo_codes')
                ->withPivot('times_used', 'limit', 'created_at');
}


}
