<?php

namespace App\Services;

use RuntimeException;

class EnvWriter
{
    /**
     * Persist the given key/value pairs into the application's .env file.
     *
     * @param  array<string, string|null>  $values
     */
    public function update(array $values): void
    {
        $path = base_path('.env');

        if (! is_file($path) || ! is_writable($path)) {
            throw new RuntimeException('.env file is missing or not writable.');
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException('Unable to read the .env file.');
        }

        foreach ($values as $key => $value) {
            $key = strtoupper(trim((string) $key));

            if (! preg_match('/^[A-Z0-9_]+$/', $key)) {
                continue;
            }

            $value = $value === null ? '' : (string) $value;
            $formatted = '"' . str_replace(['\\', '"'], ['\\\\', '\"'], $value) . '"';
            $replacement = $key . '=' . $formatted;
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
            } else {
                $content = rtrim($content, "\r\n") . PHP_EOL . $replacement . PHP_EOL;
            }
        }

        if (file_put_contents($path, $content) === false) {
            throw new RuntimeException('Unable to write the .env file.');
        }
    }
}
