<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteType extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name', 'price_per_kg', 'unit'];

    // Milik sebuah Kategori
    public function category()
    {
        return $this->belongsTo(WasteCategory::class, 'category_id');
    }
}
