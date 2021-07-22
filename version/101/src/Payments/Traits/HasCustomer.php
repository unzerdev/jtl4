<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Payments\Traits;

use Adresse;
use UnzerSDK\Resources\Customer;
use UnzerSDK\Resources\CustomerFactory;
use UnzerSDK\Resources\EmbeddedResources\Address;
use Kunde;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayApiAdapter;
use Plugin\s360_heidelpay_shop4\Utils\SessionHelper;

/**
 * Payment Methods which require a Customer object.
 *
 * @see https://docs.heidelpay.com/docs/additional-resources#create-customer-resources
 * @package Plugin\s360_heidelpay_shop4\Payments\Traits
 */
trait HasCustomer
{
    /**
     * Create a new customer resource or fetch one if we have a id for it.
     *
     * @param HeidelpayApiAdapter $adapter
     * @param SessionHelper $session
     * @param bool $isB2B
     * @return Customer
     */
    protected function createOrFetchHeidelpayCustomer(
        HeidelpayApiAdapter $adapter,
        SessionHelper $session,
        bool $isB2B
    ): Customer {
        if ($session->has(SessionHelper::KEY_CUSTOMER_ID) && $session->get(SessionHelper::KEY_CUSTOMER_ID) != -1) {
            $customer = $adapter->getApi()->fetchCustomer($session->get(SessionHelper::KEY_CUSTOMER_ID));

            // Update names as they might have changed
            $customer->setFirstname(utf8_encode($session->getFrontendSession()->Customer()->cVorname));
            $customer->setLastname(utf8_encode($session->getFrontendSession()->Customer()->cNachname));

            return $customer;
        }

        if ($isB2B) {
            return $adapter->getApi()->createOrUpdateCustomer(
                $this->createHeidelpayB2BCustomer($session->getFrontendSession()->Customer())
            );
        }

        return $adapter->getApi()->createOrUpdateCustomer(
            $this->createHeidelpayCustomer($session->getFrontendSession()->Customer())
        );
    }

    /**
     * Create a Heidelpay Customer Instance.
     *
     * @param Kunde $customer
     * @return Customer
     */
    protected function createHeidelpayCustomer(Kunde $customer): Customer
    {
        $customerObj = CustomerFactory::createCustomer(
            utf8_encode($customer->cVorname),
            utf8_encode($customer->cNachname)
        );

        // Set external customer so we do not have to map it ourself.
        $customerObj->setCustomerId($customer->kKunde);

        return $customerObj;
    }

    /**
     * Create a Heidelpay Address for Shipping
     *
     * @param \stdClass|Adresse $address
     * @return Address
     */
    protected function createHeidelpayAddress($address): Address
    {
        return (new Address())
            ->setName(utf8_encode($address->cVorname . ' ' . $address->cNachname))
            ->setStreet(utf8_encode($address->cStrasse . ' ' . $address->cHausnummer))
            ->setZip(utf8_encode($address->cPLZ))
            ->setCity(utf8_encode($address->cOrt))
            ->setCountry(utf8_encode($address->cLand));
    }

    /**
     * Create a Heidelpay B2B Customer (registered or non-registered) instance.
     *
     * @param Kunde $customer
     * @return Customer
     */
    protected function createHeidelpayB2BCustomer(Kunde $customer): Customer
    {
        $address  = (new Address())
            ->setName(utf8_encode($customer->cVorname . ' ' . $customer->cNachname))
            ->setStreet(utf8_encode($customer->cStrasse . ' ' . $customer->cHausnummer))
            ->setZip(utf8_encode($customer->cPLZ))
            ->setCity(utf8_encode($customer->cOrt))
            ->setCountry(utf8_encode($customer->cLand));

        // Registered = registered in the commercial register with a commercial register number
        if ($customer->cUSTID) {
            $obj = CustomerFactory::createRegisteredB2bCustomer(
                $address,
                utf8_encode($customer->cUSTID),
                utf8_encode($customer->cFirma)
            );

            $obj->setCustomerId($customer->kKunde);

            return $obj;
        }

        $obj = CustomerFactory::createNotRegisteredB2bCustomer(
            utf8_encode($customer->cVorname),
            utf8_encode($customer->cNachname),
            date('Y-m-d', strtotime($customer->dGeburtstag)),
            $address,
            utf8_encode($customer->cMail),
            utf8_encode($customer->cFirma)
        );
        $obj->setCustomerId($customer->kKunde);

        return $obj;
    }
}
