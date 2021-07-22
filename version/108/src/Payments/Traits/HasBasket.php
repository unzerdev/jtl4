<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Payments\Traits;

use UnzerSDK\Constants\BasketItemTypes;
use UnzerSDK\Resources\Basket;
use UnzerSDK\Resources\EmbeddedResources\BasketItem;
use Sprache;
use Warenkorb;
use WarenkorbPos;

/**
 * Payment Methods which require a Basket object.
 *
 * @see https://docs.heidelpay.com/docs/additional-resources#basket-resource
 * @package Plugin\s360_heidelpay_shop4\Payments\Traits
 */
trait HasBasket
{
    /**
     * Create a Heidelpay Basket instance.
     *
     * @param Warenkorb $cart
     * @param object $currency
     * @param Sprache $lang
     * @param string $orderId
     * @return Basket
     */
    protected function createHeidelpayBasket(Warenkorb $cart, $currency, Sprache $lang, string $orderId = ''): Basket
    {

        $basket = (new Basket($orderId))
            ->setAmountTotalGross($cart->gibGesamtsummeWaren(true, false))
            ->setCurrencyCode($currency->cISO ?? '');

        $cumulatedDelta    = 0;
        $cumulatedDeltaNet = 0;
        $amountTotalDiscount = 0;

        foreach ($cart->PositionenArr as $position) {
            $basketItem = $this->createHeidelpayBasketItem(
                $position,
                $lang,
                $cumulatedDelta,
                $cumulatedDeltaNet,
                $amountTotalDiscount
            );
            $basket->addBasketItem($basketItem);
        }

        $basket->setAmountTotalDiscount($amountTotalDiscount);

        // AmountTotalGross should be the total amount without any coupouns, discounts applied
        // but gibGesamtsummeWaren() does not consider coupon/voucher positions, so have have to add them here ...
        if ($basket->getAmountTotalDiscount()) {
            $basket->setAmountTotalGross($basket->getAmountTotalGross() + $basket->getAmountTotalDiscount());
        }

        return $basket;
    }

    /**
     * Create a Heidelpay BasketItem instance.
     *
     * @param Warenkorb $cart
     * @param Sprache $lang
     * @param float $cumulatedDelta    Rounding Error Delta, @see Warenkorb::useSummationRounding
     * @param float $cumulatedDeltaNet Rounding Error Delta, @see Warenkorb::useSummationRounding
     * @param float $amountTotalDiscount
     * @return BasketItem
     */
    protected function createHeidelpayBasketItem(
        WarenkorbPos $position,
        Sprache $lang,
        float &$cumulatedDelta,
        float &$cumulatedDeltaNet,
        float &$amountTotalDiscount
    ): BasketItem {
        $title = $position->cName;
        if (\is_array($title)) {
            $title = $title[$lang->getIso()];
        }

        // !NOTE: JTL distributes its rounding errors of the total basket sum to the cart positions,
        // ! so we have to do the same ...
        $grossAmount        = berechneBrutto(
            $position->fPreis * $position->nAnzahl,
            gibUst($position->kSteuerklasse),
            12
        );
        $netAmount          = $position->fPreis * $position->nAnzahl;
        $roundedGrossAmount = berechneBrutto(
            $position->fPreis * $position->nAnzahl + $cumulatedDelta,
            gibUst($position->kSteuerklasse),
            2
        );
        $roundedNetAmount   = \round($position->fPreis * $position->nAnzahl + $cumulatedDeltaNet, 2);
        $cumulatedDelta    += ($grossAmount - $roundedGrossAmount);
        $cumulatedDeltaNet += ($netAmount - $roundedNetAmount);

        // Set Basket Item
        $basketItem = new BasketItem(
            utf8_encode($title),
            $roundedNetAmount,
            $position->fPreis,
            (int) $position->nAnzahl
        );

        $basketItem->setAmountGross($roundedGrossAmount);

        if ($this->isPromotionLineItemType((string) $position->nPosTyp)) {
            $basketItem->setAmountGross(0);
            $basketItem->setAmountNet(0);
            $basketItem->setAmountPerUnit(0);
            $basketItem->setAmountDiscount($roundedGrossAmount * -1);

            $amountTotalDiscount += $basketItem->getAmountDiscount();
        }

        $basketItem->setVat((float) gibUst($position->kSteuerklasse));
        $basketItem->setType($this->getBasketLineItemType((string) $position->nPosTyp));
        $basketItem->setBasketItemReferenceId($this->generateBasketItemReferenceId($position->cArtNr, $title));

        return $basketItem;
    }

    /**
     * Get the basket item type for a line item.
     *
     * @param string $type
     * @return string|null
     */
    private function getBasketLineItemType(string $type): ?string
    {
        switch ($type) {
            // Goods (includes digital, as jtl does not differ between those)
            case C_WARENKORBPOS_TYP_ARTIKEL:
            case C_WARENKORBPOS_TYP_GRATISGESCHENK:
                return BasketItemTypes::GOODS;

            // Vouchers and coupons
            case C_WARENKORBPOS_TYP_GUTSCHEIN:
            case C_WARENKORBPOS_TYP_KUPON:
            case C_WARENKORBPOS_TYP_NEUKUNDENKUPON:
                return BasketItemTypes::VOUCHER;

            // different type of shipping fees
            case C_WARENKORBPOS_TYP_VERPACKUNG:
            case C_WARENKORBPOS_TYP_VERSANDPOS:
            case C_WARENKORBPOS_TYP_VERSANDZUSCHLAG:
            case C_WARENKORBPOS_TYP_VERSAND_ARTIKELABHAENGIG:
                return BasketItemTypes::SHIPMENT;
        }

        return null;
    }

    /**
     * Checks if the type is a voucher basket item type.
     *
     * @param string $type
     * @return boolean
     */
    private function isPromotionLineItemType(string $type): bool
    {
        return $this->getBasketLineItemType($type) === BasketItemTypes::VOUCHER;
    }

    /**
     * Generate basket item refernce id
     *
     * @param string|null $productNumber
     * @param string $title
     * @return string
     */
    private function generateBasketItemReferenceId(?string $productNumber, string $title): string
    {
        $productNumber = utf8_encode($productNumber);

        if (empty($productNumber)) {
            $productNumber = $title . '-' . time();
        }

        // Unzer API does not like spaces or other special chars in the ref id
        return preg_replace('/[^a-z0-9\-]/im', '', str_replace(' ', '-', $productNumber));
    }
}
