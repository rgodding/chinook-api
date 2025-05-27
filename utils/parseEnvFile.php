<?php

function parseEnvFile(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];

        foreach ($lines as $line) {
            // Ignorer kommentarer og tomme linjer
            if (strpos(trim($line), '#') === 0) continue;

            // Del på første '='
            $parts = explode('=', $line, 2);
            if (count($parts) == 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                // Fjern eventuelle citationstegn rundt om værdien
                $value = trim($value, "\"'");

                $env[$key] = $value;
            }
        }

        return $env;
    }
