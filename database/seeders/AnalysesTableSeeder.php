<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnalysesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('analyses')->delete();
        
        \DB::table('analyses')->insert(array (
            0 => 
            array (
                'id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'submission_id' => '019f8494-a835-70b1-98b9-31b898b3b7f6',
                'type' => 'document',
                'category' => NULL,
                'status' => 'completed',
                'config' => NULL,
                'metadata' => NULL,
                'started_at' => '2026-07-21 12:09:25',
                'completed_at' => '2026-07-21 12:25:20',
                'error' => NULL,
                'created_at' => '2026-07-21 12:09:23',
                'updated_at' => '2026-07-21 12:25:20',
            ),
        ));
        
        
    }
}