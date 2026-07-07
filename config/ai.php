<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI provider that will be used to resolve
    | prompts. You can choose any of the configured providers below.
    |
    */
    'default' => env('AI_PROVIDER', 'ollama'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure all the AI providers supported by the application.
    | Standard options include Ollama (local), Google Gemini, OpenAI, etc.
    |
    */
    'providers' => [

        'ollama' => [
            'endpoint' => env('OLLAMA_ENDPOINT', 'http://localhost:11434'),
            'default_model' => env('OLLAMA_DEFAULT_MODEL', 'gemma2'),
            'timeout' => env('OLLAMA_TIMEOUT', 60), // in seconds
        ],

    ],

];
