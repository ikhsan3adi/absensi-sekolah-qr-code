<?php

namespace App\Libraries\enums;

enum UserRole: int
{
  case Scanner = 0;
  case SuperAdmin = 1;
  case Kepsek = 2;
  case StafPetugas = 3;

  public const ALL_ROLES = [
    self::Scanner,
    self::SuperAdmin,
    self::Kepsek,
    self::StafPetugas,
  ];

  public function label(): string
  {
    return match ($this) {
      self::Scanner => 'Scanner',
      self::SuperAdmin => 'Super Admin',
      self::Kepsek => 'Kepsek',
      self::StafPetugas => 'Staf Petugas',
    };
  }

  public function isSuperAdmin(): bool
  {
    return $this === self::SuperAdmin;
  }
}
