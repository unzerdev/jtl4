<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/**
 * This class defines integration tests to verify interface and functionality of the payment method Invoice Secured.
 *
 * Copyright (C) 2020 - today Unzer E-Com GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @link  https://docs.unzer.com/
 *
 * @author  Simon Gabriel <development@unzer.com>
 *
 * @package  UnzerSDK\test\integration\PaymentTypes
 */
namespace UnzerSDK\test\integration\PaymentTypes;

use UnzerSDK\Constants\ApiResponseCodes;
use UnzerSDK\Constants\CancelReasonCodes;
use UnzerSDK\Exceptions\UnzerApiException;
use UnzerSDK\Resources\PaymentTypes\InvoiceSecured;
use UnzerSDK\Resources\TransactionTypes\Charge;
use UnzerSDK\test\BaseIntegrationTest;

class InvoiceSecuredTest extends BaseIntegrationTest
{
    /**
     * Verifies Invoice Secured payment type can be created.
     *
     * @test
     *
     * @return InvoiceSecured
     */
    public function invoiceSecuredTypeShouldBeCreatableAndFetchable(): InvoiceSecured
    {
        /** @var InvoiceSecured $invoice */
        $invoice = $this->unzer->createPaymentType(new InvoiceSecured());
        $this->assertInstanceOf(InvoiceSecured::class, $invoice);
        $this->assertNotNull($invoice->getId());

        $fetchedInvoice = $this->unzer->fetchPaymentType($invoice->getId());
        $this->assertInstanceOf(InvoiceSecured::class, $fetchedInvoice);
        $this->assertEquals($invoice->getId(), $fetchedInvoice->getId());

        return $invoice;
    }
    
    /**
     * Verify, backwards compatibility regarding fetching payment type and map it to invoice secured class.
     *
     * @test
     */
    public function ivgTypeShouldBeFechable(): InvoiceSecured
    {
        $ivgMock = $this->getMockBuilder(InvoiceSecured::class)->setMethods(['getUri'])->getMock();
        $ivgMock->method('getUri')->willReturn('/types/invoice-guaranteed');

        /** @var InvoiceSecured $ivgType */
        $ivgType = $this->unzer->createPaymentType($ivgMock);
        $this->assertInstanceOf(InvoiceSecured::class, $ivgType);
        $this->assertRegExp('/^s-ivg-[.]*/', $ivgType->getId());

        $fetchedType = $this->unzer->fetchPaymentType($ivgType->getId());
        $this->assertInstanceOf(InvoiceSecured::class, $fetchedType);
        $this->assertRegExp('/^s-ivg-[.]*/', $fetchedType->getId());

        return $fetchedType;
    }

    /**
     * Verify fetched ivg type can be charged
     *
     * @test
     * @depends ivgTypeShouldBeFechable
     *
     * @param InvoiceSecured $ivgType fetched ivg type.
     *
     * @throws UnzerApiException
     */
    public function ivgTypeShouldBeChargable(InvoiceSecured $ivgType)
    {
        $customer = $this->getMaximumCustomer();
        $charge = $ivgType->charge(100.00, 'EUR', 'https://unzer.com', $customer);

        $this->assertNotNull($charge);
        $this->assertNotEmpty($charge->getId());
        $this->assertTrue($charge->isPending());

        return $charge;
    }

    /**
     * Verify fetched ivg type can be shipped.
     *
     * @test
     * @depends ivgTypeShouldBeChargable
     */
    public function ivgTypeShouldBeShippable(Charge $ivgCharge)
    {
        $invoiceId = 'i' . self::generateRandomId();

        $ship = $this->unzer->ship($ivgCharge->getPayment(), $invoiceId);
        // expect Payment to be pending after shipment.
        $this->assertTrue($ship->getPayment()->isPending());
        $this->assertNotNull($ship);
    }

    /**
     * Verify Invoice Secured is not authorizable.
     *
     * @test
     *
     * @param InvoiceSecured $invoice
     * @depends invoiceSecuredTypeShouldBeCreatableAndFetchable
     */
    public function verifyInvoiceIsNotAuthorizable(InvoiceSecured $invoice): void
    {
        $this->expectException(UnzerApiException::class);
        $this->expectExceptionCode(ApiResponseCodes::API_ERROR_TRANSACTION_AUTHORIZE_NOT_ALLOWED);

        $this->unzer->authorize(1.0, 'EUR', $invoice, self::RETURN_URL);
    }

