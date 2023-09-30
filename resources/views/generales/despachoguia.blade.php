<div class="modal fade" id="myModalguiadesp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Guia despacho</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="nfila" id="nfila">
                    <input type="hidden" name="status" id="status">
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="idg" class="control-label">OD</label>
                        <input type="text" name="idg" id="idg" class="form-control" required placeholder="ID" disabled readonly/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-10" classorig="form-group col-xs-12 col-sm-10">
                        <label for="guiadespachom" class="control-label">Guia despacho</label>
                        <input type="text" name="guiadespachom" id="guiadespachom" class="form-control requeridos numerico" tipoval="texto" required placeholder="Guia despacho"/>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnGuardarG" name="btnGuardarG" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        
    </div>
</div>