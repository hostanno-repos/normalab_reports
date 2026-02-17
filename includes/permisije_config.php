<?php
/**
 * Definicija sekcija i akcija za permisije.
 * Koristi se na stranici Podešavanja → Permisije i (buduće) za skrivanje stavki prema ulozi.
 */

// Sekcije aplikacije (stranice / grupe funkcionalnosti)
$PERMISIJE_SEKCIJE = array(
    array('kljuc' => 'pregledklijenata',     'naziv' => 'Pregled klijenata'),
    array('kljuc' => 'pregledmjerila',       'naziv' => 'Pregled mjerila'),
    array('kljuc' => 'opremazainspekciju',   'naziv' => 'Oprema za inspekciju'),
    array('kljuc' => 'brojacirn',            'naziv' => 'Brojači radnih naloga'),
    array('kljuc' => 'pregledradnihnaloga',  'naziv' => 'Pregled radnih naloga'),
    array('kljuc' => 'tipoviizvjestaja',     'naziv' => 'Tipovi izvještaja'),
    array('kljuc' => 'pregledizvjestaja',    'naziv' => 'Pregled izvještaja'),
    array('kljuc' => 'kontrolori',           'naziv' => 'Kontrolori'),
    array('kljuc' => 'metodeinspekcije',     'naziv' => 'Metode inspekcije'),
    array('kljuc' => 'vrsteinspekcije',      'naziv' => 'Vrste inspekcije'),
    array('kljuc' => 'vrsteuredjaja',        'naziv' => 'Vrste uređaja'),
    array('kljuc' => 'mjernevelicine',       'naziv' => 'Mjerne veličine'),
    array('kljuc' => 'referentnevrijednosti','naziv' => 'Referentne vrijednosti'),
    array('kljuc' => 'rjesenjazaovlascivanje', 'naziv' => 'Rješenja o ovlašćivanju'),
    array('kljuc' => 'nivoihijerarhije',     'naziv' => 'Nivoi hijerarhije'),
    array('kljuc' => 'korisnickeuloge',      'naziv' => 'Korisničke uloge'),
    array('kljuc' => 'korisnici',            'naziv' => 'Korisnici'),
);

// Akcije unutar sekcija (zajednički set za sve sekcije)
$PERMISIJE_AKCIJE = array(
    array('kljuc' => 'pregled',   'naziv' => 'Pregled'),
    array('kljuc' => 'dodavanje', 'naziv' => 'Dodavanje'),
    array('kljuc' => 'uredivanje','naziv' => 'Uređivanje'),
    array('kljuc' => 'brisanje',  'naziv' => 'Brisanje'),
);

// Uloge kojima se NE dodjeljuju permisije – uvijek imaju sve dozvoljeno (Administrator, Super administrator)
$PERMISIJE_ULOGE_ISKLJUCENE = array(1, 7);

// Sekcije za koje se u Podešavanjima ne prikazuje akcija Brisanje (npr. mjerne veličine se ne smiju brisati)
$PERMISIJE_SEKCIJE_BEZ_BRISANJA = array('mjernevelicine');
