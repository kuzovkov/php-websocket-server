#!/usr/bin/env php

<?php 
    //$DB_FILE = 'ukraine-latest.osm.sqlite';
    $DB_FILE = 'texas-latest.osm.sqlite';
    
    $db = new SQLite3($DB_FILE);
    $db->loadExtension('libspatialite.so');
    $sql = '';
    //$sql = 'SELECT count(*) FROM roads';
    //$sql = 'SELECT AsGeoJSON(geometry) AS geometry FROM roads_net WHERE NodeFrom=322345 AND NodeTo=704608 LIMIT 1';
    
    while (trim($sql) != 'quit'){
        echo 'Input SQL query or "quit" for exit: ';
        $sql = fgets(STDIN);
        if (trim($sql) == 'quit') break;
        $start = microtime(true);
        $res = $db->query($sql);
        $end = microtime(true);
        echo 'Executing time: ' . ($end - $start) . " c\n";
        while ($row = $res->fetchArray(SQLITE3_ASSOC)){
            foreach($row as $key=>$value){
                echo $key . ': ' . $value . ';  ';
            }
            echo "\n";
        }
        
    }
    
    