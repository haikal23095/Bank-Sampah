<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'staff_id', 'date', 'amount', 'status', 'method', 'admin_note'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'float',
    ];

    // Nasabah pemilik penarikan
    public function nasabah()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Petugas yang memproses
    public function petugas()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
