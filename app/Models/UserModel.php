<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected function initialize(): void
    {
        parent::initialize();

        // Add custom field for teacher relationship
        $this->allowedFields = array_merge($this->allowedFields, [
            'id_guru',
        ]);
    }
}
