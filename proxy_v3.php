<?php
init();
function init() {

    $coords = truncRequest();
    $x = $coords[0];
    $y = $coords[1];

    $stops = json_decode(runUrl("http://reisapi.ruter.no/Place/GetClosestStops?coordinates=(x=$x,y=$y)&proposals=6&maxdistance=1400"), true);

    $departures = getDepartures($stops);



    echo json_encode(filterDeps($departures));

} // End init

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

function getDepartures($stops) {
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
} // End getDepartures

function truncRequest() {
    if (!isset($_GET['coords']) || empty($_GET['coords'])) {
        echo "Error, send coordinates!";
        exit();
    } // End if
    return explode(",", $_GET["coords"]);
} // End truncRequest

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
}