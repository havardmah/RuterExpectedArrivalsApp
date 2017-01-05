<?php

if (isset($_GET['stopId']) && !empty($_GET['stopId'])) {
    $xml_feed_url = "http://reisapi.ruter.no/StopVisit/GetDepartures/" . $_GET['stopId'] . "?transporttypes=bus";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $xml_feed_url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $xml = curl_exec($ch);
    curl_close($ch);
    echo $xml;
} else {
    echo "add parameter stopId={id} on the end of the url";
}
