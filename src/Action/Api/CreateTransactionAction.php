<?php

namespace PayumTW\EzShip\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpPostRedirect;
use PayumTW\EzShip\Request\Api\CreateTransaction;
use Payum\Core\Exception\RequestNotSupportedException;

class CreateTransactionAction extends BaseApiAwareAction
{
    /**
     * {@inheritdoc}
     *
     * @param $request CreateTransaction
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $params = $this->api->createTransaction((array) $details);

        if (isset($params['status']) === true) {
            $details->replace($params);

            return;
        }

        throw new HttpPostRedirect(
            $this->api->getApiEndpoint(empty($details['orderAmount']) === true ? 'cvs' : 'capture'),
            $this->api->createTransaction((array) $details)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof CreateTransaction &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
