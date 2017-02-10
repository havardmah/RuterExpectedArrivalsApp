<?php

$stops = [
    [
        "id" => 3010320,
        "name" => "St. Hanshaugen, ved Markus Kirke (retning øst)",
        "direction" => 1,
        "nextDeparture" => [],
    ], [
        "id" => 3010320,
        "name" => "St. Hanshaugen, ved Markus Kirke (retning vest)",
        "direction" => 2,
        "nextDeparture" => [],
    ], [
        "id" => 3010321,
        "name" => "St. Hanshaugen, ved Bergstien (retning øst)",
        "direction" => null,
        "nextDeparture" => [],
    ], [
        "id" => 3010322,
        "name" => "St. Hanshaugen, ved Dalbergstien (retning vest)",
        "direction" => null,
        "nextDeparture" => [],
    ]
]; // End stopIds

$result = [];

run();
function run() {
    global $stops, $result;
    foreach ($stops as $stop) {
        $id = $stop['id'];
        $direction = $stop['direction'];
        $originalData = json_decode(curlConnect($id));
        $currentDeparture = filterDepartures($direction, $originalData);
        //insertToStop($id, $direction, $currentDeparture);

        if ($direction != null) {
            if ($stop['id'] == $id && $stop['direction'] == $direction) $stop['nextDeparture'] = json_decode(json_encode($currentDeparture), true);
        } else {
            if ($stop['id'] == $id) $stop['nextDeparture'] = json_decode(json_encode($currentDeparture), true);
        } // End if/else

        $result[] = $stop;
    } // End foreach
} // End run

echo json_encode($result);

function filterDepartures ($direction, $data) {
    $result = [];

    foreach ($data as $departure) {
        if ($direction != null) {
            if ($departure->MonitoredVehicleJourney->DirectionRef == $direction) $result[] = $departure;
        } else {
            $result[] = $departure;
        } // End if/else
    } // End foreach

    return @$result[0];
} // End filterDepartures

function insertToStop($stopId, $direction, $data) {
    global $stops;
    // print_r($data);
    $nextStopInfo = [
        "route" => $data->MonitoredVehicleJourney->LineRef,
        "station" => $data->MonitoredVehicleJourney->DestinationName,
        "aimed" => $data->MonitoredVehicleJourney->MonitoredCall->AimedArrivalTime,
        "expected" => $data->MonitoredVehicleJourney->MonitoredCall->ExpectedArrivalTime
    ];

    foreach ($stops as $stop) {
        if ($direction != null) {
            if ((int)$stop['id'] == $stopId && $stop['direction'] == $direction) $stop['nextDeparture'] = json_decode(json_encode($data), true);
        } else {
            if ((int)$stop['id'] == $stopId) $stop['nextDeparture'] = json_decode(json_encode($data), true);
        } // End if/else

        echo '<pre>';
        print_r($stop);
        echo '</pre>';
    } // End foreach

    //print_r($stops);
} // End insertToStops

function curlConnect ($id) {
    $url = "http://reisapi.ruter.no/StopVisit/GetDepartures/" . $id;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
} // End curlConnect