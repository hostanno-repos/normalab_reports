-- Dodavanje kolone za lokaciju mjerila u tabelu izvjestaji
-- Za stare izvještaje ostaje NULL - u ispisu se koristi mjesto inspekcije kao fallback

ALTER TABLE izvjestaji
ADD COLUMN izvjestaji_lokacijamjerila VARCHAR(255) NULL DEFAULT NULL
AFTER izvjestaji_mjestoinspekcije;
