{strip}
    <div class="hp-order-detail-wrapper">
        <div class="row">
            <div class="col-12 col-xs-12">
                <div class="input-group">
                    <div class="btn-group input-group-btn">
                        {if $hpPortalUrl}
                            <a class="btn btn-primary" title="Bestellung im hIP anzeigen" href="{$hpPortalUrl}" target="_blank">
                                Bestellung im Insight-Portal anzeigen
                            </a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6 col-xs-12">
                <div class="card panel panel-default">
                    <div class="card-header panel-heading">
                        <h4 class="panel-title">Lieferadresse</h4>
                    </div>
                    <div class="card-body panel-body">
                        {if $hpOrder->Lieferadresse}
                            {$hpOrder->Lieferadresse->cTitel} {$hpOrder->Lieferadresse->cVorname} {$hpOrder->Lieferadresse->cNachname}
                            {if !empty($hpOrder->Lieferadresse->cStrasse)}
                                <br/>{$hpOrder->Lieferadresse->cStrasse} {$hpOrder->Lieferadresse->cHausnummer}
                            {/if}
                            {if !empty($hpOrder->Lieferadresse->cAdressZusatz)}
                                <br/>{$hpOrder->Lieferadresse->cAdressZusatz}
                            {/if}
                            {if !empty($hpOrder->Lieferadresse->cPLZ) || !empty($hpOrder->Lieferadresse->cOrt)}
                                <br/>{$hpOrder->Lieferadresse->cPLZ} {$hpOrder->Lieferadresse->cOrt}
                            {/if}
                            {if !empty($hpOrder->Lieferadresse->cBundesland)}
                                <br/>{$hpOrder->Lieferadresse->cBundesland}
                            {/if}
                            {if !empty($hpOrder->Lieferadresse->cLand)}
                                <br/>{$hpOrder->Lieferadresse->cLand}<br/>
                            {/if}
                            {if !empty($hpOrder->Lieferadresse->cTel)}
                                <br/>Telefon: {$hpOrder->Lieferadresse->cTel}<br/>
                            {/if}
                            {if !empty($hpOrder->Lieferadresse->cMail)}
                                <br/>E-Mail Adresse: <a href="mailto: {$hpOrder->Lieferadresse->cMail}">{$hpOrder->Lieferadresse->cMail}</a>
                            {/if}
                        {elseif $hpOrder->oRechnungsadresse}
                            {$hpOrder->oRechnungsadresse->cTitel} {$hpOrder->oRechnungsadresse->cVorname} {$hpOrder->oRechnungsadresse->cNachname}
                            {if !empty($hpOrder->oRechnungsadresse->cStrasse)}
                                <br/>{$hpOrder->oRechnungsadresse->cStrasse} {$hpOrder->oRechnungsadresse->cHausnummer}
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cAdressZusatz)}
                                <br/>{$hpOrder->oRechnungsadresse->cAdressZusatz}
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cPLZ) || !empty($hpOrder->oRechnungsadresse->cOrt)}
                                <br/>{$hpOrder->oRechnungsadresse->cPLZ} {$hpOrder->oRechnungsadresse->cOrt}
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cBundesland)}
                                <br/>{$hpOrder->oRechnungsadresse->cBundesland}
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cLand)}
                                <br/>{$hpOrder->oRechnungsadresse->cLand}<br/>
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cTel)}
                                <br/>Telefon: {$hpOrder->oRechnungsadresse->cTel}<br/>
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cMail)}
                                <br/>E-Mail Adresse: <a href="mailto: {$hpOrder->oRechnungsadresse->cMail}">{$hpOrder->oRechnungsadresse->cMail}</a>
                            {/if}
                        {/if}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xs-12">
                <div class="card panel panel-default">
                    <div class="card-header panel-heading">
                        <h4 class="panel-title">Rechnungsadresse</h4>
                    </div>
                    <div class="card-body panel-body">
                        {if $hpOrder->oRechnungsadresse}
                            {$hpOrder->oRechnungsadresse->cTitel} {$hpOrder->oRechnungsadresse->cVorname} {$hpOrder->oRechnungsadresse->cNachname}
                            {if !empty($hpOrder->oRechnungsadresse->cStrasse)}
                                <br/>{$hpOrder->oRechnungsadresse->cStrasse} {$hpOrder->oRechnungsadresse->cHausnummer}
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cAdressZusatz)}
                                <br/>{$hpOrder->oRechnungsadresse->cAdressZusatz}
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cPLZ) || !empty($hpOrder->oRechnungsadresse->cOrt)}
                                <br/>{$hpOrder->oRechnungsadresse->cPLZ} {$hpOrder->oRechnungsadresse->cOrt}
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cBundesland)}
                                <br/>{$hpOrder->oRechnungsadresse->cBundesland}
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cLand)}
                                <br/>{$hpOrder->oRechnungsadresse->cLand}<br/>
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cTel)}
                                <br/>Telefon: {$hpOrder->oRechnungsadresse->cTel}<br/>
                            {/if}
                            {if !empty($hpOrder->oRechnungsadresse->cMail)}
                                <br/>E-Mail Adresse: <a href="mailto: {$hpOrder->oRechnungsadresse->cMail}">{$hpOrder->oRechnungsadresse->cMail}</a>
                            {/if}
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xs-12">
                <div class="card panel panel-default">
                    <div class="card-header panel-heading">
                        <h4 class="panel-title">Statusinformationen</h4>
                    </div>
                    <div class="card-body panel-body">
                        <div class="row">
                            <div class="col-12 col-xs-12">
                                <dl class="dl-horizontal row">
                                    <dt class="col-5">Status</dt>
                                    <dd class="col-7">
                                        {if $hpPayment->getState() === \UnzerSDK\Constants\PaymentState::STATE_PENDING}
                                            Ausstehend
                                        {elseif $hpPayment->getState() === \UnzerSDK\Constants\PaymentState::STATE_COMPLETED}
                                            Abgeschlossen
                                        {elseif $hpPayment->getState() === \UnzerSDK\Constants\PaymentState::STATE_CANCELED}
                                            Storniert
                                        {elseif $hpPayment->getState() === \UnzerSDK\Constants\PaymentState::STATE_PARTLY}
                                            Teilweise Bezahlt/Abgeschlossen
                                        {elseif $hpPayment->getState() === \UnzerSDK\Constants\PaymentState::STATE_PAYMENT_REVIEW}
                                            Zalungsüberprüfung
                                        {elseif $hpPayment->getState() === \UnzerSDK\Constants\PaymentState::STATE_CHARGEBACK}
                                            Rückbuchung
                                        {else}
                                            {\UnzerSDK\Constants\PaymentState::mapStateCodeToName($hpPayment->getState())}
                                        {/if}
                                    </dd>

                                    <dt class="col-5">Rechnungsnummer</dt>
                                    <dd class="col-7">{$hpPayment->getInvoiceId()}</dd>

                                    <dt class="col-5">Zahlungs-ID</dt>
                                    <dd class="col-7">{$hpPayment->getId()}</dd>

                                    <dt class="col-5">Zahlungsart</dt>
                                    <dd class="col-7">
                                        {if $hpPayment->getPaymentType()->getResourceName() == 'card'}
                                            Kreditkarte
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'sepa-direct-debit'}
                                            SEPA Lastschrift
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'sepa-direct-debit-guaranteed'}
                                            SEPA Lastschrift (guaranteed)
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'sepa-direct-debit-secured'}
                                            SEPA Lastschrift (gesichert)
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'invoice'}
                                            Rechnung
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'invoice-guaranteed'}
                                            Rechnung (guaranteed)
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'invoice-secured'}
                                            Rechnung (gesichert)
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'invoice-factoring'}
                                            Rechnung (Fakturierung)
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'paypal'}
                                            PayPal
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'sofort'}
                                            SOFORT
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'giropay'}
                                            Giropay
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'prepayment'}
                                            Vorkasse
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'eps'}
                                            EPS
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'pis'}
                                            FlexiPay Direct
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'alipay'}
                                            Alipay
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'wechatpay'}
                                            WeChat Pay
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'ideal'}
                                            iDEAL
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'hire-purchase-direct-debit'}
                                            Ratenzahlung
                                        {else if $hpPayment->getPaymentType()->getResourceName() == 'installment-secured'}
                                            Ratenzahlung
                                        {else}
                                            {$hpPayment->getPaymentType()->getResourceName()}
                                        {/if}

                                        <em>({$hpPayment->getPaymentType()->getId()})</em>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xs-12">
                <div class="card panel panel-default">
                    <div class="card-header panel-heading">
                        <h4 class="panel-title">Transaktionen</h4>
                    </div>

                    {if !empty($hpPayment->getCharges())}
                    {* <div class="table-responsive"> *}
                        <table class="list table table-striped">
                            <thead>
                            <tr>
                                <th class="tleft">ID</th>
                                <th class="tleft">Kurz-ID</th>
                                <th class="tleft">Status</th>
                                <th class="tleft">Betrag</th>
                            </tr>
                            </thead>
                            <tbody>
                                {foreach from=$hpPayment->getCharges() item='charge' name='charges'}
                                    <tr {if !$smarty.foreach.charges.first} style="margin-top:10px;"{/if} class="{if $charge->isError()}danger{elseif $charge->isPending()}warning{else}success{/if}">
                                        <td>{$charge->getId()}</td>
                                        <td class="hp-short-id">{$charge->getShortId()}</td>
                                        <td class="hp-status">
                                            {if $charge->isPending()}
                                                Ausstehend
                                            {elseif $charge->isError()}
                                                Fehlerhaft
                                            {elseif $charge->isSuccess()}
                                                Erfolgreich
                                            {else}
                                                -
                                            {/if}

                                            {if $charge->getMessage()}
                                                &nbsp; <i class="fa fas fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="{$charge->getMessage()->getMerchant()}"></i>
                                            {/if}
                                        </div>
                                        <td class="hp-amount">{if isset($charge->getAmount())}{($charge->getAmount())|number_format:2}{else} - {/if}{if isset($charge->getCurrency())} {$charge->getCurrency()}{/if}</td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    {* </div> *}
                    {else}
                        <div class="card-body panel-body text-center"><em>- keine -</em></div>
                    {/if}
                </div>
            </div>

            {if !empty($hpPayment->getShipments())}
                <div class="col-12 col-xs-12">
                    <div class="card panel panel-default">
                        <div class="card-header panel-heading">
                            <h4 class="panel-title">Versandmeldungen</h4>
                        </div>

                        {* <div class="table-responsive"> *}
                            <table class="list table table-striped">
                                <thead>
                                <tr>
                                    <th class="tleft">ID</th>
                                    <th class="tleft">Kurz-ID</th>
                                    <th class="tleft">Status</th>
                                    <th class="tleft">Rechnungsnummer</th>
                                </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$hpPayment->getShipments() item='shipment' name='shipments'}
                                        <tr {if !$smarty.foreach.shipments.first} style="margin-top:10px;"{/if} class="{if $shipment->isError()}danger{elseif $shipment->isPending()}warning{else}success{/if}">
                                            <td>{$shipment->getId()}</td>
                                            <td class="hp-short-id">{$shipment->getShortId()}</td>
                                            <td class="hp-status">
                                                {if $shipment->isPending()}
                                                    Ausstehend
                                                {elseif $shipment->isError()}
                                                    Fehlerhaft
                                                {elseif $shipment->isSuccess()}
                                                    Erfolgreich
                                                {else}
                                                    -
                                                {/if}

                                                {if $shipment->getMessage()}
                                                    &nbsp; <i class="fa fas fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="{$shipment->getMessage()->getMerchant()}"></i>
                                                {/if}
                                            </div>
                                            <td class="hp-invoice-id">{if isset($shipment->getInvoiceId())}{$shipment->getInvoiceId()}{else} - {/if}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        {* </div> *}
                </div>
            {/if}

            {if !empty($hpPayment->getCancellations())}
                <div class="col-12 col-xs-12">
                    <div class="card panel panel-default">
                        <div class="card-header panel-heading">
                            <h4 class="panel-title">Stornierungen</h4>
                        </div>

                        {* <div class="table-responsive"> *}
                            <table class="list table table-striped">
                                <thead>
                                <tr>
                                    <th class="tleft">ID</th>
                                    <th class="tleft">Kurz-ID</th>
                                    <th class="tleft">Status</th>
                                    <th class="tleft">Referenz</th>
                                    <th class="tleft">Betrag</th>
                                </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$hpPayment->getCancellations() item='cancelation' name='cancelations'}
                                        <tr {if !$smarty.foreach.cancelations.first} style="margin-top:10px;"{/if} class="{if $cancelation->isError()}danger{elseif $cancelation->isPending()}warning{else}success{/if}">
                                            <td>{$cancelation->getId()}</td>
                                            <td class="hp-short-id">{$cancelation->getShortId()}</td>
                                            <td class="hp-status">
                                                {if $cancelation->isPending()}
                                                    Ausstehend
                                                {elseif $cancelation->isError()}
                                                    Fehlerhaft
                                                {elseif $cancelation->isSuccess()}
                                                    Erfolgreich
                                                {else}
                                                    -
                                                {/if}

                                                {if $cancelation->getMessage()}
                                                    &nbsp; <i class="fa fas fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="{$cancelation->getMessage()->getMerchant()}"></i>
                                                {/if}
                                            </div>
                                            <td class="hp-payment-ref">{if isset($cancelation->getPaymentReference())}{$cancelation->getPaymentReference()}{else} - {/if}</td>
                                            <td class="hp-invoice-id">{if isset($cancelation->getAmount())}{($cancelation->getAmount())|number_format:2}{else} - {/if}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        {* </div> *}
                </div>
            {/if}
        </div>
    </div>
{/strip}