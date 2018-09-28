/*browser:true*/
/*global define*/
define([
    'jquery',
    'underscore',
    'mage/translate'
], function ($, _, $t) {
    'use strict';

    return function (zipcode, country, credential) {
        var url = 'https://maps.googleapis.com/maps/api/geocode/json';
        var result = null;

        $.ajax({
            type: 'GET',
            dataType : 'json',
            url: url + '?address=' + zipcode + '&components=country:' + country + '&key=' + credential.key,
            cache: false,

            success: function (response) {
                if(response.status && response.status == 'OK') {
                    result = response.results[0]['address_components'];

                    //var zip = result[0]['long_name'];
                    var street = result[1]['long_name'];
                    var city = result[2]['long_name'];
                    var region = result[3]['long_name'];
                    var country = result[4]['short_name'];

                    $('#country option').filter(function(index){
                        return $(this).val() === country;
                    }).prop('selected', true);

                    if(!$('#region_id')[0].disabled) {
                        $('#region_id option').filter(function(index){
                            return $(this).text() === region;
                        }).prop('selected', true);
                    } else {
                        $('#region').val(region);
                    }

                    $('#city').val(city);
                    $('#street_1').val(street);

                } else {
                    result = $t('Provided Zip/Postal Code seems to be invalid.');
                }
            },
            error: function (json) {
                result = $t('Provided Zip/Postal Code seems to be invalid.');
            }
        });

        return result;
    }
});
