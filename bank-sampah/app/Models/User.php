<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role',
        'address', 'bank_name', 'account_number', 'join_date',
    ];

    protected $hidden = ['password', 'remember_token'];

    // Relasi ke Dompet (Satu User punya Satu Dompet)
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // Transaksi sebagai Nasabah
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    // Transaksi yang dilayani (jika role PETUGAS/ADMIN)
    public function servicedTransactions()
    {
        return $this->hasMany(Transaction::class, 'staff_id');
    }
}
