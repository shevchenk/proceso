<!-- /.modal -->
<div class="modal fade" id="personaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="error" name="error" action="" method="post">
        </form>
        <form id="form_personas_modal" name="form_personas_modal" action="" method="post">
          <fieldset>
            <legend>Datos personales</legend>
            <div class="row form-group">

              <div class="col-sm-12">
                <div class="col-sm-4">
                  <label class="control-label">Nombre
                      <a id="error_nombre" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_nombre" id="txt_nombre">
                </div>
                <div class="col-sm-4">
                  <label class="control-label">Apellido Paterno
                      <a id="error_paterno" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Apellido Paterno">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Apellido Paterno" name="txt_paterno" id="txt_paterno">
                </div>
                <div class="col-sm-4">
                  <label class="control-label">Apellido Materno
                      <a id="error_materno" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Apellido Materno">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Apellido Materno" name="txt_materno" id="txt_materno">
                </div>
              </div>

              <div class="col-sm-12">
                <div class="col-sm-4">
                  <label class="control-label">Fecha de Nacimiento
                      <a id="error_fecha_nacimiento" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Fecha de Nacimiento">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="AAAA-MM-DD" id="txt_fecha_nacimiento" name="txt_fecha_nacimiento" onfocus="blur()"/>
                </div>
                <div class="col-sm-4">
                  <label class="control-label">DNI
                      <a id="error_dni" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese DNI">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese DNI" name="txt_dni" id="txt_dni">
                </div>
                <div class="col-sm-4">
                  <label class="control-label">Password
                      <a id="error_password" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Password">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="password" class="form-control" placeholder="Ingrese Password" name="txt_password" id="txt_password">
                </div>
              </div>
              <div class="col-sm-12">
                <div class="col-sm-4">
                  <label class="control-label">Email
                      <a id="error_email" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese email">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese email" name="txt_email" id="txt_email">
                </div>
                <!-- <div class="col-sm-4">
                  <label class="control-label">Sexo:
                  </label>
                  <select class="form-control" name="slct_sexo" id="slct_sexo">
                      <option value='' style="display:none">.:Seleccione:.</option>
                      <option value='F'>Femenino</option>
                      <option value='M' selected>Masculino</option>
                  </select>
                </div>-->
                <div class="col-sm-4">
                  <label class="control-label">Email corporativo
                      <a id="error_email" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese email">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Email corporativo" name="txt_email_mdi" id="txt_email_mdi">
                </div>
                <div class="col-sm-4">
                  <label class="control-label">Estado:
                  </label>
                  <select class="form-control" name="slct_estado" id="slct_estado">
                      <option value='0'>Inactivo</option>
                      <option value='1' selected>Activo</option>
                  </select>
                </div>
                <div class="col-sm-2" style="display:none">
                  <label class="control-label" style="color: blue;">Vista Documento:
                  </label>
                  <select class="form-control" name="slct_vista_doc" id="slct_vista_doc">                      
                      <option value='1'>Si</option>
                      <option value='0'>No</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="col-sm-3">
                  <label class="control-label">Teléfono</label>
                  <input type="text" class="form-control" placeholder="Ingrese Teléfono" name="txt_telefono" id="txt_telefono">
                </div>

                <div class="col-sm-3">
                  <label class="control-label">Celular</label>
                  <input type="text" class="form-control" placeholder="Ingrese Celular" name="txt_celular" id="txt_celular">
                </div>

                <div class="col-sm-6">
                  <label class="control-label">Dirección</label>
                  <input type="text" class="form-control" placeholder="Ingrese Dirección" name="txt_direccion" id="txt_direccion">
                </div>

              </div>
              <div class="col-sm-12">
                <div class="col-sm-2">
                  <label class="control-label">Género:
                  </label>
                  <select class="form-control" name="slct_sexo" id="slct_sexo">
                      <option value='' style="display:none">Seleccione</option>
                      <option value='F'>Femenino</option>
                      <option value='M' selected>Masculino</option>
                  </select>
                </div>
              </div>

            </div>
          </fieldset>
          <fieldset id="f_areas_cargo">
            <legend>Rol que desempeña en la institución</legend>

            <div class="row form-group">
                <div class="col-sm-12">
                  <div class="col-sm-2">
                    <label class="control-label">Modalidad:
                    </label>
                    <select class="form-control" name="slct_modalidad" id="slct_modalidad">
                        <!-- <option value='' style="display:none">.:Seleccione:.</option> -->
                        <option value='1' selected>Trabajador</option>
                        <option value='2'>Tercero</option>
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <label class="control-label">Es Responsable de área?:
                    </label>
                    <select class="form-control" name="slct_responsable_area" id="slct_responsable_area">
                        <!-- <option value='' style="display:none">.:Seleccione:.</option> -->
                        <option value='0' selected>No</option>
                        <option value='1'>Si</option>
                    </select>
                  </div>
                  <div class="col-sm-4">
                    <label class="control-label">Area:
                    </label>
                    <select class="form-control" name="slct_area" id="slct_area">
                    </select>
                    <select style="display:none" name="slct_area_aux" id="slct_area_aux">
                    </select>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="col-sm-4">
                    <label class="control-label">Rol:
                    </label>
                    <select class="form-control" name="slct_rol" id="slct_rol">
                    </select>
                  </div>

                  <div class="col-sm-4">
                    <label class="control-label">Lugar de procedencia:
                    </label>
                    <select class="form-control" name="slct_local[]" id="slct_local" multiple>
                    </select>
                  </div>

                  <div class="col-sm-2" style="display:none;">
                    <label class="control-label" style="color: red;">Documentos Privados:
                    </label>
                    <select class="form-control" name="slct_doc_privados" id="slct_doc_privados">                      
                        <option value='1'>Si</option>
                        <option value='0'>No</option>
                    </select>
                  </div>

                </div>
            </div>

          </fieldset>
          <fieldset id="f_areas_cargo">
            <legend>Rol que desempeña en el sistema</legend>

            <div class="row form-group">
              <div class="col-sm-12">
                <div class="col-sm-4">
                  <label class="control-label">Nivel de documento:
                  </label>
                  <select class="form-control" name="slct_nivel" id="slct_nivel">
                      <option value='1' selected>1</option>
                      <option value='2'>2</option>
                      <option value='3'>3</option>
                      <option value='4'>4</option>
                      <option value='5'>5</option>
                  </select>
                </div>

                <div class="col-sm-4">
                  <label class="control-label">Nivel de Proceso:
                  </label>
                  <select class="form-control" name="slct_nivel_proceso" id="slct_nivel_proceso">
                      <option value='1' selected>1</option>
                      <option value='2'>2</option>
                      <option value='3'>3</option>
                      <option value='4'>4</option>
                      <option value='5'>5</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="col-sm-6">
                  <label class="control-label">Roles:
                  </label>
                  <select class="form-control" name="slct_cargos" id="slct_cargos">
                  </select>
                </div>
                <div class="col-sm-6">
                    <br>
                    <button type="button" class="btn btn-success" Onclick="AgregarArea();">
                      <i class="fa fa-plus fa-sm"></i>
                      &nbsp;Nuevo
                    </button>
                </div>
              </div>
            </div>
            <ul class="list-group" id="t_cargoPersona"></ul>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal -->
