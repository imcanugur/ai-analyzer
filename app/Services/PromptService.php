<?php

namespace App\Services;

class PromptService
{
    /**
     * Get raw prompt content without any replacements.
     * Useful for loading system prompts.
     */
    public function get(string $name): string
    {
        $path = resource_path("prompts/{$name}.md");

        if (! file_exists($path)) {
            throw new \InvalidArgumentException("Prompt template not found: {$name}");
        }

        return file_get_contents($path);
    }

    /**
     * Render a prompt template with placeholder replacements.
     */
    public function render(string $name, array $replacements = []): string
    {
        $prompt = $this->get($name);

        foreach ($replacements as $key => $value) {
            $prompt = str_replace('{{ '.$key.' }}', $value, $prompt);
            $prompt = str_replace('{{'.$key.'}}', $value, $prompt);
        }

        return $prompt;
    }

    /**
     * Load a prompt file and replace placeholders.
     *
     * @deprecated Use render() instead. Kept for backward compatibility.
     */
    public function load(string $name, array $replacements = []): string
    {
        return $this->render($name, $replacements);
    }
}
