<?php

use App\Libraries\enums\UserRole;

if (! function_exists('user')) {
    function user()
    {
        return auth()->user();
    }
}

/**
 * Get the highest-priority role group for the current user.
 *
 * Priority: superadmin > admin > kepsek > scanner > guru
 * Returns the group name string, or null if no group found.
 */
function user_role(): ?string
{
    $u = user();
    if ($u === null) {
        return null;
    }

    $groups = $u->getGroups();
    if (empty($groups)) {
        return null;
    }

    $priority = ['superadmin', 'admin', 'kepsek', 'scanner', 'guru'];

    foreach ($priority as $group) {
        if (in_array($group, $groups, true)) {
            return $group;
        }
    }

    return $groups[0];
}

/**
 * Get the display label for a role (from group name or UserRole enum).
 */
function getUserRole(string|int $role): string
{
    // If it's an old integer value (from before migration), convert it
    if (is_numeric($role)) {
        $oldMap = [
            0 => 'scanner',
            1 => 'superadmin',
            2 => 'kepsek',
            3 => 'admin',
        ];
        $role = $oldMap[(int) $role] ?? 'scanner';
    }

    return UserRole::fromGroup($role)->label();
}

/**
 * Apakah user memiliki profil guru (terhubung ke tb_guru)?
 */
function is_guru(): bool
{
    $user = user();
    return $user !== null && ! empty($user->id_guru);
}

/**
 * Apakah user adalah wali kelas yang terdaftar di tb_kelas?
 */
function is_wali_kelas(): bool
{
    $user = user();
    if ($user === null || empty($user->id_guru)) {
        return false;
    }

    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $db = \Config\Database::connect();
    $cached = $db->table('tb_kelas')
        ->where('id_wali_kelas', $user->id_guru)
        ->countAllResults() > 0;

    return $cached;
}

function is_superadmin(): bool
{
    $user = user();
    return $user !== null && $user->inGroup('superadmin');
}

function is_kepsek(): bool
{
    $user = user();
    return $user !== null && $user->inGroup('kepsek');
}

function can_edit_attendance(): bool
{
    $user = user();
    return $user !== null && $user->can('attendance.edit');
}

function can_generate_qr(): bool
{
    $user = user();
    return $user !== null && $user->can('qr.generate');
}

function can_view_report(): bool
{
    $user = user();
    return $user !== null && $user->can('attendance.view');
}
