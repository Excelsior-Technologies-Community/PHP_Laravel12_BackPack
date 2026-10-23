<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, CrudTrait;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'payment_terms',
        'lead_time_days',
        'reliability_score',
        'notes',
    ];

    protected $casts = [
        'lead_time_days' => 'integer',
        'reliability_score' => 'decimal:1',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getActiveProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
