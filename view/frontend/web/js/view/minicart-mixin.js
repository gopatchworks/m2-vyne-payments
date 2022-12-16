define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore',
    'sidebar',
    'mage/translate',
    'mage/dropdown'
], function (Component, customerData, $, ko, _) {
    'use strict';

    var mixin = {
        getCartParam: function () {
            try {
                var mini_cart_wrapper = document.getElementById("minicart-content-wrapper");
                console.log(mini_cart_wrapper);
            }
            catch (err) {
                console.log(err);
                // do nothing
            }

            return this._super();
        }
    };

    return function (Component) {
        return Component.extend(mixin);
    };
});
