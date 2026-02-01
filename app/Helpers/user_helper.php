<?php

use App\Libraries\enums\UserRole;

function user_role(): UserRole
{
    $u = user();

    return UserRole::from(intval($u->is_superadmin));
}

function getUserRole(int|string $role): string
{
    return UserRole::from(intval($role))->label();
}

function is_wali_kelas(): bool
{
    return !empty(user()->id_guru);
}

function is_superadmin(): bool
{
    return user_role()->isSuperAdmin();
}

function is_kepsek(): bool
{
    return user_role() === UserRole::Kepsek;
}

function can_edit_attendance(): bool
{
    return in_array(user_role(), [UserRole::SuperAdmin, UserRole::StafPetugas]);
}

function can_generate_qr(): bool
{
    return in_array(user_role(), [UserRole::SuperAdmin, UserRole::StafPetugas]);
}

function can_view_report(): bool
{
    return in_array(user_role(), [UserRole::SuperAdmin, UserRole::StafPetugas, UserRole::Kepsek]);
}

