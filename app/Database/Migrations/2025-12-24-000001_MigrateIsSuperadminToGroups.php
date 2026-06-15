<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateIsSuperadminToGroups extends Migration
{
    public function up()
    {
        // Only run if is_superadmin column exists
        if (! $this->db->fieldExists('is_superadmin', 'users')) {
            echo "\nColumn 'is_superadmin' already removed. Skipping migration.\n";

            return;
        }

        // Map old is_superadmin values to Shield group names
        $groupMap = [
            0 => 'scanner',
            1 => 'superadmin',
            2 => 'kepsek',
            3 => 'admin',
        ];

        // Get Shield's auth_groups_users table name from config
        $tables       = config('Auth')->tables;
        $groupsTable  = $tables['groups_users'] ?? 'auth_groups_users';

        // Get all users with their is_superadmin and id_guru values
        $users = $this->db->table('users')
            ->select('id, is_superadmin, id_guru')
            ->get()
            ->getResult();

        $inserts = [];
        $now     = date('Y-m-d H:i:s');

        foreach ($users as $user) {
            $isSuperadmin = (int) $user->is_superadmin;
            $groupName    = $groupMap[$isSuperadmin] ?? 'scanner';

            // Primary group based on role
            $inserts[] = [
                'user_id'    => (int) $user->id,
                'group'      => $groupName,
                'created_at' => $now,
            ];

            // Superadmin also gets 'admin' group for convenience
            if ($groupName === 'superadmin') {
                $inserts[] = [
                    'user_id'    => (int) $user->id,
                    'group'      => 'admin',
                    'created_at' => $now,
                ];
            }

            // Users linked to a teacher profile get 'guru' group too
            if (! empty($user->id_guru)) {
                $inserts[] = [
                    'user_id'    => (int) $user->id,
                    'group'      => 'guru',
                    'created_at' => $now,
                ];
            }
        }

        if ($inserts !== []) {
            $this->db->table($groupsTable)->insertBatch($inserts);
            echo 'Migrated ' . count($users) . " user(s) to Shield groups.\n";
        }

        // Drop the is_superadmin column
        $this->forge->dropColumn('users', 'is_superadmin');
        // echo "Dropped 'is_superadmin' column from users table.\n";
    }

    public function down()
    {
        // Re-add is_superadmin column
        $this->forge->addColumn('users', [
            'is_superadmin' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'username',
            ],
        ]);

        // Restore is_superadmin from Shield groups
        $tables       = config('Auth')->tables;
        $groupsTable  = $tables['groups_users'] ?? 'auth_groups_users';

        $groupMap = [
            'superadmin' => 1,
            'admin'      => 3,
            'kepsek'     => 2,
            'scanner'    => 0,
        ];

        $priority = ['superadmin', 'kepsek', 'admin', 'scanner'];

        // Get all users who have role groups
        $groupUsers = $this->db->table($groupsTable)
            ->select('user_id, group')
            ->whereIn('group', $priority)
            ->get()
            ->getResult();

        // Map user_id to their highest-priority group
        $userGroups = [];

        foreach ($groupUsers as $gu) {
            $uid = (int) $gu->user_id;

            if (! isset($userGroups[$uid])) {
                $userGroups[$uid] = $gu->group;
            } else {
                $currentPriority = array_search($userGroups[$uid], $priority, true);
                $newPriority     = array_search($gu->group, $priority, true);

                if ($newPriority < $currentPriority) {
                    $userGroups[$uid] = $gu->group;
                }
            }
        }

        // Update users
        foreach ($userGroups as $userId => $group) {
            $isSuperadmin = $groupMap[$group] ?? 0;

            $this->db->table('users')
                ->where('id', $userId)
                ->update(['is_superadmin' => $isSuperadmin]);
        }

        // echo "Rolled back: restored 'is_superadmin' column and mapped groups back.\n";
    }
}
