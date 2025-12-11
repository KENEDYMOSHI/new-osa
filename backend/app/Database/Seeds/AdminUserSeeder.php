<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $users = model('CodeIgniter\Shield\Models\UserModel');
        
        // Check if admin exists
        $existing = $users->where('username', 'admin')->first();
        
        if (!$existing) {
            $user = new \CodeIgniter\Shield\Entities\User([
                'username' => 'admin',
                'email'    => 'admin@example.com',
                'password' => 'admin',
            ]);
            $users->save($user);
            
            // Get the ID
            $user = $users->findById($users->getInsertID());
            
            // Activate
            $user->activate();
            
            // Add UUID
            $db = \Config\Database::connect();
            $uuid = strtoupper(md5(uniqid(rand(), true)));
            $db->table('users')->where('id', $user->id)->update(['uuid' => $uuid]);
            
            echo "Admin user created successfully.\n";
        } else {
            echo "Admin user already exists.\n";
        }
    }
}
