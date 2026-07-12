<?php

namespace Database\Seeders;

use App\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    protected SettingRepositoryInterface $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed default stage models configuration in database settings table
        if (!$this->settingRepository->get('stage_models')) {
            $this->settingRepository->set('stage_models', [
                'summary' => 'qwen3:4b',
                'grammar' => 'gemma3:4b',
                'references' => 'qwen3:4b',
                'similarity' => 'qwen3:8b',
                'reviewer' => 'mistral:7b',
                'plagiarism' => 'qwen3:8b',
                'readability' => 'gemma3:4b',
            ]);
        }
    }
}
