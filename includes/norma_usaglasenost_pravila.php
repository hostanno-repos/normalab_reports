<?php
/**
 * Pravila usaglašenosti po mjernoj veličini — usklađeno s mpdf-includes skriptama.
 * Koristi pregledizvjestajapdf.php, rezultati_mjerenja_logika.php i backfill.
 */

if (!function_exists('norma_mjerna_odstupanje_decimals')) {
    /**
     * Broj decimala pri usporedbi s dozvoljenim odstupanjem (kao $tacnost u mpdf skriptama).
     */
    function norma_mjerna_odstupanje_decimals(int $mjernaVelicinaId): int
    {
        if ($mjernaVelicinaId === 11) {
            return 3;
        }
        if ($mjernaVelicinaId === 15) {
            return 2;
        }
        if (in_array($mjernaVelicinaId, [8, 17, 18, 27, 28], true)) {
            return 1;
        }

        return 0;
    }
}

if (!function_exists('norma_referentne_spec_ids')) {
    /** Referentne vrijednosti s odstupanjem u J (apsolutno), npr. ultrazvuk. */
    function norma_referentne_spec_ids(): array
    {
        return [58, 59, 60, 61, 62, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76];
    }
}

if (!function_exists('norma_mjerna_apsolutno_odstupanje_ids')) {
    /**
     * Mjerne veličine gdje se δ[%] prikazuje, ali se usaglašenost procjenjuje preko |ΔX| i НДГ u jedinicama mjere
     * (script[one-hidden-two-not-measurable-absolute].php, one-shown-two-not-measurable-absolute).
     */
    function norma_mjerna_apsolutno_odstupanje_ids(): array
    {
        return [
            7,   // invazivni pritisak (mmHg)
            8,   // temperatura (°C)
            9,   // saturacija SpO2 (%)
            11,  // npr. sterilizator
            17,  // temp. zraka inkubator (℃)
            18,  // temp. kože (℃)
            19,  // kiseonik (Vol.%)
            28,  // npr. 10.php apsolutno
            151, // NIBP sistolni (mmHg)
            152, // NIBP dijastolni (mmHg)
        ];
    }
}

if (!function_exists('norma_mjerna_shown_relative_ids')) {
    /** script[one-shown-two-not-measurable-relative].php */
    function norma_mjerna_shown_relative_ids(): array
    {
        return [4, 21, 22];
    }
}

if (!function_exists('norma_mjerna_hidden_absolute_ids')) {
    /**
     * Apsolutna skripta "hidden": mješoviti/neizmjereni redovi se ne prikazuju.
     */
    function norma_mjerna_hidden_absolute_ids(): array
    {
        return [10, 11, 17, 18, 19, 20, 28, 151, 152, 155, 156];
    }
}

if (!function_exists('norma_mjerna_shown_absolute_ids')) {
    /**
     * Apsolutna skripta "shown": red bez mjerenja je vidljiv i tretira se kao DA
     * (osim eksplicitnog slučaja '---' koji ostaje NE).
     */
    function norma_mjerna_shown_absolute_ids(): array
    {
        $aps = norma_mjerna_apsolutno_odstupanje_ids();
        $hidden = norma_mjerna_hidden_absolute_ids();
        return array_values(array_diff($aps, $hidden));
    }
}

if (!function_exists('norma_mjerna_hidden_absolute_ids')) {
    /**
     * Apsolutna skripta "hidden": mješoviti/neizmjereni redovi se ne prikazuju.
     * Usklađeno s pregledizvjestajapdf.php granom za standardnu tabelu.
     */
    function norma_mjerna_hidden_absolute_ids(): array
    {
        return [10, 11, 17, 18, 19, 20, 28, 151, 152, 155, 156];
    }
}

if (!function_exists('norma_mjerna_shown_absolute_ids')) {
    /**
     * Apsolutna skripta "shown": red bez mjerenja je vidljiv i tretira se kao DA
     * (osim eksplicitnog slučaja '---' koji ostaje NE).
     */
    function norma_mjerna_shown_absolute_ids(): array
    {
        $aps = norma_mjerna_apsolutno_odstupanje_ids();
        $hidden = norma_mjerna_hidden_absolute_ids();
        return array_values(array_diff($aps, $hidden));
    }
}

if (!function_exists('norma_usaglasenost_usporedi_apsolutno')) {
    /**
     * Da li se za ovaj red uspoređuje apsolutna greška s dozvoljenim odstupanjem (ne relativni %).
     *
     * @param int   $mjernaVelicinaId
     * @param float $referentnaVrijednostXs
     * @param int   $referentnaVrijednostId
     */
    function norma_usaglasenost_usporedi_apsolutno(
        int $mjernaVelicinaId,
        float $referentnaVrijednostXs,
        int $referentnaVrijednostId
    ): bool {
        if (in_array($referentnaVrijednostId, norma_referentne_spec_ids(), true)) {
            return true;
        }

        if (in_array($mjernaVelicinaId, norma_mjerna_apsolutno_odstupanje_ids(), true)) {
            return true;
        }

        // Defibrilator (mj. vel. 10): ispod 100 J — apsolutno (3.php absolute-up-to-100)
        if ($mjernaVelicinaId === 10 && round($referentnaVrijednostXs, 1) < 100) {
            return true;
        }

        // Defibrilator / ultrazvuk: Xs = 2, 10, 30, 70 — apsolutno u J (pregledizvjestajapdf + mpdf)
        $xsInt = (int) round($referentnaVrijednostXs);
        if (in_array($xsInt, [2, 10, 30, 70], true)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('norma_usaglasenost_mjerenje_nije_izvrseno')) {
    function norma_usaglasenost_mjerenje_nije_izvrseno(
        $prvo,
        $drugo,
        $trece
    ): bool {
        return $prvo === '--' && $drugo === '--' && $trece === '--';
    }
}

if (!function_exists('norma_usaglasenost_sva_tri_crtica')) {
    function norma_usaglasenost_sva_tri_crtica($prvo, $drugo, $trece): bool
    {
        return $prvo === '-' && $drugo === '-' && $trece === '-';
    }
}

if (!function_exists('norma_usaglasenost_sva_tri_brojcano')) {
    function norma_usaglasenost_sva_tri_brojcano($prvo, $drugo, $trece): bool
    {
        return $prvo !== '-' && $prvo !== '--'
            && $drugo !== '-' && $drugo !== '--'
            && $trece !== '-' && $trece !== '--'
            && is_numeric((string) $prvo)
            && is_numeric((string) $drugo)
            && is_numeric((string) $trece);
    }
}

if (!function_exists('norma_invazivni_preskoci_red_bez_mjerenja')) {
    /**
     * Invazivni pritisak (MV 7): Xs -10 i -50 se ne prikazuju u PDF-u ako nema mjerenja.
     */
    function norma_invazivni_preskoci_red_bez_mjerenja(
        int $mjernaVelicinaId,
        float $referentnaVrijednostXs,
        $prvo,
        $drugo,
        $trece
    ): bool {
        if ($mjernaVelicinaId !== 7) {
            return false;
        }

        $xs = (int) round($referentnaVrijednostXs);
        if (!in_array($xs, [-10, -50], true)) {
            return false;
        }

        return !norma_usaglasenost_sva_tri_brojcano($prvo, $drugo, $trece);
    }
}
