{include file="{$hpAdmin.adminTemplatePath}partials/_header.tpl"}

<div class="hp-admin-content">
    <div class="row">
        <div class="col-xs-12 col-12">
            <form action="{$hpSettings.formAction}" method="post">
                {$jtl_token}

                {* API Auth *}
                <div class="panel panel-default card mb-3">
                    <div class="panel-heading card-title">
                        <h3 class="panel-title">API Zugangsdaten</h3>
                    </div>

                    <div class="panel-body card-text">
                        {* Private Key *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-privateKey">Privater Schlüssel (Private Key)</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control" name="privateKey" id="hpSettings-privateKey" placeholder="s-priv-xxxxxxxxxx" value="{if isset($hpSettings.config.privateKey)}{$hpSettings.config.privateKey}{/if}" />
                                <small class="form-text help-block text-muted">Eingabe des von Unzer zur Verfügung gestellten Private Key.</small>
                            </div>
                        </div>

                        {* Public Key *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-publicKey">Öffentlicher Schlüssel (Public Key)</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control" name="publicKey" id="hpSettings-publicKey" placeholder="s-pub-xxxxxxxxxx" value="{if isset($hpSettings.config.publicKey)}{$hpSettings.config.publicKey}{/if}" />
                                <small class="form-text help-block text-muted">Eingabe des von Unzer zur Verfügung gestellten Public Key.</small>
                            </div>
                        </div>

                        {* hIP / Insight Merchant ID *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-merchantId">Händler ID für das Insight Portal <small>(Optional)</small></label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col9">
                                <input type="text" class="form-control" name="merchantId" id="hpSettings-merchantId" value="{if isset($hpSettings.config.merchantId)}{$hpSettings.config.merchantId}{/if}" />
                                <small class="form-text help-block text-muted">Die Händler ID (Merchant ID) des Insight Portal wird benötigt, um in den Bestelldetails einen Link zur jeweiligen Transaktion im Insight Portal bereitzustellen.</small>
                            </div>
                        </div>
                    </div>
                </div>

                {* Styles *}
                <div class="panel panel-default card mb-3">
                    <div class="panel-heading card-title">
                        <h3 class="panel-title">Styles</h3>
                    </div>

                    <div class="panel-body card-text">
                        {* Font Size *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-fontSize">Schriftgröße</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control" name="fontSize" id="hpSettings-fontSize" placeholder="z.B. 16px oder 1.125rem" value="{if isset($hpSettings.config.fontSize)}{$hpSettings.config.fontSize}{/if}" />
                            </div>
                        </div>

                        {* Font Color *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-fontColor">Schriftfarbe</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control form-colored" name="fontColor" id="hpSettings-fontColor" placeholder="z.B. red oder #ff0000" value="{if isset($hpSettings.config.fontColor)}{$hpSettings.config.fontColor}{/if}" />
                            </div>
                        </div>

                        {* Font Family *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-fontFamily">Schriftart</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control" name="fontFamily" id="hpSettings-fontFamily" placeholder="z.B. Arial, Helvetica, sans-serif" value="{if isset($hpSettings.config.fontFamily)}{$hpSettings.config.fontFamily}{/if}" />
                            </div>
                        </div>
                    </div>
                </div>

                {* Advanced Settings *}
                <div class="panel panel-default card mb-3">
                    <div class="panel-heading card-title">
                        <h3 class="panel-title">Experten-Einstellungen</h3>
                    </div>

                    <div class="panel-body card-text">
                        {* PQ Selector Submit Button *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-selectorSubmitButton">Selektor für den Submit-Button im Zahlungszwischenschritt</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control" name="selectorSubmitButton" id="hpSettings-selectorSubmitButton" placeholder="#form_payment_extra .submit, .cta-checkout-continue" value="{if isset($hpSettings.config.selectorSubmitButton)}{$hpSettings.config.selectorSubmitButton}{else}#form_payment_extra .submit, .cta-checkout-continue{/if}" />
                                <small class="form-text help-block text-muted">Der Selektor für den Submit-Button im Zahlungszwischenschritt.</small>
                            </div>
                        </div>

                        {* PQ Selector Change Payment Method *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-pqSelectorChangePaymentMethod">PQ-Selector für "Zahlungsart ändern" Button im Zwischenschritt</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control" name="pqSelectorChangePaymentMethod" id="hpSettings-pqSelectorChangePaymentMethod" placeholder="#order-additional-payment" value="{if isset($hpSettings.config.pqSelectorChangePaymentMethod)}{$hpSettings.config.pqSelectorChangePaymentMethod}{else}#order-additional-payment{/if}" />
                                <small class="form-text help-block text-muted">Der PHP-Query-Selektor für das Einhängen des "Zahlungsart ändern"-Buttons auf der Zahlungszwischenschritt-Seite.</small>
                            </div>
                        </div>

                        {* PQ Method Change Payment Method *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-pqMethodChangePaymentMethod">PQ-Einfügemethode für "Zahlungsart ändern" Button im Zwischenschritt</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <select class="form-control" name="pqMethodChangePaymentMethod" id="hpSettings-pqMethodChangePaymentMethod">
                                    <option value="append" {if isset($hpSettings.config.pqMethodChangePaymentMethod) && $hpSettings.config.pqMethodChangePaymentMethod == 'append'}selected{/if}>Anhängen (Append)</option>
                                    <option value="prepend" {if isset($hpSettings.config.pqMethodChangePaymentMethod) && $hpSettings.config.pqMethodChangePaymentMethod == 'prepend'}selected{/if}>Vorhängen (Prepend)</option>
                                    <option value="before" {if isset($hpSettings.config.pqMethodChangePaymentMethod) && $hpSettings.config.pqMethodChangePaymentMethod == 'before'}selected{/if}>Vorstellen (Before)</option>
                                    <option value="after" {if isset($hpSettings.config.pqMethodChangePaymentMethod) && $hpSettings.config.pqMethodChangePaymentMethod == 'after'}selected{/if}>Anstellen (After)</option>
                                    <option value="replaceWith" {if isset($hpSettings.config.pqMethodChangePaymentMethod) && $hpSettings.config.pqMethodChangePaymentMethod == 'replaceWith'}selected{/if}>Ersetzen (Replace)</option>
                                </select>
                                <small class="form-text help-block text-muted">Die PHP-Query-Einfügemethode für das Einhängen des "Zahlungsart ändern"-Buttons auf der Zahlungszwischenschritt-Seite.</small>
                            </div>
                        </div>

                        {* PQ Selector Errors *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-pqSelectorErrors">PQ-Selector für Fehlermeldungen</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control" name="pqSelectorErrors" id="hpSettings-pqSelectorErrors" placeholder="#result-wrapper, .basket_wrapper, .order-completed, #account" value="{if isset($hpSettings.config.pqSelectorErrors)}{$hpSettings.config.pqSelectorErrors}{else}#result-wrapper, .basket_wrapper, .order-completed, #account{/if}" />
                                <small class="form-text help-block text-muted">Der PHP-Query-Selektor für das Einhängen von Plugin-Fehlermeldungen auf der Versandart/Zahlungsart-Auswahl.</small>
                            </div>
                        </div>

                        {* PQ Method Errors *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-pqMethodErrors">PQ-Einfügemethode für Fehlermeldungen</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <select class="form-control" name="pqMethodErrors" id="hpSettings-pqMethodErrors">
                                    <option value="prepend" {if isset($hpSettings.config.pqMethodErrors) && $hpSettings.config.pqMethodErrors == 'prepend'}selected{/if}>Vorhängen (Prepend)</option>
                                    <option value="append" {if isset($hpSettings.config.pqMethodErrors) && $hpSettings.config.pqMethodErrors == 'append'}selected{/if}>Anhängen (Append)</option>
                                    <option value="before" {if isset($hpSettings.config.pqMethodErrors) && $hpSettings.config.pqMethodErrors == 'before'}selected{/if}>Vorstellen (Before)</option>
                                    <option value="after" {if isset($hpSettings.config.pqMethodErrors) && $hpSettings.config.pqMethodErrors == 'after'}selected{/if}>Anstellen (After)</option>
                                    <option value="replaceWith" {if isset($hpSettings.config.pqMethodErrors) && $hpSettings.config.pqMethodErrors == 'replaceWith'}selected{/if}>Ersetzen (Replace)</option>
                                </select>
                                <small class="form-text help-block text-muted">Die PHP-Query-Einfügemethode für das Einhängen von Plugin-Fehlermeldungen auf der Versandart/Zahlungsart-Auswahl.</small>
                            </div>
                        </div>

                        {* PQ Selector ReviewStep *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-pqSelectorReviewStep">PQ-Selector für Zahlungsart-Extras auf der "Bestellung überprüfen"-Seite</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <input type="text" class="form-control" name="pqSelectorReviewStep" id="hpSettings-pqSelectorReviewStep" placeholder="#order-confirm" value="{if isset($hpSettings.config.pqSelectorReviewStep)}{$hpSettings.config.pqSelectorReviewStep}{else}#order-confirm{/if}" />
                                <small class="form-text help-block text-muted">Der PHP-Query-Selektor für das Einhängen von Zahlungsart-Extras auf der "Bestellung überprüfen"-Seite</small>
                            </div>
                        </div>

                        {* PQ Method ReviewStep *}
                        <div class="hp-admin-option row mb-2">
                            <div class="hp-admin-option__title col-xs-3 col-3">
                                <label for="hpSettings-pqMethodReviewStep">PQ-Einfügemethode für "Bestellung überprüfen"-Seite<</label>
                            </div>
                            <div class="hp-admin-option__input col-xs-9 col-9">
                                <select class="form-control" name="pqMethodReviewStep" id="hpSettings-pqMethodReviewStep">
                                    <option value="prepend" {if isset($hpSettings.config.pqMethodReviewStep) && $hpSettings.config.pqMethodReviewStep == 'prepend'}selected{/if}>Vorhängen (Prepend)</option>
                                    <option value="append" {if isset($hpSettings.config.pqMethodReviewStep) && $hpSettings.config.pqMethodReviewStep == 'append'}selected{/if}>Anhängen (Append)</option>
                                    <option value="before" {if isset($hpSettings.config.pqMethodReviewStep) && $hpSettings.config.pqMethodReviewStep == 'before'}selected{/if}>Vorstellen (Before)</option>
                                    <option value="after" {if isset($hpSettings.config.pqMethodReviewStep) && $hpSettings.config.pqMethodReviewStep == 'after'}selected{/if}>Anstellen (After)</option>
                                    <option value="replaceWith" {if isset($hpSettings.config.pqMethodReviewStep) && $hpSettings.config.pqMethodReviewStep == 'replaceWith'}selected{/if}>Ersetzen (Replace)</option>
                                </select>
                                <small class="form-text help-block text-muted">Die PHP-Query-Einfügemethode für das Einhängen von Zahlungsart-Extras auf der "Bestellung überprüfen"-Seite</small>
                            </div>
                        </div>
                    </div>
                </div>

                {* Save Button *}
                <div class="panel panel-default card mb-3">
                    <div class="card-body">
                        <div class="panel-body card-text">
                            <div class="hp-admin-option row mb-2">
                                <div class="hp-admin-option__title col-xs-12 col-12">
                                    <button class="btn btn-primary" type="submit" name="saveSettings" value="1"><i class="fa fa-save"></i>&nbsp; Speichern</button>

                                    {if isset($hpSettings.webhooks) && $hpSettings.webhooks}
                                        <button class="btn btn-info pull-right float-right" type="submit" name="registerWebhooks" value="1"><i class="fa fa-refresh"></i>&nbsp; Webhooks neu registrieren</button>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>