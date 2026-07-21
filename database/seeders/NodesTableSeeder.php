<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('nodes')->delete();
        
        \DB::table('nodes')->insert(array (
            0 => 
            array (
                'id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
            'name' => 'Node-1 (Local Ollama)',
                'driver' => 'ollama',
                'endpoint' => 'http://127.0.0.1:11434',
                'api_key' => NULL,
                'status' => 'online',
                'capabilities' => '["qwen3.6:27b","kimi-k2.7-code:cloud","qwen2.5:7b","qwen2.5:3b","qwen3:1.7b"]',
                'weight' => 1,
                'priority' => 1,
                'active_connections' => 1,
                'last_health_check_at' => '2026-07-19 18:36:02',
                'last_error' => NULL,
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-21 12:25:19',
            ),
        ));
        
        
    }
}