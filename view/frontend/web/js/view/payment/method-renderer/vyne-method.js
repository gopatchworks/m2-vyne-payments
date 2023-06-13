define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'mage/url',
        'mage/translate',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/customer-data',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Ui/js/modal/alert'
    ],
    function (Component, quote, urlBuilder, storage, url, $t, errorProcessor, customer, customerData, globalMessageList, fullScreenLoader, alertModal) {
        'use strict';
        return Component.extend({
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Vyne_Payments/payment/vyne'
            },
            displayMessage: function(msg) {
                alertModal({
                    title: 'Error',
                    content: msg,
                    actions: {
                        always: function(){
                            window.scrollTo(0,0);
                            globalMessageList.addErrorMessage({
                                message: $t(msg)
                            });
                        }
                    }
                });
            },
            getColourScheme: function() {
                var scheme = window.checkoutConfig.payment.vyne.colour_scheme == 'Dark' ? 'dark' : 'light';
                return scheme + ' payment-method-content';
            },
            showPopup: function() {
                document.getElementsByClassName('vyne-popup')[0].style.visibility = 'visible';
            },
            hidePopup: function() {
                document.getElementsByClassName('vyne-popup')[0].style.visibility = 'hidden';
            },
            getMethodImage: function () {
                return checkoutConfig.image[this.item.method];
            },
            getInstructions: function () {
                return window.checkoutConfig.payment.vyne.description;
            },
            afterPlaceOrder: function () {
                window.location.replace(url.build('vyne/gateway/redirect/'));
            },
            /**
             * @returns {String}
             */
            getCode: function () {
                return 'vyne';
            },
        });
    }
);
