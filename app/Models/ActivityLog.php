<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $table    = 'activity_logs';
    protected $fillable = [
        'user_id', 'username', 'role',
        'action', 'description', 'module',
        'ip_address', 'user_agent',
    ];

    /**
     * Catat aktivitas user yang sedang login.
     *
     * @param string      $action       e.g. 'absensi.store'
     * @param string|null $description  Kalimat deskriptif, e.g. 'Membuat absensi Bandongan 18-07-2026'
     * @param string|null $module       e.g. 'kepkam', 'mahadiyah', 'admin'
     */
    public static function log(string $action, ?string $description = null, ?string $module = null): void
    {
        try {
            $user = Auth::user();
            static::create([
                'user_id'    => $user?->id,
                'username'   => $user?->username,
                'role'       => $user?->role,
                'action'     => $action,
                'description'=> $description,
                'module'     => $module ?? static::guessModule($action),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Jangan sampai logging error mengganggu request utama
            \Log::error('[ActivityLog] Gagal mencatat log: ' . $e->getMessage());
        }
    }

    /**
     * Guess module dari action string (prefix sebelum titik).
     */
    private static function guessModule(string $action): string
    {
        return explode('.', $action)[0] ?? 'general';
    }

    /**
     * Relasi ke user (opsional, bisa null jika user sudah dihapus).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Label & warna untuk tampilan ────────────────────────────────

    public function getModuleColorAttribute(): string
    {
        return match($this->module) {
            'kepkam'    => 'bg-green-100 text-green-700',
            'mahadiyah' => 'bg-blue-100 text-blue-700',
            'keamanan'  => 'bg-red-100 text-red-700',
            'kantor'    => 'bg-orange-100 text-orange-700',
            'madin'     => 'bg-teal-100 text-teal-700',
            'admin'     => 'bg-purple-100 text-purple-700',
            'auth'      => 'bg-gray-100 text-gray-700',
            default     => 'bg-gray-100 text-gray-500',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match(true) {
            str_contains($this->action, 'login')   => 'fa-sign-in-alt',
            str_contains($this->action, 'logout')  => 'fa-sign-out-alt',
            str_contains($this->action, 'store')   => 'fa-plus',
            str_contains($this->action, 'update')  => 'fa-pen',
            str_contains($this->action, 'destroy') => 'fa-trash',
            str_contains($this->action, 'download')=> 'fa-download',
            str_contains($this->action, 'reset')   => 'fa-redo',
            str_contains($this->action, 'libur')   => 'fa-moon',
            default                                 => 'fa-circle-dot',
        };
    }
}
