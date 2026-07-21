<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModelHasRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('model_has_roles')->delete();
        
        \DB::table('model_has_roles')->insert(array (
            0 => 
            array (
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
                'model_type' => 'App\\Models\\User',
                'model_id' => '019f3cb1-f6f8-7170-a9d4-22bd5d94992f',
            ),
            1 => 
            array (
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
                'model_type' => 'App\\Models\\User',
                'model_id' => '019f3cb1-f6f8-7170-a9d4-22bd5d94992f',
            ),
            2 => 
            array (
                'role_id' => '019f6bd6-f303-709d-9c5a-ac4bd54bd49c',
                'model_type' => 'App\\Models\\User',
                'model_id' => '019f576f-e40e-7257-b758-4f1a0aaa2092',
            ),
        ));
        
        
    }
}