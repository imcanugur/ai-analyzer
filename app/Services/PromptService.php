<?php

declare(strict_types=1);

namespace App\Services;

class PromptService
{
    /**
     * Render a raw template string by replacing placeholders like {{ text }}.
     */
    public function renderString(string $template, array $replacements = []): string
    {
        foreach ($replacements as $key => $value) {
            if (is_scalar($value)) {
                $template = str_replace('{{ '.$key.' }}', (string) $value, $template);
                $template = str_replace('{{'.$key.'}}', (string) $value, $template);
            }
        }

        return $template;
    }

    /**
     * Get raw prompt content from disk file (legacy fallback).
     */
    public function get(string $name): string
    {
        $path = resource_path("prompts/{$name}.md");

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return "# Academic Analysis: {$name}\n\n{{ text }}\n\nPerform analysis for {$name}.";
    }

    /**
     * Render a prompt template with placeholder replacements.
     */
    public function render(string $name, array $replacements = []): string
    {
        $prompt = $this->get($name);

        return $this->renderString($prompt, $replacements);
    }
}
