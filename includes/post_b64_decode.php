<?php
/**
 * Dekodira POST vrijednosti kodirane na klijentu kao "B64FLD:<base64>".
 * Koristi se za tekstualna polja koja mod_security blokira (403) zbog sadržaja.
 */
if (!function_exists('norma_post_b64_decode')) {
    function norma_post_b64_decode(): void
    {
        foreach ($_POST as $key => $value) {
            if (!is_string($value) || strncmp($value, 'B64FLD:', 7) !== 0) {
                continue;
            }
            $decoded = base64_decode(substr($value, 7), true);
            if ($decoded !== false) {
                $_POST[$key] = $decoded;
            }
        }
    }
}

norma_post_b64_decode();
