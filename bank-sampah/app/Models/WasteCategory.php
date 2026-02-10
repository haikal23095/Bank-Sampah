<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    // Satu Kategori punya banyak Jenis Sampah
    public function wasteTypes()
    {
        return $this->hasMany(WasteType::class, 'category_id');
    }
}
