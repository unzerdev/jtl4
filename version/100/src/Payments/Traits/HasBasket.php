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
            ->setAmountTotalGross($cart->gibGesamtsummeWaren(true))
            ->setCurrencyCode($currency->cISO ?? '');

        $cumulatedDelta    = 0;
        $cumulatedDeltaNet = 0;
        foreach ($cart->PositionenArr as $position) {
            $basketItem = $this->createHeidelpayBasketItem($position, $lang, $cumulatedDelta, $cumulatedDeltaNet);
            $basket->addBasketItem($basketItem);
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
     * @return BasketItem
     */
    protected function createHeidelpayBasketItem(
        WarenkorbPos $position,
        Sprache $lang,
        float &$cumulatedDelta,
        float &$cumulatedDeltaNet
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

        $basketItem = new BasketItem(
            utf8_encode($title),
            $roundedNetAmount,
            $position->fPreis,
            (int) $position->nAnzahl
        );

        $basketItem->setAmountGross($roundedGrossAmount);
        $basketItem->setVat((float) gibUst($position->kSteuerklasse));
        $basketItem->setAmountVat($basketItem->getAmountGross() - $basketItem->getAmountNet());
        $basketItem->setBasketItemReferenceId(utf8_encode($position->cArtNr));

        if (empty($basketItem->getBasketItemReferenceId())) {
            $basketItem->setBasketItemReferenceId($title . '-' . time());
        }

        // Unzer API does not like spaces in the ref id
        $basketItem->setBasketItemReferenceId(
            str_replace(' ', '-', $basketItem->getBasketItemReferenceId())
        );

        switch ($position->nPosTyp) {
            // Goods (includes digital, as jtl does not differ between those)
            case C_WARENKORBPOS_TYP_ARTIKEL:
            case C_WARENKORBPOS_TYP_GRATISGESCHENK:
                $basketItem->setType(BasketItemTypes::GOODS);
                break;

            // Vouchers and coupons
            case C_WARENKORBPOS_TYP_GUTSCHEIN:
            case C_WARENKORBPOS_TYP_KUPON:
            case C_WARENKORBPOS_TYP_NEUKUNDENKUPON:
                $basketItem->setType(BasketItemTypes::VOUCHER);
                break;

            // different type of shipping fees
            case C_WARENKORBPOS_TYP_VERPACKUNG:
            case C_WARENKORBPOS_TYP_VERSANDPOS:
            case C_WARENKORBPOS_TYP_VERSANDZUSCHLAG:
            case C_WARENKORBPOS_TYP_VERSAND_ARTIKELABHAENGIG:
                $basketItem->setType(BasketItemTypes::SHIPMENT);
        }

        return $basketItem;
    }
}
