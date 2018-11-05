define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'mageUtils',
    'jquery',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/translate'
], function (_, registry, Abstract, utils, $, fullScreenLoader, $t) {
    'use strict';

    return Abstract.extend({
        validatedPostCodeExample: [],

        defaults: {
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            this._super();
            var postCode = this.value();
            var country = registry.get(this.parentName + '.' + 'country_id');
            var countryId = country.value();
            //var patterns = window.checkoutConfig.postCodes[countryId];
            var region = registry.get(this.parentName + '.' + 'region_id').uid;
            var regionObj = registry.get(this.parentName + '.' + 'region_id');
            var city = registry.get(this.parentName + '.' + 'city');
            var street = registry.get(this.parentName + '.' + 'street.0');

            this.validatedPostCodeExample = [];

            if (!utils.isEmpty(postCode)) {
                var fields = {
                    'country': '#country_id',
                    'region_id' : '#region_id',
                    'region': '#region',
                    'city': '#city',
                    'street': '#street_1'
                };

                var service = this._pickupService(services, countryId);

                var model = require([service.model], function(converter){
                    var result = converter(postCode, countryId, fields, service.credentials);
                    if($('#'+ region)[0]) {
                        $('#'+ region)[0][data.data[0].prefcode].selected = true;
                        regionObj.value($('#'+ region)[0][data.data[0].prefcode].value);
                    }

                    city.value(data.data[0][lang]['address1']);
                    street.value(data.data[0][lang]['address2']);
                    console.log(result);
                });
            }

        },

        _pickupService: function (services, country) {
            if(!services[country]) {
                return services['default'];
            }
            return services[country];
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var country = registry.get(this.parentName + '.' + 'country_id'),
                options = country.indexedOptions,
                option;
            var postcode = registry.get(this.parentName + '.' + 'postcode');

            if (!value) {
                return;
            }

            option = options[value];

            if (option['is_zipcode_optional']) {
                this.error(false);
                this.validation = _.omit(this.validation, 'required-entry');
            } else {
                this.validation['required-entry'] = true;
            }

            this.required(!option['is_zipcode_optional']);
        }
    });
});
