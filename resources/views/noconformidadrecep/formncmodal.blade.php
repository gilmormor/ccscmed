<div class="modal fade" id="myModalDatos" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"  data-focus-on="input:first">
    <div class="modal-dialog modal-dialog-centered modal-lg" id="mdialTamanio" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="titulomodal" name="titulomodal">Datos</h3>
            </div>
            <div class="modal-body"  style="overflow-y: auto;">
                <input type="hidden" name="idhide" id="idhide" value="">
                <input type="hidden" name="ihide" id="ihide" value="">

                

                <section class="content">
                    <!-- row -->
                    <div class="row">
                      <div class="col-md-12">
                        <!-- The time line -->
                        <ul class="timeline">
                            <li class="time-label">
                                <span class="bg-red" id="fechanc" name="fechanc">
                                </span>
                            </li>
                            <!-- /.timeline-label -->
                            <!-- timeline item -->
                            <li>
                                <i class="fa fa-envelope bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time" id="horanc" name="horanc"></span>
                                    <h3 class="timeline-header" id="motivonc_id" name="motivonc_id"></h3>
                                    <h3 class="timeline-header" id="puntonormativo" name="puntonormativo"></h3>
                                    <h3 class="timeline-header" id="formadeteccionnc" name="formadeteccionnc"></h3>
                                    <h3 class="timeline-header" id="hallazgo" name="hallazgo"></h3>
                                    <h3 class="timeline-header" id="jefaturas" name="jefaturas"></h3>
                                    <h3 class="timeline-header" id="certificados" name="certificados"></h3>
                                    <h3 class="timeline-header" id="puntonorma" name="puntonorma"></h3>
                                    <h3 class="timeline-header" id="responsables" name="responsables"></h3>
                                </div>
                            </li>
                            <!-- END timeline item -->
                            <!-- timeline item -->



                            <!-- timeline time label -->
                            <li class="time-label">
                                    <span class="bg-aqua" id="fechaai" name="fechaai">
                                    </span>
                            </li>
                            <!-- /.timeline-label -->
                            <!-- timeline item -->
                            <li>
                                <i class="fa fa-edit bg-blue" id="circuloedidAI" name="circuloedidAI"></i>
                
                                <div class="timeline-item">
                                <span class="time" id="horaai" name="horaai"></span>
                
                                <h3 class="timeline-header" name="accioninmediatatxt" id="accioninmediatatxt"><a href="validarai()">Acción Inmediata</a></h3>
                
                                <div class="timeline-body" id="linebodyai1">
                                    <textarea name="accioninmediata" id="accioninmediata" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                                </div>
                                <div class="timeline-footer" id="linebodyai2">
                                    <a id="guardarAI" name="guardarAI" class="btn btn-primary btn-xs">Guardar</a>
                                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                                </div>
                                </div>
                            </li>
                            <!-- END timeline item -->

                            <!-- timeline time label -->
                            <li class="time-label obsvalai" style="display:none;">
                                <span class="bg-aqua" id="fechavalai" name="fechavalai">
                                </span>
                            </li>
                            <!-- /.timeline-label -->
                            <!-- timeline item -->
                            <li class="obsvalai" style="display:none;">
                                <i class="fa fa-edit bg-blue"></i>

                                <div class="timeline-item">
                                <span class="time" id="horavalai" name="horavalai"></span>

                                <h3 class="timeline-header" name="obsvalaitxt" id="obsvalaitxt"><a href="validarai()">Validación No conformidad</a></h3>

                                <div class="timeline-body" id="linebodyvalai1">
                                    <textarea name="obsvalai" id="obsvalai" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                                </div>
                                <div class="timeline-footer" id="linebodyvalai2">
                                    <a id="guardarvalAIAp" name="guardarvalAIAp" class="btn btn-primary btn-xs" onclick="apre(1)">Aprobar</a>
                                    <a id="guardarvalAIRe" name="guardarvalAIRe" class="btn btn-danger btn-xs" onclick="apre(0)">Rechazar</a>
                                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                                </div>
                                </div>
                            </li>
                            <!-- END timeline item -->


                            <!-- timeline item -->
                            <!-- timeline time label -->
                            <li class="time-label acausa" style="display:none;">
                                <span class="bg-green" id="fechaac" name="fechaac">
                                
                                </span>
                            </li>
                                <!-- /.timeline-label -->
                                <!-- timeline item -->
                            <li class="acausa" style="display:none;">
                                <i class="fa fa-edit bg-blue"></i>
                
                                <div class="timeline-item">
                                <span class="time" id="horaac" name="horaac"></span>
                
                                <h3 class="timeline-header" name="analisisdecausatxt" id="analisisdecausatxt"><a href="#">Análisis de causa</a></h3>
                
                                <div class="timeline-body" id="linebodyac1">
                                    <textarea name="analisisdecausa" id="analisisdecausa" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                                </div>
                                <div class="timeline-footer" id="linebodyac2">
                                    <a id="guardarACausa" name="guardarACausa" class="btn btn-primary btn-xs">Guardar</a>
                                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                                </div>
                                </div>
                            </li>
                            <!-- END timeline item -->
                            <!-- timeline item -->

                            <!-- timeline item -->
                            <!-- timeline time label -->
                            <li class="time-label acorrect" style="display:none;">
                                <span class="bg-purple" id="fechaacorr" name="fechaacorr">
                                
                                </span>
                            </li>
                                <!-- /.timeline-label -->
                                <!-- timeline item -->
                            <li class="acorrect" style="display:none;">
                                <i class="fa fa-edit bg-blue"></i>
                
                                <div class="timeline-item">
                                <span class="time" id="horaacorr" name="horaacorr"></span>
                
                                <h3 class="timeline-header" name="accorrectxt" id="accorrectxt"><a href="#">Acción Correctiva</a></h3>
                
                                <div class="timeline-body" id="linebodyacorr1">
                                    <textarea name="accorrec" id="accorrec" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                                </div>
                                <div class="timeline-footer" id="linebodyacorr2">
                                    <a id="guardarACorr" name="guardarACorr" class="btn btn-primary btn-xs">Guardar</a>
                                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                                </div>
                                </div>
                            </li>
                            <!-- END timeline item -->
                            <!-- timeline item -->


                            <!-- END timeline item -->
                            <!-- timeline time label -->
                            <!-- /.timeline-label -->
                            <!-- timeline item -->
                            <li class="acorrect" style="display:none;">
                                <i class="fa fa-camera bg-purple"></i>
                
                                <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>
                
                                <h3 class="timeline-header"><a href="#">Adjuntar Archivos <i class="glyphicon glyphicon-paperclip"></i></a></h3>
                
                                <div class="timeline-body">
                                    <!--
                                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                                    -->
                                    <DIV id="PANEL_0" class="panel panel-primary text-justify">
                                        <DIV class="panel-heading">
                                            <H3 class="panel-title">Envio de solicitud</H3>
                                        </DIV>
                                        <DIV class="panel-body">
                                            <FORM id="form-general" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                                                @csrf @method("put")
                                                <label for="file-ess" role="button">Seleccionar Archivos</label>
                                                <input id="file-ess" name="file-ess[]" type="file" multiple>
                                                <SMALL class="form-text text-muted">Seleccionar archivos de Office 201X: docx, xlsx, pptx y pdf hasta un máximo de 5.</SMALL>
                                            </form>
                                            <p>&nbsp;</p>
                                            <div class="alert alert-success" role="alert"></div>
                                        </DIV>
                                    </DIV>

                                </div>
                                </div>
                            </li>
                            <!-- END timeline item -->

                            <!-- timeline item -->
                            <!-- timeline time label -->
                            <li class="time-label fechacompromiso" style="display:none;">
                                <span class="bg-yellow" id="fechafechacompromiso" name="fechafechacompromiso">
                                
                                </span>
                            </li>
                                <!-- /.timeline-label -->
                                <!-- timeline item -->
                            <li class="fechacompromiso" style="display:none;">
                                <i class="fa fa-edit bg-aqua" id="circuloedidFC" name="circuloedidFC"></i>
                
                                <div class="timeline-item">
                                <span class="time" id="horafechacompromiso" name="horafechacompromiso"></span>
                
                                <h3 class="timeline-header" name="fechacompromisotxt" id="fechacompromisotxt"><a href="#">Fecha de compromiso</a></h3>
                
                                <div class="timeline-body" id="linebodyfeccomp1">
                                    <!--<textarea name="fechacompromiso" id="fechacompromiso" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>-->
                                    <input type="text" bsDaterangepicker class="form-control datepicker requeridos" name="fechacompromiso" id="fechacompromiso" placeholder="DD/MM/AAAA" readonly>
                                </div>
                                <div class="timeline-footer" id="linebodyfeccomp2">
                                    <a id="guardarfechacompromiso" name="guardarfechacompromiso" class="btn btn-primary btn-xs">Guardar</a>
                                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                                </div>
                                </div>
                            </li>
                            <!-- END timeline item -->
                            <!-- timeline item -->

                            <!-- timeline item -->
                            <li>
                                <i class="fa fa-clock-o bg-gray"></i>
                            </li>
                        </ul>
                      </div>
                      <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </section>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <!--<button type="button" id="guardarDatos1" name="guardarDatos1" class="btn btn-primary">Guardar</button>-->
            </div>
        </div>
        
    </div>
</div>


