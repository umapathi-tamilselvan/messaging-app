<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = [
        'phone',
        'code',
        'expires_at',
        'used',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
            'attempts' => 'integer',
        ];
    }

    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture() && $this->attempts < 5;
    }

    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    public static function generate(string $phone): self
    {
        // Clean up old unused OTPs
        self::where('phone', $phone)
            ->where('used', false)
            ->where('expires_at', '<', now())
            ->delete();

        return self::create([
            'phone' => $phone,
            'code' => str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(5),
            'used' => false,
            'attempts' => 0,
        ]);
    }
}
