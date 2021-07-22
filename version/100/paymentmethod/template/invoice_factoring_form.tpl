{include file="{$hpPayment.pluginPath}paymentmethod/template/_includes.tpl"}

<div class="unzerUI form" novalidate>
    <div id="customer" class="field"></div>
</div>

<script>
$(document).ready(function() {
    var HpPayment = new window.HpPayment('{$hpPayment.config.publicKey}', window.HpPayment.PAYMENT_TYPES.INVOICE_FACTORING, {
        submitButton: $('{if $hpPayment.config.selectorSubmitButton}{$hpPayment.config.selectorSubmitButton}{else}#form_payment_extra .submit{/if}').get(0),
        locale: '{$hpPayment.locale}',
        {if empty($hpPayment.customerId) || $hpPayment.customerId == -1}
            customerId: {if !empty($hpPayment.customer->getId())}'{$hpPayment.customer->getId()}'{else}null{/if},
        {else}
            customerId: '{$hpPayment.customerId}',
        {/if}
        customer: {$hpPayment.customer->jsonSerialize()},
    });
});
</script>

{include file="{$hpPayment.pluginPath}paymentmethod/template/_footer.tpl"}