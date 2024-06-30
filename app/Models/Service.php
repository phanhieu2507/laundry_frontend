<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'service_id';

    protected $fillable = ['service_name', 'description', 'duration', 'is_available', 'price_per_unit', 'unit_type', 'image_url'];



}
