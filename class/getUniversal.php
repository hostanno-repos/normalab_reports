<?php
// UniversalQuery.php

class UniversalQuery {

    // Uklanjamo svojstva $total_pages i $total_records jer će biti globalne

    /**
     * Izvršava univerzalni SELECT upit.
     */
    public function executeQuery(object $getObjects) {
        // ... (Logika za executeQuery ostaje ista, jer ne koristi $total_pages) ...
        global $pdo;
        // ... (Ostatak executeQuery je nepromenjen) ...
        
        if (!($pdo instanceof PDO)) { throw new Exception("PDO konekcija (\$pdo) nije dostupna."); }
        $table = $getObjects->table ?? null;
        if (!$table) { throw new InvalidArgumentException("Naziv tabele je obavezan (svojstvo 'table')."); }

        $select = $getObjects->select ?? '*';
        $tableAlias = $getObjects->tableAlias ?? 't';
        $sql = "SELECT $select FROM `$table` AS `$tableAlias`";
        $bindings = [];

        // JOIN-ovi
        if (isset($getObjects->joins) && is_array($getObjects->joins)) {
            foreach ($getObjects->joins as $join) {
                $type = strtoupper($join->type ?? 'INNER'); $joinTableWithAlias = $join->table ?? null; $onCondition = $join->on ?? null;
                if ($joinTableWithAlias && $onCondition) { $sql .= " $type JOIN $joinTableWithAlias ON $onCondition"; }
            }
        }

        // WHERE uslovi
        if (isset($getObjects->where) && is_array($getObjects->where)) {
            $whereClauses = []; $paramIndex = 0;
            foreach ($getObjects->where as $condition) {
                if (count($condition) === 3) {
                    list($column, $operator, $value) = $condition; $paramName = ":val" . $paramIndex++;
                    $whereClauses[] = "$column $operator $paramName"; $bindings[$paramName] = $value;
                } else if (count($condition) === 1 && is_string($condition[0])) { $whereClauses[] = $condition[0]; }
            }
            if (!empty($whereClauses)) { $sql .= " WHERE " . implode(" AND ", $whereClauses); }
        }
        
        // ORDER BY, LIMIT, OFFSET
        if (isset($getObjects->orderBy)) { $sql .= " ORDER BY " . $getObjects->orderBy; }
        if (isset($getObjects->limit)) { $sql .= " LIMIT " . (int)$getObjects->limit; }
        if (isset($getObjects->offset)) { $sql .= " OFFSET " . (int)$getObjects->offset; }
        
        try {
            $stmt = $pdo->prepare($sql); $stmt->execute($bindings);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
    }
    
    /**
     * Izvršava COUNT upit i DFINIŠE GLOBALNE VARIJABLE $total_pages i $total_records.
     *
     * @param object $getObjects Konfiguracioni objekat.
     * @param int $perPage Broj objekata po stranici.
     */
    public function countTotal(object $getObjects, int $perPage) {
        // KLJUČNA IZMJENA: Deklarišemo globalne varijable koje želimo postaviti
        global $pdo;
        global $total_pages;
        global $total_records;
        
        if (!($pdo instanceof PDO)) return;

        $table = $getObjects->table ?? null;
        if (!$table) return;

        $tableAlias = $getObjects->tableAlias ?? 't';
        $sql = "SELECT COUNT(*) FROM `$table` AS `$tableAlias`";
        $bindings = [];

        // JOIN-ovi
        if (isset($getObjects->joins) && is_array($getObjects->joins)) {
            foreach ($getObjects->joins as $join) {
                $type = strtoupper($join->type ?? 'INNER'); $joinTableWithAlias = $join->table ?? null; $onCondition = $join->on ?? null;
                if ($joinTableWithAlias && $onCondition) { $sql .= " $type JOIN $joinTableWithAlias ON $onCondition"; }
            }
        }

        // WHERE uslovi
        if (isset($getObjects->where) && is_array($getObjects->where)) {
            $whereClauses = []; $paramIndex = 0;
            foreach ($getObjects->where as $condition) {
                if (count($condition) === 3) {
                    list($column, $operator, $value) = $condition; $paramName = ":val" . $paramIndex++;
                    $whereClauses[] = "$column $operator $paramName"; $bindings[$paramName] = $value;
                } else if (count($condition) === 1 && is_string($condition[0])) { $whereClauses[] = $condition[0]; }
            }
            if (!empty($whereClauses)) { $sql .= " WHERE " . implode(" AND ", $whereClauses); }
        }
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($bindings);
            
            // Postavljanje globalnih varijabli
            $total_records = (int)$stmt->fetchColumn(); 
            $total_pages = (int)ceil($total_records / $perPage);
            
        } catch (PDOException $e) {
            error_log("Database COUNT Error: " . $e->getMessage() . " | SQL: " . $sql);
            // U slučaju greške, postavi na 0
            $total_records = 0;
            $total_pages = 0;
        }
    }
}
?>