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
        return minutesLeft + " min";
    };
}); // End timeCalculate

ruterApp.controller("RuterController", ["$http", function ($http) {
    var _this = this;

    _this.busStops = [{ id: 3010320, name: "St. Hanshaugen, ved Markus Kirke"}, { id: 3010321, name: "St. Hanshaugen, ved Bergstien" }, { id: "3010322", name: "St. Hanshaugen, ved Dalbergstien" }];

    var apiUrl = "proxy.php?stopId="; // + Busstop ID on end
    $http
        .get(apiUrl + "3010320")
        .then(
            // Success
            function (response) {
               console.log("Success", response);
            },
            // Error
            function (response) {
                console.log("Error", response);
            }
        );
}]); // End RuterController