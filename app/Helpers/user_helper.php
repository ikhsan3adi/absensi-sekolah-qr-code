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
