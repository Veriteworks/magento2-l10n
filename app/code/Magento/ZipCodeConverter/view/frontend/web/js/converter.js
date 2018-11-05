define([
    'jquery',
    'mage/template',
    'mage/translate',
    'jquery/ui',
    'mage/validation'
], function ($, mageTemplate, $t) {
    'use strict';
    var self;

    $.widget('mage.zipconverter', {
        _create: function () {
            self = $;
            $(this.element).on('change', $.proxy(this._search, this));

        },

        _search: function(event) {
            var postcode = event.target.value;
            var services = this.options.services;
            var country = this.options.country;
            var countryCode = $(country).val();
            var service = this._pickupService(services, countryCode);
            var fields = {
                'country': '#country_id',
                'region_id' : '#region_id',
                'region': '#region',
                'city': '#city',
                'street': '#street_1'
            };

            var model = require([service.model], function(converter){
                var result = converter(postcode, countryCode, fields, service.credentials);
                console.log(result);
                if(result['region']) {
                    if(!$('#region_id')[0].disabled) {
                        //self(fields['region_id'])[0][result['region_id']].selected = true;
                        $('#region_id option').filter(function(index){
                            return $(this).text() === result['region'];
                        }).prop('selected', true);
                    } else {
                        self(fields['region']).val(result['region']);
                    }
                }

                if(result['city']) {
                    self(fields['city']).val(result['city']);
                }

                if(result['street']) {
                    self(fields['street']).val(result['street']);
                }
            });
        },

        _pickupService: function (services, country) {
            if(!services[country]) {
                return services['default'];
            }
            return services[country];
        }

    });

    return $.mage.zipconverter;
});
