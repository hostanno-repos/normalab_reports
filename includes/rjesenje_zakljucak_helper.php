<?php
/**
 * Rješenje o ovlašćivanju — tekst zaključka na Zavod PDF-u.
 */

if (!function_exists('norma_rjesenje_vrsta_je_vaga')) {
    /** Vrste uređaja koje koriste "Tekst zaključka - vage". */
    function norma_rjesenje_vrsta_je_vaga(int $vrstaUredjajaId): bool
    {
        // 52 = Neautomatska vaga (RU-12); proširi po potrebi.
        return in_array($vrstaUredjajaId, [52], true);
    }
}

if (!function_exists('norma_rjesenje_format_datum')) {
    function norma_rjesenje_format_datum(?string $datumYmd): string
    {
        if ($datumYmd === null || $datumYmd === '') {
            return '30.12.2025.';
        }
        $ts = strtotime($datumYmd);
        if ($ts === false) {
            return '30.12.2025.';
        }

        return date('d.m.Y.', $ts);
    }
}

if (!function_exists('norma_rjesenje_default_tekst_zakljucka')) {
    /**
     * Šablon teksta zaključka. Placeholdere PDF zamjenjuje podacima iz izvještaja/rješenja.
     */
    function norma_rjesenje_default_tekst_zakljucka(string $brojRjesenja, string $datumFormatted): string
    {
        $broj = trim($brojRjesenja) !== '' ? trim($brojRjesenja) : '18/1.10/393.10-03-09-25/25';
        $datum = trim($datumFormatted) !== '' ? trim($datumFormatted) : '30.12.2025.';

        return 'Резултати инспекције се односе искључиво на дати предмет у тренутку инспекције. '
            . 'На основу Рјешења о измјени и допуни рјешења о овлашћивању тијела за верификацију мјерила број '
            . $broj . ' од ' . $datum . ' године, на мјерило је постављен републички жиг у облику наљепнице број: [NOVIZIG].';
    }
}

if (!function_exists('norma_rjesenje_fetch_for_datum')) {
    /**
     * Najnovije rješenje čiji je datum izdavanja <= datum inspekcije.
     */
    function norma_rjesenje_fetch_for_datum(PDO $pdo, string $datumInspekcije): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT * FROM rjesenjazaovlascivanje
             WHERE rjesenjazaovlascivanje_datum_izdavanja <= ?
             ORDER BY rjesenjazaovlascivanje_datum_izdavanja DESC
             LIMIT 1'
        );
        $stmt->execute(array($datumInspekcije));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}

if (!function_exists('norma_rjesenje_legacy_tekst_zakljucka')) {
    /** Fallback ako u bazi nema teksta (stari zapisi). */
    function norma_rjesenje_legacy_tekst_zakljucka(?array $rjesenje, string $noviZig): string
    {
        $broj = ($rjesenje && !empty($rjesenje['rjesenjazaovlascivanje_broj_rjesenja']))
            ? $rjesenje['rjesenjazaovlascivanje_broj_rjesenja']
            : '18/1.10/393.10-03-09-25/25';
        $datum = norma_rjesenje_format_datum(
            $rjesenje['rjesenjazaovlascivanje_datum_izdavanja'] ?? null
        );

        return str_replace(
            array('[NOVIZIG]', '{{NOVIZIG}}'),
            array($noviZig, $noviZig),
            norma_rjesenje_default_tekst_zakljucka($broj, $datum)
        );
    }
}

if (!function_exists('norma_rjesenje_ispis_zakljucka')) {
    /**
     * Vraća HTML-escaped tekst paragrafa zaključka za PDF.
     */
    function norma_rjesenje_ispis_zakljucka(?array $rjesenje, string $noviZig, int $vrstaUredjajaId = 0): string
    {
        $noviZig = trim($noviZig);
        $koristiVage = norma_rjesenje_vrsta_je_vaga($vrstaUredjajaId);

        $raw = '';
        if ($rjesenje) {
            if ($koristiVage && !empty(trim((string) ($rjesenje['rjesenjazaovlascivanje_tekst_zakljucka_vage'] ?? '')))) {
                $raw = (string) $rjesenje['rjesenjazaovlascivanje_tekst_zakljucka_vage'];
            } elseif (!empty(trim((string) ($rjesenje['rjesenjazaovlascivanje_tekst_zakljucka'] ?? '')))) {
                $raw = (string) $rjesenje['rjesenjazaovlascivanje_tekst_zakljucka'];
            }
        }

        if ($raw === '') {
            $raw = norma_rjesenje_legacy_tekst_zakljucka($rjesenje, $noviZig);
        } else {
            $brojRjesenja = ($rjesenje && !empty($rjesenje['rjesenjazaovlascivanje_broj_rjesenja']))
                ? trim((string) $rjesenje['rjesenjazaovlascivanje_broj_rjesenja'])
                : '';
            $datumRjesenja = norma_rjesenje_format_datum(
                $rjesenje['rjesenjazaovlascivanje_datum_izdavanja'] ?? null
            );
            // Nova sintaksa [X] + stara {{X}} radi kompatibilnosti sa već sačuvanim tekstovima
            $raw = str_replace(
                array(
                    '[NOVIZIG]', '[BROJRJESENJA]', '[DATUMRJESENJA]',
                    '{{NOVIZIG}}', '{{BROJRJESENJA}}', '{{DATUMRJESENJA}}',
                ),
                array(
                    $noviZig, $brojRjesenja, $datumRjesenja,
                    $noviZig, $brojRjesenja, $datumRjesenja,
                ),
                $raw
            );
        }

        return htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    }
}
