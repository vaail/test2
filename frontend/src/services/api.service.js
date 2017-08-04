(function () {
    'use strict';
    var app = angular.module('liftDispatcher');
    app.service('ApiService', ['$http', '$q', function ($http, $q) {
        var baseUrl = '/api/';
        var buildingId = null;
        var building = null;
        var lifts = null;

        function encodeData(data) {
            return Object.keys(data).map(function(key) {
                return [key, data[key]].map(encodeURIComponent).join("=");
            }).join("&");
        }
        
        function genUrl(path, data) {
            var url = [baseUrl, path].join('');
            data = typeof data === 'undefined' ? {} : data;
            data = Object.keys(data).reduce(function(rest, key) {
                if(typeof data[key] !== 'undefined' && data[key] !== null && data[key] !== '') { rest[key] = data[key] }
                return rest;
            }, {});
            if(Object.keys(data).length > 0) {
                url = [url, encodeData(data)].join('?');
            }

            return url;
        }

        function setBuildingId(id) {
            buildingId = id;
        }

        function getBuilding() {
            if(building !== null) {
                return $q(function(resolve, reject) {
                    resolve(building);
                });
            } else {
                return $http({
                    method: 'GET',
                    url: genUrl('building/'+buildingId)
                }).then(function (res) {
                    building = res.data;
                    return building;
                })
            }
        }

        function getLifts() {
            if(lifts !== null) {
                return $q(function(resolve, reject) {
                    resolve(lifts);
                });
            } else {
                return $http({
                    method: 'GET',
                    url: genUrl('lift', {building_id: buildingId})
                }).then(function (res) {
                    lifts = res.data;
                    return lifts;
                })
            }
        }

        function order(floor, target_floor, direction) {
            return $http({
                method: 'GET',
                url: genUrl('order', {building_id: buildingId, floor: floor, target_floor: target_floor, direction: direction})
            }).then(function (res) {
                return res.data;
            });
        }

        return {
            setBuildingId: setBuildingId,
            building: {
                get: getBuilding
            },
            lift: {
                get: getLifts
            },
            order: order
        };
    }]);
})();