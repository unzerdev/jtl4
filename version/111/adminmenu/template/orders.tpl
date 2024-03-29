{include file="{$hpAdmin.adminTemplatePath}partials/_includes.tpl"}
{include file="{$hpAdmin.adminTemplatePath}partials/_header.tpl"}


<div class="hp-admin-content">
    <div class="row">
        <div class="col-12 col-xs-12">
            <div class="alert alert-info">Der Bezahl-Status in der Tabelle enth�lt nur einen zwischengespeicherten Stand. Um den aktuellen Bezahl-Status zu erfahren, rufen Sie die Detail-Seite der Bestellung auf.</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xs-12">
            <h3>Bestell&uuml;bersicht</h3>
        </div>
        <div class="col-11 col-xs-11 col-sm-6">
            <div class="hp-search">
                <form class="form" onsubmit="window.hpOrderManagement.search($(this).find('[name=searchValue]').val());return false;">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" autocomplete="off" placeholder="Bestellnummer oder Payment ID" name="searchValue"/>
                        <div class="input-group-append input-group-btn">
                            <button class="btn btn-success" type="submit">Suchen</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-1 col-xs-1 col-sm-2">
            <div class="hp-on-ajax-loading">
                <div class="text-center">
                    <i class="fa fas fa-spinner fa-pulse"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-xs-12 col-sm-4">
            <div class="hp-pagination">
                <div class="btn-group">
                    <button type="button" class="btn btn-default" onclick="window.hpOrderManagement.firstPage();">
                        <i class="fa fas fa-fast-backward" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-default" onclick="window.hpOrderManagement.prevPage();">
                        <i class="fa fas fa-backward" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-default disabled hp-current-page-indicator" disabled="disabled">1</button>
                    <button type="button" class="btn btn-default" onclick="window.hpOrderManagement.nextPage();">
                        <i class="fa fas fa-forward" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-12 col-xs-12">
            <hr>
        </div>

        <div class="col-12 col-xs-12">
            <div class="table-responsive">
                <table class="list table table-striped">
                    <thead>
                    <tr>
                        <th class="tleft">Bestellnummer</th>
                        <th class="tleft">JTL Status</th>
                        <th class="tleft">Unzer ID <em><small>(Payment Id)</small></em></th>
                        <th class="tleft">Bezahl-Status</th>
                        <th class="tleft">Bezahlmethode</th>
                        <th class="tright">Betrag</th>
                        <th class="tright">Bestelldatum</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="hp-orders">
                        <tr class="hp-on-ajax-loading">
                            <td class="text-center" colspan="8">
                                <i class="fa fas fa-2x fa-spinner fa-pulse"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="hp-order-detail-modal" data-backdrop="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Details zur Bestellung <em>%orderId%</em></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fenster schlie�en</button>
            </div>
        </div>
    </div>
</div>
