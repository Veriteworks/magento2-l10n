/*browser:true*/
/*global define*/
define([
    'jquery',
    'underscore',
    'mage/translate'
], function ($, _, $t) {
    'use strict';

    return function (zipcode, country, fields, credential) {
        var url = 'https://maps.googleapis.com/maps/api/geocode/json';
        var result = null;

        var api = function () {
            return $.ajax({
                type: 'GET',
                dataType : 'json',
                async: false,
                url: url + '?address=' + zipcode + '&components=country:' + country + '&key=' + credential.key,
                cache: false
            });
        };

        api().done(function (response) {
            if(response.status && response.status == 'OK') {
                result = response.results[0]['address_components'];

                //var zip = result[0]['long_name'];
                var street = result[1]['long_name'];
                var city = result[2]['long_name'];
                var region = result[3]['long_name'];
                var country = result[4]['short_name'];

                result = {
                    'region': region,
                    'city': city,
                    'street': street
                };

            }
        });

        return result;
    }
});
