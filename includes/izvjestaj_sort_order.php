<?php
/**
 * ORDER BY za pregled izvještaja: godina iz sufiksa /26, zatim redni broj (1224), oba DESC.
 * Koristi izvjestaji_broj (npr. 1224/26) ili zadnji segment radninalozi_broj (PRZ-11/01-1224/26).
 */
function norma_izvjestaji_order_by_sql(): string
{
    $broj = "COALESCE(NULLIF(izvjestaji.izvjestaji_broj, ''), SUBSTRING_INDEX(radninalozi.radninalozi_broj, '-', -1))";

    return 'CAST(SUBSTRING_INDEX(' . $broj . ", '/', -1) AS UNSIGNED) DESC, "
        . 'CAST(SUBSTRING_INDEX(' . $broj . ", '/', 1) AS UNSIGNED) DESC, "
        . 'izvjestaji.izvjestaji_id DESC';
}
