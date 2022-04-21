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
            defaults: {
                template: 'Vyne_Magento/payment/vyne'
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
            initVynePayment: function () {
            },
            getInstructions: function () {
                return window.checkoutConfig.payment.vyne.description;
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
