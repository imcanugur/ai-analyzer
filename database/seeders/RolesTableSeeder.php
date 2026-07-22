<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
                'name' => 'super_admin',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            1 => 
            array (
                'id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
                'name' => 'user',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:14:35',
                'updated_at' => '2026-07-16 16:14:35',
            ),
            2 => 
            array (
                'id' => '019f6bd6-f303-709d-9c5a-ac4bd54bd49c',
                'name' => 'none',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:51:16',
                'updated_at' => '2026-07-16 16:51:16',
            ),
        ));
        
        
    }
}