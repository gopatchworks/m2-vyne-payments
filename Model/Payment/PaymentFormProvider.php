<?php
namespace Vyne\Magento\Model\Payment;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Cart;
use Vyne\Magento\Helper\Data as VyneHelper;

/**
 * Class BillingAgreementConfigProvider
 */
class PaymentFormProvider implements ConfigProviderInterface
{
    const CODE = 'vyne';
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        UrlInterface $urlBuilder,
        Cart $cart,
        VyneHelper $vyneHelper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->urlBuilder = $urlBuilder;
        $this->cart = $cart;
        $this->vyneHelper = $vyneHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'vyne' => [
                    'method' => __('Vyne Payment'),
                    'environment' => $this->vyneHelper->getVyneEnvironment(),
                    'description' => $this->vyneHelper->getPaymentInstructions(),
                    'colour_scheme' => $this->vyneHelper->getColourScheme(),
                    'isActive' => $this->vyneHelper->isEnabled()
                ]
            ]
        ];

        return $config;
    }
}
