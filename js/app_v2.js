/**
 * Created by Håvard on 03.01.2017.
 * ngGeolocation: https://github.com/ninjatronic/ngGeolocation
 */
var ruterApp = angular.module("ruterApp", ['ngGeolocation']);

ruterApp.filter('timeCalculate', function () {
    return function (x) {
        if (x != undefined) {
            var time = new Date();
            var receivedTime = new Date(x);
            var calculatedTime = Math.abs(time - receivedTime);
            var minutesLeft = (calculatedTime/1000/60) << 0;

            if (minutesLeft == 0) return "Nå";
            return minutesLeft + " min";
        } else {
            return "-";
        }
    };
}); // End timeCalculate

ruterApp.controller("RuterController", ["$http", "$geolocation", function ($http, $geolocation) {
    var _this = this;

    _this.loading = true;
    _this.pos = null;
    _this.interval = null;

    _this.init = function () {
        $geolocation.getCurrentPosition({
            timeout: 60000,
            enableHighAccuracy: true
        }).then(function(position) {
            _this.pos = position.coords;

            console.log(_this.pos);

            _this.getBusDepartures();
            _this.interval = setInterval(_this.getBusDepartures, 5000);
        });
    }; // End init

    _this.getBusDepartures = function () {
        var apiUrl = "proxy_v2.php";

        $http.get(apiUrl)
            .success(function (response) {
                _this.response = response;
            })
            .catch(function (err) {
                console.log("Error", err);
            })
            .finally(function () {
                _this.loading = false;
            });
            /*
            .then(
                // Success
                function (response) {
                    _this.response = response.data;
                },
                // Error
                function (response) {
                    console.log("Error", response);
                }
            ); // End http
            */

    }; // End getBusDepartures

    _this.init();

}]); // End RuterController