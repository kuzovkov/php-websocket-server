#!/usr/bin/env php
    
<?php 
    //$DB_FILE = 'ukraine-latest.osm.sqlite';
    $DB_FILE = 'texas-latest.osm.sqlite';
    
    $db = new SQLite3($DB_FILE);
    $db->loadExtension('libspatialite.so');
    
    $nodes = array();
    $roads = array();
    
    $sql = 'SELECT node_from, node_to, name, cost, length, Y(rn.geometry) AS lat_from, X(rn.geometry) AS lng_from, Y(rn2.geometry) AS lat_to, X(rn2.geometry) AS lng_to, AsGeoJSON(r.geometry) AS geometry FROM roads r,roads_nodes rn, roads_nodes rn2 WHERE r.node_from=rn.node_id AND r.node_to=rn2.node_id ORDER BY node_from,node_to';
    $start = microtime(true);
    
    $res = $db->query($sql);
    while ($row = $res->fetchArray(SQLITE3_ASSOC)){
        $roads[] = $row;
    }
    echo "Roads loaded \n";
    
    $sql = 'SELECT node_id, cardinality, Y(geometry) AS lat, X(geometry) AS lng FROM roads_nodes';
    $res = $db->query($sql);
    while ($row = $res->fetchArray(SQLITE3_ASSOC)){
        $nodes[] = $row;
    }
    
    $end = microtime(true);
    echo "Nodes loaded \n"; 
    echo 'Executing time: ' . ($end - $start) . " c\n";
    print_r($roads[1000000]);
    
    while (trim($sql) != 'quit'){
        echo 'Input SQL query or "quit" for exit: ';
        $sql = fgets(STDIN);
        
        if (trim($sql) == 'quit') break;
        $start = microtime(true);
        $res = $db->query($sql);
        while ($row = $res->fetchArray(SQLITE3_ASSOC)){
            foreach($row as $key=>$value){
                echo $key . ': ' . $value . ';  ';
            }
            echo "\n";
        }
        $end = microtime(true);
        echo 'Executing time: ' . ($end - $start) . " c\n";
    }
    
    