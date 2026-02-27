<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'staff_id', 'date', 'admin_note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Derived totals computed from details
    public function getTotalAmountAttribute()
    {
        if ($this->relationLoaded('details')) {
            return (float) $this->details->sum('subtotal');
        }

        return (float) $this->details()->sum('subtotal');
    }

    public function getTotalWeightAttribute()
    {
        if ($this->relationLoaded('details')) {
            return (float) $this->details->sum('weight');
        }

        return (float) $this->details()->sum('weight');
    }

    public function isDeposit()
    {
        return $this->details()->exists();
    }

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
