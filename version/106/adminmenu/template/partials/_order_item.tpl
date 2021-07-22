{strip}
    <tr class="hp-order-item {$bgClass}" data-shop-order-id="{$hpOrder->getId()}">
        <td class="hp-order-table-column hp-shop-order-number">
            {if $hpOrder->getJtlOrderNumber()}{$hpOrder->getJtlOrderNumber()}{elseif $hpOrder->getId()}{$hpOrder->getId()}{else} - {/if}
        </td>
        <td class="hp-order-table-column hp-shop-order-status">
            {if isset($hpOrder->getOrder()->cStatus)}
                {if $hpOrder->getOrder()->cStatus === "-1"}
                    Storniert
                {elseif $hpOrder->getOrder()->cStatus === "1"}
                    Offen
                {elseif $hpOrder->getOrder()->cStatus === "2"}
                    In Bearbeitung
                {elseif $hpOrder->getOrder()->cStatus === "3"}
                    Bezahlt
                {elseif $hpOrder->getOrder()->cStatus === "4"}
                    Versandt
                {elseif $hpOrder->getOrder()->cStatus === "5"}
                    Teilversandt
                {else}
                    {$hpOrder->getOrder()->cStatus}
                {/if}
            {else}
                -
            {/if}
        </td>
        <td class="hp-order-table-column hp-payment-id">{if $hpOrder->getPaymentId()}{$hpOrder->getPaymentId()}{else} - {/if}</dt>
        <td class="hp-order-table-column hp-order-status hp-status-{if $hpOrder->getPaymentState()}{$hpOrder->getPaymentState()|mb_strtolower|escape}{else}unknown{/if}">
            {if $hpOrder->getPaymentState()}
               {if $hpOrder->getPaymentState() === \UnzerSDK\Constants\PaymentState::STATE_NAME_PENDING}
                    Ausstehend
                {elseif $hpOrder->getPaymentState() === \UnzerSDK\Constants\PaymentState::STATE_NAME_COMPLETED}
                    Abgeschlossen
                {elseif $hpOrder->getPaymentState() === \UnzerSDK\Constants\PaymentState::STATE_NAME_CANCELED}
                    Storniert
                {elseif $hpOrder->getPaymentState() === \UnzerSDK\Constants\PaymentState::STATE_NAME_PARTLY}
                    Teilweise Bezahlt/Abgeschlossen
                {elseif $hpOrder->getPaymentState() === \UnzerSDK\Constants\PaymentState::STATE_NAME_PAYMENT_REVIEW}
                    Zalungsüberprüfung
                {elseif $hpOrder->getPaymentState() === \UnzerSDK\Constants\PaymentState::STATE_NAME_CHARGEBACK}
                    Rückbuchung
                {else}
                    {$hpOrder->getPaymentState()}
                {/if}
            {else}
                -
            {/if}
        </td>
        <td class="hp-order-table-column hp-payment-type">
            {if $hpOrder->getPaymentTypeName()}
                {if $hpOrder->getPaymentTypeName() == 'card'}
                    Kreditkarte
                {else if $hpOrder->getPaymentTypeName() == 'sepa-direct-debit'}
                    SEPA Lastschrift
                {else if $hpOrder->getPaymentTypeName() == 'sepa-direct-debit-guaranteed'}
                    SEPA Lastschrift (gesichert)
                {else if $hpOrder->getPaymentTypeName() == 'sepa-direct-debit-secured'}
                    SEPA Lastschrift (gesichert)
                {else if $hpOrder->getPaymentTypeName() == 'invoice'}
                    Rechnung
                {else if $hpOrder->getPaymentTypeName() == 'invoice-guaranteed'}
                    Rechnung (gesichert)
                {else if $hpOrder->getPaymentTypeName() == 'invoice-secured'}
                    Rechnung (gesichert)
                {else if $hpOrder->getPaymentTypeName() == 'invoice-factoring'}
                    Rechnung (Fakturierung)
                {else if $hpOrder->getPaymentTypeName() == 'paypal'}
                    PayPal
                {else if $hpOrder->getPaymentTypeName() == 'sofort'}
                    SOFORT
                {else if $hpOrder->getPaymentTypeName() == 'giropay'}
                    Giropay
                {else if $hpOrder->getPaymentTypeName() == 'prepayment'}
                    Vorkasse
                {else if $hpOrder->getPaymentTypeName() == 'eps'}
                    EPS
                {else if $hpOrder->getPaymentTypeName() == 'pis'}
                    FlexiPay Direct
                {else if $hpOrder->getPaymentTypeName() == 'alipay'}
                    Alipay
                {else if $hpOrder->getPaymentTypeName() == 'wechatpay'}
                    WeChat Pay
                {else if $hpOrder->getPaymentTypeName() == 'ideal'}
                    iDEAL
                {else if $hpOrder->getPaymentTypeName() == 'hire-purchase-direct-debit'}
                    Ratenzahlung
                {else if $hpOrder->getPaymentTypeName() == 'installment-secured'}
                    Ratenzahlung
                {else}
                    {$hpOrder->getPaymentTypeName() }
                {/if}
            {/if}

            {if $hpOrder->getPaymentTypeId()}
                <em>({$hpOrder->getPaymentTypeId()})</em>
            {/if}
        </td>
        <td class="hp-order-table-column hp-amount tright">{gibPreisStringLocalized($hpOrder->getOrder()->fGesamtsumme)}</td>
        <td class="hp-order-table-column hp-date tright">{if isset($hpOrder->getOrder()->dErstellt)}{$hpOrder->getOrder()->dErstellt|strtotime|date_format:"d.m.Y H:i:s"}{else} - {/if}</td>
        <td class="hp-order-table-column hp-order-actions">
            <div class="input-group">
                <div class="btn-group input-group-btn">
                    <button type="button" class="btn btn-xs btn-default" title="Details ansehen" onclick="window.hpOrderManagement.getDetails('{$hpOrder->getId()}');"><i class="fa fas fa-pen fa-pencil" aria-hidden="true"></i></button>
                    {if $hpPortalUrl}
                        <a class="btn btn-xs btn-primary" title="Bestellung im hp-Portal anzeigen" href="{$hpPortalUrl}" target="_blank"><i class="fa fas fa-external-link" aria-hidden="true"></i></a>
                    {/if}
                </div>
            </div>
        </td>
    </tr>
{/strip}