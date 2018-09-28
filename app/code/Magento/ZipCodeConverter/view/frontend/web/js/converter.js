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

            var model = require([service.model], function(converter){

                var result = converter(postcode, countryCode, service.credentials);
                console.log(result);
            });
        },

        _pickupService: function (services, country) {
            var keys = services.keys;
            if(!services[country]) {
                return services['default'];
            }
            return services[country];
        }

    });

    return $.mage.zipconverter;
});
