/*browser:true*/
/*global define*/
define([
    'jquery',
    'underscore',
    'mage/translate'
], function ($, _, $t) {
    'use strict';
    var self;

    return function (zipcode, country, credential) {
        var url = 'https://madefor.github.io/postal-code-api/api/v1';
        var noDash = new RegExp(/^[0-9]{7}$/);
        var hasDash = new RegExp(/^[0-9]{3}\-?[0-9]{4}$/);
        var result = null;

        if(noDash.test(zipcode) || hasDash.test(zipcode)) {
            self = $;
            var code1 = zipcode.replace(/^([0-9]{3}).*/, "$1");
            var code2 = zipcode.replace(/.*([0-9]{4})$/, "$1");
            var lang = credential.lang;

            $.ajax({
                type: 'GET',
                dataType : 'json',
                url: url + '/' + code1 +'/' + code2 + '.json',
                cache: false,

                success: function (json) {
                    var data = eval(json);

                    if(!$('#region_id')[0].disabled) {
                        self('#region_id')[0][data.data[0].prefcode].selected = true;
                    } else {
                        $('#region').val(data.data[0][lang]['prefecture']);
                    }

                    self('#city').val(data.data[0][lang]['address1']);
                    self('#street_1').val(data.data[0][lang]['address2']);

                },
                error: function (json) {
                    alert($t('Provided Zip/Postal Code seems to be invalid.'));
                }
            });
        }

        return result;
    }
});
