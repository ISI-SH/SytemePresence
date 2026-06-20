<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DailyToken extends Model
{
    protected $fillable = ['token', 'date', 'valid_from', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'valid_from' => 'datetime',
        'date'       => 'date',
    ];

    public static function current(): ?self
    {
        return static::validQuery()
            ->orderByDesc('valid_from')
            ->first();
    }

    public static function ensureCurrent(): self
    {
        $validFrom = now()->copy()->startOfMinute();
        $expiresAt = $validFrom->copy()->addMinute();

        return static::firstOrCreate(
            ['valid_from' => $validFrom],
            [
                'token'      => Str::random(32),
                'date'       => today(),
                'expires_at' => $expiresAt,
            ]
        );
    }

    public static function isTokenValid(string $scanned): bool
    {
        return static::validQuery()
            ->where('token', $scanned)
            ->exists();
    }

    public static function validQuery(): Builder
    {
        return static::query()
            ->where('valid_from', '<=', now())
            ->where('expires_at', '>', now());
    }

    public function isValid(string $scanned): bool
    {
        return $this->token === $scanned
            && $this->valid_from <= now()
            && $this->expires_at > now();
    }

    public function secondsUntilExpiry(): int
    {
        return max(0, $this->expires_at->getTimestamp() - now()->getTimestamp());
    }
}
