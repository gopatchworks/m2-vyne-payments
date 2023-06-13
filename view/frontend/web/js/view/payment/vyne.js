define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'vyne',
                component: 'Vyne_Payments/js/view/payment/method-renderer/vyne-method'
            }
        );
        return Component.extend({});
    }
);
