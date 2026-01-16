<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

class ActivatePatternApprovalUsers extends Seeder
{
    public function run()
    {
        // Get all users who registered recently but might not be properly activated
        $db = \Config\Database::connect();
        
        // Get users created today (Pattern Approval registrations)
        $users = $db->table('users')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->get()
            ->getResult();

        $userModel = model(UserModel::class);

        foreach ($users as $userData) {
            $user = $userModel->findById($userData->id);
            
            if ($user) {
                // Ensure user is activated
                if (!$user->isActivated()) {
                    echo "Activating user ID: {$user->id} ({$user->email})\n";
                    $user->activate();
                    $userModel->save($user);
                    echo "✓ User activated successfully\n";
                } else {
                    echo "User ID: {$user->id} ({$user->email}) is already activated\n";
                }
            }
        }

        echo "\nActivation process complete!\n";
    }
}
