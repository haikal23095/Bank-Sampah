<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'staff_id', 'date', 'type', 'total_amount', 
        'total_weight', 'status', 'method', 'admin_note'
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'float',
        'total_weight' => 'float',
    ];

    // Nasabah pemilik transaksi
    public function nasabah()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Petugas yang memproses
    public function petugas()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // Detail barang yang disetor (khusus DEPOSIT)
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
