<?php
/**
 * Puna putanja do php(.exe) za exec() iz Apachea — PATH često ne sadrži PHP.
 *
 * Prioritet: NORMA_PHP_EXE (okruženje) → PHP_BINDIR → roditelj od extension_dir
 * (WAMP/XAMPP) → PHP_BINARY ako je php.exe → dirname(PHP_BINARY)/php.exe
 *
 * @return string Prazno na Windowsu ako nije pronađeno; na Linuxu 'php' kao zadnji izbor.
 */
if (!function_exists('norma_resolve_php_executable')) {
    function norma_resolve_php_executable()
    {
        $override = getenv('NORMA_PHP_EXE');
        if (is_string($override) && $override !== '' && is_file($override)) {
            return $override;
        }

        $isWin = (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Windows')
            || (!defined('PHP_OS_FAMILY') && strncasecmp(PHP_OS, 'WIN', 3) === 0);

        if ($isWin) {
            $candidates = array();
            $push = function ($p) use (&$candidates) {
                if (!is_string($p) || $p === '') {
                    return;
                }
                $p = str_replace('/', DIRECTORY_SEPARATOR, $p);
                $candidates[] = $p;
            };

            $push(PHP_BINDIR . DIRECTORY_SEPARATOR . 'php.exe');

            $ext = ini_get('extension_dir');
            if ($ext !== false && $ext !== '') {
                $extDir = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $ext), DIRECTORY_SEPARATOR);
                $push(dirname($extDir) . DIRECTORY_SEPARATOR . 'php.exe');
            }

            $phprc = getenv('PHPRC');
            if (is_string($phprc) && $phprc !== '') {
                $dir = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $phprc), DIRECTORY_SEPARATOR);
                $push($dir . DIRECTORY_SEPARATOR . 'php.exe');
            }

            if (defined('PHP_BINARY') && PHP_BINARY !== '') {
                $pb = str_replace('/', DIRECTORY_SEPARATOR, PHP_BINARY);
                if (stripos($pb, 'php.exe') !== false) {
                    $push($pb);
                }
                $push(dirname($pb) . DIRECTORY_SEPARATOR . 'php.exe');
            }

            $seen = array();
            foreach ($candidates as $c) {
                if ($c === '' || isset($seen[$c])) {
                    continue;
                }
                $seen[$c] = true;
                if (is_file($c)) {
                    return $c;
                }
            }

            return '';
        }

        if (defined('PHP_BINARY') && PHP_BINARY !== '') {
            $base = basename(PHP_BINARY);
            if (stripos($base, 'php') !== false && @is_executable(PHP_BINARY)) {
                return PHP_BINARY;
            }
        }

        return 'php';
    }
}
