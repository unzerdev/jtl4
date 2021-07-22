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
            $customer->setFirstname(
                html_entity_decode(
                    utf8_encode($session->getFrontendSession()->Customer()->cVorname),
                    ENT_COMPAT,
                    'UTF-8'
                )
            );
            $customer->setLastname(
                html_entity_decode(
                    utf8_encode($session->getFrontendSession()->Customer()->cNachname),
                    ENT_COMPAT,
                    'UTF-8'
                )
            );

            return $customer;
        }

        // Create new customer object but do not save the customer in the api
        // because some mandatory fields (e.g. birthday) may be missing!
        if ($isB2B) {
            return $this->createHeidelpayB2BCustomer($session->getFrontendSession()->Customer());
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
            html_entity_decode(utf8_encode($customer->cVorname), ENT_COMPAT, 'UTF-8'),
            html_entity_decode(utf8_encode($customer->cNachname), ENT_COMPAT, 'UTF-8')
        );

        $customerObj->setEmail($customer->cMail);

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
            ->setName(
                html_entity_decode(
                    utf8_encode($address->cVorname . ' ' . $address->cNachname),
                    ENT_COMPAT,
                    'UTF-8'
                )
            )
            ->setStreet(
                html_entity_decode(
                    utf8_encode($address->cStrasse . ' ' . $address->cHausnummer),
                    ENT_COMPAT,
                    'UTF-8'
                )
            )
            ->setZip(utf8_encode($address->cPLZ))
            ->setCity(html_entity_decode(utf8_encode($address->cOrt), ENT_COMPAT, 'UTF-8'))
            ->setCountry(html_entity_decode(utf8_encode($address->cLand), ENT_COMPAT, 'UTF-8'));
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
            ->setName(
                html_entity_decode(
                    utf8_encode($customer->cVorname . ' ' . $customer->cNachname),
                    ENT_COMPAT,
                    'UTF-8'
                )
            )
            ->setStreet(
                html_entity_decode(
                    utf8_encode($customer->cStrasse . ' ' . $customer->cHausnummer),
                    ENT_COMPAT,
                    'UTF-8'
                )
            )
            ->setZip(utf8_encode($customer->cPLZ))
            ->setCity(html_entity_decode(utf8_encode($customer->cOrt), ENT_COMPAT, 'UTF-8'))
            ->setCountry(html_entity_decode(utf8_encode($customer->cLand), ENT_COMPAT, 'UTF-8'));

        // Registered = registered in the commercial register with a commercial register number
        if ($customer->cUSTID) {
            $obj = CustomerFactory::createRegisteredB2bCustomer(
                $address,
                utf8_encode($customer->cUSTID),
                html_entity_decode(utf8_encode($customer->cFirma), ENT_COMPAT, 'UTF-8')
            );

            $obj->setFirstname(html_entity_decode(utf8_encode($customer->cVorname), ENT_COMPAT, 'UTF-8'));
            $obj->setLastname(html_entity_decode(utf8_encode($customer->cNachname), ENT_COMPAT, 'UTF-8'));
            $obj->setEmail($customer->cMail);
            $obj->setSalutation($customer->cAnrede == 'm' ? 'mr' : 'mrs');
            $obj->setCustomerId($customer->kKunde);

            return $obj;
        }

        $birthday = date('Y-m-d', strtotime($customer->dGeburtstag));
        if (empty($customer->dGeburtstag) || $customer->dGeburtstag === '0000-00-00') {
            $birthday = '';
        }

        $obj = CustomerFactory::createNotRegisteredB2bCustomer(
            html_entity_decode(utf8_encode($customer->cVorname), ENT_COMPAT, 'UTF-8'),
            html_entity_decode(utf8_encode($customer->cNachname), ENT_COMPAT, 'UTF-8'),
            $birthday,
            $address,
            utf8_encode($customer->cMail),
            html_entity_decode(utf8_encode($customer->cFirma), ENT_COMPAT, 'UTF-8')
        );
        $obj->setSalutation($customer->cAnrede == 'm' ? 'mr' : 'mrs');
        $obj->setCustomerId($customer->kKunde);

        return $obj;
    }
}
