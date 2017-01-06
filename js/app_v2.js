/**
 * Created by Håvard on 03.01.2017.
 */
var ruterApp = angular.module("ruterApp", []);

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

ruterApp.controller("RuterController", ["$http", function ($http) {
    var _this = this;

    _this.loading = true;

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

    } // End getBusDepartures

    _this.getBusDepartures();
    setInterval(_this.getBusDepartures, 5000);

}]); // End RuterController