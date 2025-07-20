<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'user_id',
        'amount',
        'description',
        'bank_account',
        'proof_image',
        'status',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'updated_at',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('created_at', [$fromDate, $toDate]);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }

    // Methods
    public static function generateReferenceCode($userId)
    {
        return 'DEP_' . $userId . '_' . time();
    }

    public function approve($adminId, $note = null)
    {
        $this->update([
            'status' => 'approved',
            'admin_note' => $note,
            'processed_by' => $adminId,
            'processed_at' => now(),
        ]);

        // Update user wallet balance
        $this->user->increment('wallet_balance', $this->amount);
    }

    public function reject($adminId, $note = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_note' => $note,
            'processed_by' => $adminId,
            'processed_at' => now(),
        ]);
    }
}
