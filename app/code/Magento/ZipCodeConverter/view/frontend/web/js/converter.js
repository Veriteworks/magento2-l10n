define([
    'jquery',
    'mage/template',
    'mage/translate',
    'jquery/ui',
    'mage/validation'
], function ($, mageTemplate, $t) {
    'use strict';
    var self;

    $.widget('zipcodeconverter', {
        _create: function () {
            self = $;
            $(this.element).on('change', $.proxy(this._search, this));

        },

        _search: function(event) {
            self = $;
            var postcode = event.target.value;
            //todo: pass value into concrete api implementation
            //todo: how to find out which model is good for the country?
        }

    });

    return $.zipcodeconverter;
});
