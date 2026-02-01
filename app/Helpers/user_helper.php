<?php

const ALL_ROLES = [
    'Scanner',
    'Super Admin',
    'Kepsek',
    'Staf Petugas'
];

function getUserRole(int|string $role): string
{
    $roleIdx = intval($role);
    if ($roleIdx < 0 || $roleIdx >= count(ALL_ROLES)) {
        return 'Unknown Role';
    }

    return ALL_ROLES[$roleIdx];
}

function is_wali_kelas(): bool
{
    return !empty(user()->id_guru);
}

function is_admin_staff(): bool
{
    return empty(user()->id_guru);
}

function is_superadmin(): bool
{
    return user()->is_superadmin == 1;
}

function can_view_report(): bool
{
    return user()->is_superadmin != 0;
}

// TODO: Refactor besar besaran di logika dan representasi role user di next pull request