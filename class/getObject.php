<?php

//GET ALL OBJECTS
class allObjects
{
    public function fetch_all_objects($table, $orderBy, $ordering)
    {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM " . $table . " ORDER BY " . $orderBy . " " . $ordering);
        $query->execute();
        return $query->fetchAll();
    }
}

//GET ALL OBJECTS BY
class allObjectsBy
{
    public function fetch_all_objects_by($table, $getBy, $useToGetBy, $orderBy, $ordering)
    {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM " . $table . " WHERE " . $getBy . " = '" . $useToGetBy . "' ORDER BY " . $orderBy . " " . $ordering);
        //var_dump($query);
        $query->execute();
        return $query->fetchAll();
    }
}

//GET ALL OBJECTS BY
class allObjectsByLike
{
    public function fetch_all_objects_by($table, $getBy, $useToGetBy, $orderBy, $ordering)
    {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM " . $table . " WHERE " . $getBy . " LIKE '%" . $useToGetBy . "%' ORDER BY " . $orderBy . " " . $ordering);
        //var_dump($query);
        $query->execute();
        return $query->fetchAll();
    }
}

//GET ALL OBJECTS BY
class allObjectsBy2
{
    public function fetch_all_objects_by2($table, $getBy, $useToGetBy, $getBy2, $useToGetBy2, $orderBy, $ordering)
    {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM " . $table . " WHERE " . $getBy . " = '" . $useToGetBy . "' AND " . $getBy2 . " = '" . $useToGetBy2 . "' ORDER BY " . $orderBy . " " . $ordering);
        //var_dump($query);
        $query->execute();
        return $query->fetchAll();
    }
}

//GET SINGLE OBJECT
class singleObject
{
    public function fetch_single_object($table, $columnToGet, $idToGet)
    {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM " . $table . " WHERE " . $columnToGet . " = ?");
        $query->bindValue(1, $idToGet);
        $query->execute();
        return $query->fetch();
    }
}

//GET ALL REZULTATI
class allResults
{
    public function fetch_all_results($izvjestajid, $mjernavelicinaid, $referentnavrijednostid)
    {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM rezultatimjerenja WHERE rezultatimjerenja_izvjestajid = '" . $izvjestajid . "' AND rezultatimjerenja_mjernavelicinaid = '" . $mjernavelicinaid . "' AND rezultatimjerenja_referentnavrijednostid = '" . $referentnavrijednostid . "'");
        //var_dump($query);
        $query->execute();
        return $query->fetchAll();
    }
}

class allResultsWithSort
{
    public function fetch_all_results_with_sort($izvjestajid, $mjernavelicinaid, $referentnavrijednostid, $sortBy, $sorting)
    {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM rezultatimjerenja WHERE rezultatimjerenja_izvjestajid = '" . $izvjestajid . "' AND rezultatimjerenja_mjernavelicinaid = '" . $mjernavelicinaid . "' AND rezultatimjerenja_referentnavrijednostid = '" . $referentnavrijednostid . "' ORDER BY " . $sortBy . " " . $sorting);
        //var_dump($query);
        $query->execute();
        return $query->fetchAll();
    }
}

//GET ALL OBJECTS WITH PAGINATION
/*class allObjectsWithPagination
{
    public function fetch_all_objects_with_pagination($table, $orderBy, $ordering, $perPage)
    {
        global $pdo;
        $stmt = $pdo->query('SELECT count(*) FROM ' . $table);
        $total_results = $stmt->fetchColumn();
        $total_pages = ceil($total_results / $perPage);

        // Current page
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $starting_limit = ($page - 1) * $perPage;

        // Query to fetch novosti
        $query = "SELECT * FROM " . $table . " ORDER BY " . $orderBy . " " . $ordering . " LIMIT " . $starting_limit . "," . $perPage;

        // Fetch all novosti for current page
        $output = array();
        array_push($output, $pdo->query($query)->fetchAll());
        array_push($output, strval($total_pages));
        return $output;
    }
}*/

class allObjectsWithPagination
{
    const CACHE_TTL_KLIJENTI = 60;

    public function fetch_all_objects_with_pagination($table, $orderBy, $ordering, $perPage, $joins = [], $where = null, $params = [], $columns = '*')
    {
        global $pdo;

        $useCache = ($table === 'klijenti' && empty($joins) && empty($where));
        $cacheDir = dirname(__DIR__) . '/cache';
        $cacheFile = $cacheDir . '/klijenti_count.json';

        if ($useCache && is_file($cacheFile)) {
            $cached = @json_decode(file_get_contents($cacheFile), true);
            if (is_array($cached) && isset($cached['ts'], $cached['count']) && (time() - (int)$cached['ts']) < self::CACHE_TTL_KLIJENTI) {
                $total_results = (int) $cached['count'];
            }
        }

        if (!isset($total_results)) {
            $sqlCount = "SELECT COUNT(*) FROM " . $table;
            if (!empty($joins)) {
                foreach ($joins as $join) {
                    $type = strtoupper($join['type'] ?? 'INNER');
                    $sqlCount .= " " . $type . " JOIN " . $join['table'] . " ON " . $join['on'];
                }
            }
            if (!empty($where)) {
                $sqlCount .= " WHERE " . $where;
            }
            $stmt = $pdo->prepare($sqlCount);
            $stmt->execute($params);
            $total_results = (int) $stmt->fetchColumn();

            if ($useCache) {
                if (!is_dir($cacheDir)) {
                    @mkdir($cacheDir, 0755, true);
                }
                @file_put_contents($cacheFile, json_encode(array('ts' => time(), 'count' => $total_results)));
            }
        }

        $total_pages = $total_results > 0 ? (int) ceil($total_results / $perPage) : 0;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        if ($total_pages > 0 && $page > $total_pages) {
            $page = $total_pages;
        }
        $starting_limit = ($page - 1) * $perPage;

        if ($total_results === 0) {
            $output = array();
            array_push($output, array());
            array_push($output, '0');
            array_push($output, 0);
            return $output;
        }

        $query = "SELECT " . ($columns ?? '*') . " FROM " . $table;
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $type = strtoupper($join['type'] ?? 'INNER');
                $query .= " " . $type . " JOIN " . $join['table'] . " ON " . $join['on'];
            }
        }
        if (!empty($where)) {
            $query .= " WHERE " . $where;
        }
        $query .= " ORDER BY " . $orderBy . " " . $ordering;
        $query .= " LIMIT " . $starting_limit . "," . $perPage;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);

        $output = array();
        array_push($output, $results);
        array_push($output, strval($total_pages));
        array_push($output, $total_results);

        return $output;
    }
}


?>