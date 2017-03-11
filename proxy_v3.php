<?php
init();
function init() {
    if (isset($_GET['coords']) && !empty($_GET['coords'])) {
        getStops();
    } else if (isset($_GET['stop']) && !empty($_GET['stop'])) {
        getDepartures();
    } else {
        echo "Error - send coordinates or stop ID!";
        exit();
    }
} // End init

function getStops() {
    $coords = explode(",", $_GET["coords"]);
    $x = $coords[0];
    $y = $coords[1];

    $stops = json_decode(runUrl("http://reisapi.ruter.no/Place/GetClosestStops?coordinates=(x=$x,y=$y)&proposals=6&maxdistance=1400"), true);

    header('Content-Type: application/json');

    if (isset($_GET['deps'])) {
        $departures = filterDeps(getDeparturesByList($stops));
        echo json_encode($departures);
        exit();
    }

    echo json_encode($stops);
    exit();
} // End getStops

function getDepartures() {
    header('Content-Type: application/json');
    $departures = filterDepartures(getDeparturesByStop($_GET['stop']));
    echo json_encode($departures);
    exit();
} // End getDepartures

function filterDeps($data) {
    $result = [];

    foreach ($data as $stop) {

        $deps = [];

        foreach ($stop['deps'] as $dep) {
            $line = $dep['MonitoredVehicleJourney']['LineRef'];
            $dir = $dep['MonitoredVehicleJourney']['DirectionRef'];

            $status = true;
            foreach ($deps as $currDep) {
                if (($currDep['LineRef'] == $line && $currDep['DirectionRef'] == $dir) || empty($dir)) $status = false;
            } // End foreach

            if ($status) {
                $deps[] = $dep['MonitoredVehicleJourney'];
            } // End if
        } // End foreach

        $stopRes = [
            'info' => $stop['info'],
            'deps' => $deps
        ];
        $result[] = $stopRes;
    } // End foreach

    return $result;
} // End filterDeps

function filterDepartures($data) {
    $deps = [];

    foreach ($data as $dep) {
        $line = $dep['MonitoredVehicleJourney']['LineRef'];
        $dir = $dep['MonitoredVehicleJourney']['DirectionRef'];

        $status = true;
        foreach ($deps as $currDep) {
            if (($currDep['LineRef'] == $line && $currDep['DirectionRef'] == $dir) || empty($dir)) $status = false;
        } // End foreach

        if ($status) {
            $deps[] = $dep['MonitoredVehicleJourney'];
        } // End if
    } // End foreach

    return $deps;
} // End filterDepartures

function getDeparturesByStop($stopId) {
    return json_decode(runUrl("http://reisapi.ruter.no/StopVisit/GetDepartures/" . $stopId . "?transporttypes=Bus,Metro,Tram"), true);
} // End getDeparturesByStop

function getDeparturesByList($stops) {
    $result = [];
    foreach ($stops as $stop) {
        $deps = json_decode(runUrl("http://reisapi.ruter.no/StopVisit/GetDepartures/" . $stop['ID'] . "?transporttypes=Bus,Metro,Tram"), true);
        $stdp = [
            'info' => $stop,
            'deps' => $deps
        ];
        $result[] = $stdp;
    } // end foreach
    return $result;
} // End getDeparturesByList

function runUrl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
} // End runUrl

function Deb($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
} // End Deb