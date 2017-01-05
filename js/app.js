/**
 * Created by Håvard on 03.01.2017.
 */
var ruterApp = angular.module("ruterApp", []);

ruterApp.filter('timeCalculate', function () {
    return function (x) {
        var time = new Date();
        var receivedTime = new Date(x);
        var calculatedTime = Math.abs(time - receivedTime);
        var minutesLeft = (calculatedTime/1000/60) << 0;

        if (minutesLeft == 0) return "Nå";
        return minutesLeft + " min";
    };
}); // End timeCalculate

ruterApp.controller("RuterController", ["$http", function ($http) {
    var _this = this;

    _this.busStops = [
            { id: 3010320, name: "St. Hanshaugen, ved Markus Kirke (retning øst)", direction: "1", nextDeparture: null },
            { id: 3010320, name: "St. Hanshaugen, ved Markus Kirke (retning vest)", direction: "2", nextDeparture: null },
            { id: 3010321, name: "St. Hanshaugen, ved Bergstien (retning øst)", direction: null, nextDeparture: null },
            { id: 3010322, name: "St. Hanshaugen, ved Dalbergstien (retning vest)", direction: null, nextDeparture: null }
        ];

    function getBusDepartures(stopId, direction) {
        var apiUrl = "proxy.php?stopId="; // + Busstop ID on end

        $http
            .get(apiUrl + stopId)
            .then(
                // Success
                function (response) {
                    _this.response = response.data;
                    filterDepartures(stopId, direction);
                },
                // Error
                function (response) {
                    console.log("Error", response);
                }
            ); // End http

        return true;
    } // End getBusDepartures

    function filterDepartures(stopId, direction) {
        var result = "";
        var currentData = _this.response;

        if (direction != null) {
            var filteredRoutes = currentData.filter(function (val) {
                if (val.MonitoredVehicleJourney.DirectionRef == direction) return val;
            });
            insertToStops(stopId, direction, filteredRoutes[0].MonitoredVehicleJourney);
        } else {
            insertToStops(stopId, direction, currentData[0].MonitoredVehicleJourney);
        } // End if/else

    } // End filterDepartures

    function insertToStops(stopId, direction, data) {
        var stopLength = _this.busStops.length;

        for (var i = 0; i < stopLength; i++) {
            if (_this.busStops[i].id == stopId && _this.busStops[i].direction == direction) {
                _this.busStops[i].nextDeparture = data;
            } // End if
        } // End for
    } // End insertToStops

    function refreshValues() {
        for (var i = 0; i < _this.busStops.length; i++) {
            getBusDepartures(_this.busStops[i].id, _this.busStops[i].direction);
        }
    } // End filterDepartures

    refreshValues();
    setInterval(refreshValues, 5000);

}]); // End RuterController