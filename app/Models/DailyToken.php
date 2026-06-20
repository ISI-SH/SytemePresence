<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyToken extends Model {
    protected $fillable = ['token', 'date', 'expires_at'];
    protected $casts    = ['expires_at' => 'datetime', 'date' => 'date'];

    public static function today(): ?self {
        return static::whereDate('date', today())
            ->where('expires_at', '>', now())
            ->first();
    }

    public function isValid(string $scanned): bool {
        return $this->token === $scanned && $this->expires_at > now();
    }
}
