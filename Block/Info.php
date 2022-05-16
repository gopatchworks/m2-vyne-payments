<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Block;

class Info extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @var \Vyne\Magento\Api\TransactionRepositoryInterface
     */
    protected $_transactionRepository;

    /**
     * @var \Vyne\Magento\Helper\Data
     */
    protected $_vyneHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Vyne\Magento\Api\TransactionRepositoryInterface $transactionRepository
     * @param \Vyne\Magento\Helper\Data $vyneHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Vyne\Magento\Api\TransactionRepositoryInterface $transactionRepository,
        \Vyne\Magento\Helper\Data $vyneHelper,
        array $data = []
    ) {
        $this->_transactionRepository = $transactionRepository;
        $this->_vyneHelper = $vyneHelper;
        parent::__construct($context, $paymentConfig, $data);
    }

    /**
     * Don't show CC type for non-CC methods
     *
     * @return string|null
     */
    public function getCcTypeName()
    {
        return parent::getCcTypeName();
    }

    /**
     * @return string
     */
    public function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $data = [];
        $vyne_transaction_id = $payment->getData('vyne_transaction_id');
        //$transaction = $this->_transactionRepository->getByVyneTransactionId($vyne_transaction_id);

        //if ($transaction && $transaction->getId()) {
            /*prepare labels*/
            $last_trans_id = (string)__('Last Transaction ID');
            $status = (string)__('Status');
            $amount = (string)__('Amount');
            $captured_amount = (string)__('Captured Amount');
            $refunded_amount = (string)__('Refunded Amount');
            $currency = (string)__('Currency');

            /*prepare data*/
            $captured = $payment->getAmountPaid() ? $this->_vyneHelper->formatCurrency($payment->getAmountPaid()) : 0;
            $refunded = $payment->getAmountRefunded() ? $this->_vyneHelper->formatCurrency($payment->getAmountRefunded()) : 0;
            $data = array(
                $last_trans_id => $vyne_transaction_id,
                $status => ucwords(str_replace('_', ' ',$payment->getVyneStatus())),
                $amount => $this->_vyneHelper->formatCurrency($payment->getAmountOrdered()),
                $captured_amount => $captured ?: '0.00',
                $refunded_amount => $refunded ?: '0.00'
            );
        //}

        return $transport->addData($data);
    }
}

