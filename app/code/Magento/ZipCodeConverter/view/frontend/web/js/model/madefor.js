/*browser:true*/
/*global define*/
define([
    'jquery',
    'underscore',
    'mage/translate'
], function ($, _, $t) {
    'use strict';
    var self;

    return function (zipcode, country, fields, credential) {
        var url = 'https://madefor.github.io/postal-code-api/api/v1';
        var noDash = new RegExp(/^[0-9]{7}$/);
        var hasDash = new RegExp(/^[0-9]{3}\-?[0-9]{4}$/);
        var result = [];

        if(noDash.test(zipcode) || hasDash.test(zipcode)) {
            self = $;
            var code1 = zipcode.replace(/^([0-9]{3}).*/, "$1");
            var code2 = zipcode.replace(/.*([0-9]{4})$/, "$1");
            var lang = credential.lang;

            var api = function () {
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    async: false,
                    url: url + '/' + code1 + '/' + code2 + '.json',
                    cache: false
                });
            };

            api().done(function (json) {
                var data = eval(json);

                result = {
                    'region': data.data[0][lang]['prefecture'],
                    //'region_id': data.data[0].prefcode,
                    'city': data.data[0][lang]['address1'],
                    'street': data.data[0][lang]['address2']
                };
            });

            return result;
        }
    }
});
