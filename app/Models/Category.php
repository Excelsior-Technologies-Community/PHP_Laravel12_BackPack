<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Category extends Model
{
    use HasFactory, CrudTrait;

    protected $fillable = ['name', 'description', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Accessor for active status
    public function getStatusAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}