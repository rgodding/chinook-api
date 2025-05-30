<?php
class InputSanitizer {
    public static function clean(string $input): string {
        $input = trim($input);
        $input = strip_tags($input); // More effective than htmlspecialchars here
        return $input;
    }
}