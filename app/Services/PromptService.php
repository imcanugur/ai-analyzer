<?php

namespace App\Services;

class PromptService
{
    /**
     * Load a prompt file and replace placeholders.
     *
     * @param string $name
     * @param array $replacements
     * @return string
     */
    public function load(string $name, array $replacements = []): string
    {
        $path = resource_path("prompts/{$name}.md");

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("Prompt template not found: {$name}");
        }

        $prompt = file_get_contents($path);

        foreach ($replacements as $key => $value) {
            $prompt = str_replace('{{ ' . $key . ' }}', $value, $prompt);
            $prompt = str_replace('{{' . $key . '}}', $value, $prompt);
        }

        return $prompt;
    }
}
