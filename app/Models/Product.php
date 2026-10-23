<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, CrudTrait;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'sku',
        'barcode',
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

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->name ?? 'PRD'), 0, 4));
                if (strlen($prefix) < 3) {
                    $prefix = 'ITEM';
                }
                $product->sku = $prefix . '-' . strtoupper(bin2hex(random_bytes(3)));
            }

            if (empty($product->barcode)) {
                $product->barcode = '890' . mt_rand(100000000, 999999999);
            }
        });

        static::created(function ($product) {
            AuditLog::record('CREATED', $product, null, $product->getAttributes(), 'Created product "' . $product->name . '"');
        });

        static::updating(function ($product) {
            $dirty = $product->getDirty();
            if (!empty($dirty)) {
                $old = [];
                foreach ($dirty as $key => $newVal) {
                    $old[$key] = $product->getOriginal($key);
                }
                AuditLog::record('UPDATED', $product, $old, $dirty, 'Updated product "' . $product->name . '"');
            }
        });

        static::deleted(function ($product) {
            AuditLog::record('DELETED', $product, $product->getAttributes(), null, 'Deleted product "' . $product->name . '"');
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class)->latest();
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
