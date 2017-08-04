(function () {
    'use strict';
    var app = angular.module('liftDispatcher');
    app.component('app', {
        transclude: true,
        bindings: {
            buildingId: '<'
        },
        template: '<div ng-transclude><building data="$ctrl.building"></building></div>',
        controller: ['ApiService', function (ApiService) {
            var self = this;

            self.building = null;
            this.$onInit = function () {
                ApiService.setBuildingId(this.buildingId);
                ApiService.building.get().then(function (res) {
                    self.building = res;
                });
            };
        }]
    });
})();