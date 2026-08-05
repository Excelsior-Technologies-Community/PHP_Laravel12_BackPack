<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Product extends Model
{
    use HasFactory, CrudTrait;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'quantity',
        'restock_level',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    
    public function getAvailabilityAttribute()
    {
        return $this->is_available ? 'Available' : 'Out of Stock';
    }

    public function getStockStatusAttribute()
    {
        if ($this->quantity == 0) {
            return 'Out of Stock';
        }

        if ($this->quantity <= $this->restock_level) {
            return 'Low Stock';
        }

        return 'In Stock';
    }
}