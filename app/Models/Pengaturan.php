<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = ['hero_media'];

    private const VIDEO_EXT = ['mp4', 'webm', 'ogv', 'mov'];

    /** Baris pengaturan tunggal; dibuat otomatis kalau belum ada. */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    /** URL media hero, atau null kalau belum diunggah. */
    public function heroUrl(): ?string
    {
        return $this->hero_media ? asset('storage/'.$this->hero_media) : null;
    }

    public function heroIsVideo(): bool
    {
        return in_array(strtolower(pathinfo((string) $this->hero_media, PATHINFO_EXTENSION)), self::VIDEO_EXT, true);
    }
}
