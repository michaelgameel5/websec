<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'credit'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'credit' => 'decimal:2',
    ];

    // Relationship to Role
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Relationship to Purchases
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    // Relationship to Credit Transactions (as customer)
    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class, 'customer_id');
    }

    // Helper methods for role checking
// app/Models/User.php

    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'Admin';
    }

    public function isEmployee(): bool
    {
        return $this->role && $this->role->name === 'Employee';
    }

    public function isCustomer(): bool
    {
        return $this->role && $this->role->name === 'Customer';
    }
}