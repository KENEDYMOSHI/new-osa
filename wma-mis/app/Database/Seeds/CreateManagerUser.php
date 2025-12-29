<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UsersModel;
use CodeIgniter\Shield\Entities\User;

class CreateManagerUser extends Seeder
{
    public function run()
    {
        $users = new UsersModel();
        
        // Check if user exists
        $existingUser = $users->getUserByEmail('manager@wma.go.tz');
        
        if ($existingUser && isset($existingUser->id)) {
            // Update existing user
            echo "User exists. Updating collection_center...\n";
            $data = ['collection_center' => '001'];
            // Use query builder to bypass any model restrictions/hash issues
            $db = \Config\Database::connect();
            $db->table('users')->where('id', $existingUser->id)->update($data);
            
            // Ensure group
            $existingUser->addGroup('manager');
            echo "User updated and group ensured.\n";
            
        } else {
            // Create New User Entity
            $user = new User([
                'username'          => 'Manager',
                'email'             => 'manager@wma.go.tz',
                'password'          => 'Kene@2118',
                'first_name'        => 'Region',
                'last_name'         => 'Manager',
                'collection_center' => '001',
                'unique_id'         => bin2hex(random_bytes(10)),
                'phone_number'      => '0755123456',
                'avatar'            => 'default.png',
                'active'            => 1,
                'status'            => 'active'
            ]);
            
            // Save User
            if ($users->save($user)) {
                 $id = $users->getInsertID();
                 $createdUser = $users->findById($id);
                 
                 if ($createdUser) {
                     $createdUser->addGroup('manager');
                     echo "User 'Manager' (manager@wma.go.tz) created successfully. collection_center set to '001'. Added to 'manager' group.\n";
                 } else {
                     echo "User created but could not be retrieved for group assignment.\n";
                 }
            } else {
                 echo "Failed to create user. Errors: " . implode(', ', $users->errors()) . "\n";
            }
        }
    }
}