    /**
     * Verify Invoice Secured needs a customer object
     *
     * @test
     * @depends invoiceSecuredTypeShouldBeCreatableAndFetchable
     *
     * @param InvoiceSecured $invoiceSecured
     */
    public function invoiceSecuredShouldRequiresCustomer(InvoiceSecured $invoiceSecured): void
    {
        $this->expectException(UnzerApiException::class);
        $this->expectExceptionCode(ApiResponseCodes::API_ERROR_FACTORING_REQUIRES_CUSTOMER);
        $this->unzer->charge(1.0, 'EUR', $invoiceSecured, self::RETURN_URL);
    }

    /**
     * Verify Invoice Secured is chargeable.
     *
     * @test
     * @depends invoiceSecuredTypeShouldBeCreatableAndFetchable
     *
     * @param InvoiceSecured $invoiceSecured
     */
    public function invoiceSecuredRequiresBasket(InvoiceSecured $invoiceSecured): void
    {
        $customer = $this->getMaximumCustomer();
        $customer->setShippingAddress($customer->getBillingAddress());

        $this->expectException(UnzerApiException::class);
        $this->expectExceptionCode(ApiResponseCodes::API_ERROR_FACTORING_REQUIRES_BASKET);

        $invoiceSecured->charge(1.0, 'EUR', self::RETURN_URL, $customer);
    }

    /**
     * Verify Invoice Secured is chargeable.
     *
     * @test
     * @depends invoiceSecuredTypeShouldBeCreatableAndFetchable
     *
     * @param InvoiceSecured $invoiceSecured
     *
     * @return Charge
     */
    public function invoiceSecuredShouldBeChargeable(InvoiceSecured $invoiceSecured): Charge
    {
        $customer = $this->getMaximumCustomer();
        $customer->setShippingAddress($customer->getBillingAddress());

        $basket = $this->createBasket();
        $charge = $invoiceSecured->charge(119.0, 'EUR', self::RETURN_URL, $customer, $basket->getOrderId(), null, $basket);
        $this->assertNotNull($charge);
        $this->assertNotEmpty($charge->getId());
        $this->assertNotEmpty($charge->getIban());
        $this->assertNotEmpty($charge->getBic());
        $this->assertNotEmpty($charge->getHolder());
        $this->assertNotEmpty($charge->getDescriptor());

        return $charge;
    }

    /**
     * Verify Invoice Secured is not shippable on Unzer object.
     *
     * @test
     */
    public function verifyInvoiceSecuredIsNotShippableWoInvoiceIdOnUnzerObject(): void
    {
        // create payment type
        /** @var InvoiceSecured $invoiceSecured */
        $invoiceSecured = $this->unzer->createPaymentType(new InvoiceSecured());

        // perform charge
        $customer = $this->getMaximumCustomer();
        $customer->setShippingAddress($customer->getBillingAddress());

        $basket = $this->createBasket();
        $charge = $invoiceSecured->charge(119.0, 'EUR', self::RETURN_URL, $customer, $basket->getOrderId(), null, $basket);

        // perform shipment
        $payment = $charge->getPayment();
        $this->expectException(UnzerApiException::class);
        $this->expectExceptionCode(ApiResponseCodes::API_ERROR_SHIPPING_REQUIRES_INVOICE_ID);
        $this->unzer->ship($payment);
    }

    /**
     * Verify Invoice Secured is not shippable on payment object.
     *
     * @test
     */
    public function verifyInvoiceSecuredIsNotShippableWoInvoiceIdOnPaymentObject(): void
    {
        // create payment type
        /** @var InvoiceSecured $invoiceSecured */
        $invoiceSecured = $this->unzer->createPaymentType(new InvoiceSecured());

        // perform charge
        $customer = $this->getMaximumCustomer();
        $customer->setShippingAddress($customer->getBillingAddress());

        $basket = $this->createBasket();
        $charge = $invoiceSecured->charge(119.0, 'EUR', self::RETURN_URL, $customer, $basket->getOrderId(), null, $basket);

        // perform shipment
        $payment = $charge->getPayment();
        $this->expectException(UnzerApiException::class);
        $this->expectExceptionCode(ApiResponseCodes::API_ERROR_SHIPPING_REQUIRES_INVOICE_ID);
        $payment->ship();
    }

