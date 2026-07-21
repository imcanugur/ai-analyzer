<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubmissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('submissions')->delete();
        
        \DB::table('submissions')->insert(array (
            0 => 
            array (
                'id' => '019f8494-a835-70b1-98b9-31b898b3b7f6',
                'user_id' => '019f3cb1-f6f8-7170-a9d4-22bd5d94992f',
                'title' => 'Laravel AI Test',
                'description' => NULL,
                'status' => 'pending',
                'metadata' => NULL,
                'submitted_at' => '2026-07-21 12:09:22',
                'created_at' => '2026-07-21 12:09:22',
                'updated_at' => '2026-07-21 12:09:22',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}