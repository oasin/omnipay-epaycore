<?php

namespace Omnipay\EpayCore\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class PurchaseRequest extends AbstractRequest
{
    /**
     * @return mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
        // Validate required parameters before return data
        $this->validate('merchant_id', 'passphrase', 'currency', 'amount');

        $data['epc_merchant_id'] = $this->getMerchantID();
        $data['epc_amount'] = $this->getAmount();
        $data['epc_currency_code'] = $this->getCurrency(); // USD, EUR or OAU
        $data['epc_order_id'] = $this->getTransactionId();
        $data['epc_status_url'] = $this->getNotifyUrl();
        $data['epc_success_url'] = $this->getReturnUrl();
        $data['epc_cancel_url'] = $this->getCancelUrl();
        $data['epc_descr'] = $this->getDescription();

        $sign = [
            $data['epc_merchant_id'],
            $data['epc_amount'],
            $data['epc_currency_code'],
            $data['epc_order_id'],
            $this->getPassphrase() // merchant password
        ];

        # get epc_sign hash
        $sign = hash('sha256', implode(':', $sign));


        $data['epc_sign'] = $sign;

        return $data;
    }

    public function sendData($data)
    {
        return new PurchaseResponse($this, $data, $this->getEndpoint());
    }
}