    /**
     * Verify Invoice Secured shipment with invoice id on Unzer object.
     *
     * @test
     */
    public function verifyInvoiceSecuredShipmentWithInvoiceIdOnUnzerObject(): void
    {
        // create payment type
        /** @var InvoiceSecured $invoiceSecured */
        $invoiceSecured = $this->unzer->createPaymentType(new InvoiceSecured());

        // perform charge
        $customer = $this->getMaximumCustomer();
        $customer->setShippingAddress($customer->getBillingAddress());

        $basket = $this->createBasket();
        $charge = $invoiceSecured->charge(119.0, 'EUR', self::RETURN_URL, $customer, $basket->getOrderId(), null, $basket);

        // perform shipment
        $payment   = $charge->getPayment();
        $invoiceId = 'i' . self::generateRandomId();
        $shipment  = $this->unzer->ship($payment, $invoiceId);
        // expect Payment to be completed after shipment.
        $this->assertTrue($shipment->getPayment()->isCompleted());
        $this->assertNotNull($shipment->getId());
        $this->assertEquals($invoiceId, $shipment->getInvoiceId());
    }

    /**
     * Verify Invoice Secured shipment with invoice id on payment object.
     *
     * @test
     */
    public function verifyInvoiceSecuredShipmentWithInvoiceIdOnPaymentObject(): void
    {
        // create payment type
        /** @var InvoiceSecured $invoiceSecured */
        $invoiceSecured = $this->unzer->createPaymentType(new InvoiceSecured());

        // perform charge
        $customer = $this->getMaximumCustomer();
        $customer->setShippingAddress($customer->getBillingAddress());

        $basket = $this->createBasket();
        $charge = $invoiceSecured->charge(119.0, 'EUR', self::RETURN_URL, $customer, $basket->getOrderId(), null, $basket);

        $payment   = $charge->getPayment();
        $invoiceId = 'i' . self::generateRandomId();
        $shipment  = $payment->ship($invoiceId);
        $this->assertNotNull($shipment->getId());
        $this->assertEquals($invoiceId, $shipment->getInvoiceId());
    }

    /**
     * Verify Invoice Secured shipment with pre set invoice id
     *
     * @test
     */
    public function verifyInvoiceSecuredShipmentWithPreSetInvoiceId(): void
    {
        /** @var InvoiceSecured $invoiceSecured */
        $invoiceSecured = $this->unzer->createPaymentType(new InvoiceSecured());

        $customer = $this->getMaximumCustomer();
        $customer->setShippingAddress($customer->getBillingAddress());

        $basket = $this->createBasket();
        $invoiceId = 'i' . self::generateRandomId();
        $charge = $invoiceSecured->charge(119.0, 'EUR', self::RETURN_URL, $customer, $basket->getOrderId(), null, $basket, null, $invoiceId);

        $payment   = $charge->getPayment();
        $shipment  = $this->unzer->ship($payment);
        $this->assertNotNull($shipment->getId());
        $this->assertEquals($invoiceId, $shipment->getInvoiceId());
    }

    /**
     * Verify Invoice Secured charge can canceled.
     *
     * @test
     *
     * @param Charge $charge
     * @depends invoiceSecuredShouldBeChargeable
     */
    public function verifyInvoiceChargeCanBeCanceled(Charge $charge): Charge
    {
        $cancellation = $charge->cancel(100, CancelReasonCodes::REASON_CODE_CANCEL);
        $this->assertNotNull($cancellation);
        $this->assertNotNull($cancellation->getId());
        return $charge;
    }

    /**
     * Verify Invoice Secured charge cancel throws exception if the amount is missing.
     *
     * @test
     *
     * @param Charge $charge
     * @depends verifyInvoiceChargeCanBeCanceled
     */
    public function verifyInvoiceChargeCanBeCancelledWoAmount(Charge $charge): void
    {
        $cancellation = $charge->cancel(null, CancelReasonCodes::REASON_CODE_CANCEL);

        $this->assertNotNull($cancellation);
        $this->assertNotNull($cancellation->getId());
        $this->assertEquals(19.0, $cancellation->getAmount());
    }

    /**
     * Verify Invoice Secured charge cancel throws exception if the reason is missing.
     *
     * @test
     *
     * @param Charge $charge
     * @depends invoiceSecuredShouldBeChargeable
     */
    public function verifyInvoiceChargeCanNotBeCancelledWoReasonCode(Charge $charge): void
    {
        $this->expectException(UnzerApiException::class);
        $this->expectExceptionCode(ApiResponseCodes::API_ERROR_CANCEL_REASON_CODE_IS_MISSING);
        $charge->cancel(100.0);
    }
}
