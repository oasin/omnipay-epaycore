<?php

namespace Omnipay\EpayCore\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;

class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * @return array|mixed
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function getData()
    {
        $theirHash = (string)$this->httpRequest->request->get('V2_HASH');
        $ourHash = $this->createResponseHash($this->httpRequest->request->all());

        if ($theirHash !== $ourHash) {
            throw new InvalidResponseException("Callback hash does not match expected value");
        }

        return $this->httpRequest->request->all();
    }

    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }

    /**
     * @param $parameters
     * @return string
     * @throws InvalidRequestException
     */
    public function createResponseHash($parameters)
    {
        $this->validate('passphrase');

        return hash('sha256', implode(':', [
            $parameters['epc_merchant_id'],
            $parameters['epc_order_id'],
            $parameters['epc_created_at'],
            $parameters['epc_amount'],
            $parameters['epc_currency_code'],
            $parameters['epc_dst_account'],
            $parameters['epc_src_account'],
            $parameters['epc_batch'],
            $this->getPassphrase(),
        ]));

    }
}
