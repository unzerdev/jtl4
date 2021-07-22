{* This file must only be included ONCE in the admin *}
<script type="text/javascript">
    window.hpAdminAjaxUrl = '{$hpAdmin.adminUrl}&isAjax=1';
    window._HP_SNIPPETS_ = {
        'error': 'Fehler',
        'empty_search_result': '<b>Suche erfolglos:</b> Keine Bestellung für "%searchTerm%" gefunden.'
    };
</script>
<link rel="stylesheet" href="{$hpAdmin.adminMenuUrl}css/admin.css?v={$hpAdmin.pluginVersion}" type="text/css">
<script src="{$hpAdmin.adminMenuUrl}js/admin.js?v={$hpAdmin.pluginVersion}" defer="defer"></script>
<script src="{$hpAdmin.adminMenuUrl}js/ordermanagement.js?v={$hpAdmin.pluginVersion}" defer="defer"></script>
