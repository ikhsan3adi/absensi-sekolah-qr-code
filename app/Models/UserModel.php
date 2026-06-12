<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected function initialize(): void
    {
        parent::initialize();

        // Add custom fields for compatibility
        $this->allowedFields = array_merge($this->allowedFields, [
            'is_superadmin',
            'id_guru',
        ]);
    }
}
