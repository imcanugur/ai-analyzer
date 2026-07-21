<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => '019f576f-e40e-7257-b758-4f1a0aaa2092',
                'name' => 'Test User',
                'email' => 'user@test.com',
                'email_verified_at' => '2026-07-12 17:46:18',
                'password' => '$2y$12$ES9yqsVPxrZ/HttGHvzsue47VxUa8nNhutKhFkMyZAYIHaHD4CJS6',
                'remember_token' => 'w9uaGzDDHLEWIiYW9qaQk5H2iZFrbVysEB1tNxAZYhCNbk7wifUe0foGxVp7',
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-14 15:52:25',
                'app_authentication_secret' => NULL,
                'app_authentication_recovery_codes' => NULL,
                'has_email_authentication' => true,
            ),
            1 => 
            array (
                'id' => '019f3cb1-f6f8-7170-a9d4-22bd5d94992f',
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'email_verified_at' => '2026-07-12 20:13:25',
                'password' => '$2y$12$T8tFhGSbFHM8XIjtRCvcHeP.Y7dG0IC4ys2jY8Z1JsvNmrdRA31lu',
                'remember_token' => 'weIpfOtYHdewvLoXpfySTt4FWtUfn3VEMWPpJE08w34vVNJ8DbRLS28lXrzl',
                'created_at' => '2026-07-07 13:08:43',
                'updated_at' => '2026-07-13 15:27:31',
                'app_authentication_secret' => NULL,
                'app_authentication_recovery_codes' => NULL,
                'has_email_authentication' => true,
            ),
        ));
        
        
    }
}