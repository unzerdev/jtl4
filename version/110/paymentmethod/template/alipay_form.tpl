{include file="{$hpPayment.pluginPath}paymentmethod/template/_includes.tpl"}

<div class="redirecting-note alert alert-info">{$hpPayment.redirectingNote}</div>

<script>
$(document).ready(function() {
    var HpPayment = new window.HpPayment('{$hpPayment.config.publicKey}', window.HpPayment.PAYMENT_TYPES.ALIPAY, {
        submitButton: $('{if $hpPayment.config.selectorSubmitButton}{$hpPayment.config.selectorSubmitButton}{else}#form_payment_extra .submit{/if}').get(0),
        locale: '{$hpPayment.locale}',
        autoSubmit: true
    });
});
</script>

{include file="{$hpPayment.pluginPath}paymentmethod/template/_footer.tpl"}