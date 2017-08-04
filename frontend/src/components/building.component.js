(function () {
    'use strict';
    var app = angular.module('liftDispatcher');
    app.component('building', {
        bindings: {
            building: '<data'
        },
        templateUrl: '/src/components/building.component.html',
        controller: ['ApiService', function (ApiService) {
            var self = this;
            this.log = '';
            this.tmp = [];
            this.lifts = [];
            this.order = function (floor, targetFloor, direction) {
                if((direction === 'up' && floor === self.building.floorsCount)
                || (direction === 'down' && floor === 1)) {
                    return;
                }
                //targetFloor = 99;
                ApiService.order(floor, targetFloor, direction).then(function(res) {
                    self.log += ['Lift #', res.id, ' from ', floor, ' to ', res.currentFloor, "\n"].join('');
                    var index = self.lifts.findIndex(function(item) {
                        return item.id === res.id
                    });
                    if(index !== -1) {
                        angular.extend(self.lifts[index], res);
                    }
                }).catch(function (res) {
                    self.log += ['Error!!! ', res.data.error, "\n"].join('');
                });
            };
            this.$onChanges = function (changes) {
                if(changes.building.currentValue !== null) {
                    self.tmp = Array.apply(null, {length: changes.building.currentValue.floorsCount}).map(Function.call, Math.random);
                    ApiService.lift.get().then(function (res) {
                        self.lifts = res;
                    });
                }
            };
        }]
    });
})();