<script type="text/javascript">
$(document).ready(function() {
    
});

expedienteUnico = function(rd_id){
    $("#expedienteModal").modal('show');
    if(rd_id){
        Expediente.ExpedienteUnico({'ruta_id':rd_id},HTMLExpedienteUnico);
    }else{
        alert('Error');
    }
}

function HTMLExpedienteUnico(data){
    if(data.length > 0){
        $("#tb_tretable").html('');// inicializando
        //#75DDEC, #FFF3A2, #F58DD7
        var cab = [
            { 'id':'ER', 'nombre': 'Expediente donde he Referido', 'color': 'alert-info', 'icon': '<a class="text-aqua" href="#"><i class="fa fa-square"></i></a>' },
            { 'id':'EA', 'nombre': 'Expediente Actual', 'color': 'alert-warning', 'icon': '<a class="text-orange" href="#"><i class="fa fa-square"></i></a>' },
            { 'id':'EER', 'nombre': 'Expediente donde estoy Referido', 'color': 'alert-success', 'icon': '<a class="text-green" href="#"><i class="fa fa-square"></i></a>' }
        ];
        //TODO: Expedientes///////////////////////////////////////////////////////////////////////
        for (let i = 0; i < data.length; i++) {
            var html ='';
            var cont = 0;
            var last_ref = 0;
            var parent = 0; var child = 0; var aux_ref = '';
            var ruta_id = 0;
            var dd = '';
            var dd2 = '';
            var clase = '';
            if( data[i].length > 0 ){
                html="<tr data-id='"+cab[i].id+cont+"' style='cursor: zoom-in;'>";
                html+=    "<td class='col-md-12' data-column=name><i class='glyphicon glyphicon-chevron-right'></i>"+cab[i].nombre+cab[i].icon+"</td>";
                html+="</tr>";
                $("#tb_tretable").append(html);

                $.each(data[i],function(index, el) {
                    cont+=1;
                    parent = 0;child = 2;
                    clase = 'col-md-12';

                    referido = (el.referido !=null) ? el.referido : '';
                    fhora = (el.fecha_hora !=null) ? el.fecha_hora : '';
                    proc =(el.proceso !=null) ? el.proceso : '';
                    area =(el.area !=null) ? el.area : '';
                    nord =(el.norden !=null) ? el.norden : '';

                    let img = ''; let archivo = '';

                    if( $.trim(el.archivo)!='' ){
                        $.each(el.archivo.split("|"),function(index, varchivo){
                            if( $.trim(varchivo)!='' && varchivo.substr(-3)=='pdf' ){
                                img= 'img/archivo/pdf.jpg';
                            }
                            else if( $.trim(varchivo)!='' && (varchivo.substr(-4)=='docx' || varchivo.substr(-3)=='doc') ){
                                img= 'img/archivo/word.png';
                            }
                            else if( $.trim(varchivo)!='' && (varchivo.substr(-4)=='xlsx' || varchivo.substr(-3)=='xls' || varchivo.substr(-3)=='csv') ){
                                img= 'img/archivo/excel.jpg';
                            }
                            else if( $.trim(varchivo)!='' && (varchivo.substr(-4)=='pptx' || varchivo.substr(-3)=='ppt') ){
                                img= 'img/archivo/ppt.png';
                            }
                            else if( $.trim(varchivo)!='' && varchivo.substr(-3)=='txt' ){
                                img= 'img/archivo/txt.jpg';
                            }
                            else{
                                img= varchivo;
                            }
                            archivo +=  "<a href='"+ varchivo +"' target='_blank'>"+
                                            "<img src='"+ img +"' alt='' class='img-responsive foto_desmonte' width='60' height='50' border='0'>"+
                                        "</a>";
                        });
                    }

                    if(el.doc_digital_id!=null){
                        referido += '<a class="btn btn-default btn-sm" href="doc_digital/'+el.doc_digital_id+'" target="_blank" data-titulo="Previsualizar"><i class="fa fa-eye fa-lg"></i> </a>';
                    }

                    if( el.ruta_id != ruta_id ){
                        cont = 1;
                        ruta_id = el.ruta_id;
                        //dd = 'style="background-color: '+cab[i].color+'"'; //rgba(255, 227, 34, 0.42);
                        dd = 'class="'+cab[i].color+'"'
                        dd2 =  '&nbsp;&nbsp;';
                        html="<tr data-id='"+cab[i].id+el.ruta_id+"-"+cont+"' "+dd+" data-parent='"+cab[i].id+parent+"' data-level="+child+">";
                        html+=    "<td class='col-md-12' data-column=name>"+dd2+"<i class=''></i><i class='fa fa-angle-right'></i>"+referido+"</td>";
                        html+=    "<td>"+fhora+"</td>";
                        html+=    "<td>"+proc+"</td>";
                        html+=    "<td>"+archivo+"</td>";
                        html+=    "<td>"+area+"</td>";
                        html+=    "<td>"+nord+"</td>";
                        html+="</tr>";
                    }
                    else{
                        parent = 1;
                        dd2 =  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class=""></i><i class="fa fa-angle-double-right"></i>';
                        dd = '';

                        if( cont == 2){ //Solo para colocar la flecha en caso tenga detalle!!!
                            $("#tb_tretable tr[data-id='"+cab[i].id+el.ruta_id+"-1']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().attr('style',"cursor: zoom-in;");
                        }

                        if(el.tipo=='r'){
                            last_ref = cont;
                            aux_ref = referido;
                        }
                        else if(el.tipo == 's'){
                            $("#tb_tretable tr[data-id='"+cab[i].id+el.ruta_id+"-"+last_ref+"']").find("td:eq(0)").html(dd2+aux_ref).parent().attr('style',"cursor: zoom-in;"); //.parent().attr('style',"background-color: rgba(210, 184, 0, 0.42);");
                            $("#tb_tretable tr[data-id='"+cab[i].id+el.ruta_id+"-"+last_ref+"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right');
                            parent = last_ref;
                            child = 3;
                            dd2 =  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class=""></i><i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-right"></i>';
                        }
                        
                        html="<tr data-id='"+cab[i].id+el.ruta_id+"-"+cont+"' "+dd+" data-parent='"+cab[i].id+el.ruta_id+"-"+parent+"' data-level="+child+">";
                        html+=    "<td class='"+clase+"' data-column=name>"+dd2+referido+"</td>";
                        html+=    "<td>"+fhora+"</td>";
                        html+=    "<td>"+proc+"</td>";
                        html+=    "<td>"+archivo+"</td>";
                        html+=    "<td>"+area+"</td>";
                        html+=    "<td>"+nord+"</td>";
                        html+="</tr>";
                    }
                    $("#tb_tretable").append(html);
                });
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////

        /*tree-table*/
        $(function () {
            var $table = $('#tree-table'),
            rows = $table.find('tr');

            rows.each(function (index, row) {
                var
                    $row = $(row),
                    level = $row.data('level'),
                    id = $row.data('id'),
                    $columnName = $row.find('td[data-column="name"]'),
                    children = $table.find('tr[data-parent="' + id + '"]');

                if (children.length) {
                    var expander = $columnName.prepend('' +
                        //'<span class="treegrid-expander glyphicon glyphicon-chevron-right"></span>' +
                        '');

                    children.hide();

                    expander.on('click', function (e) {
                        var $target = $(e.target);
                        if ($target.find('i:eq(0)').hasClass('glyphicon glyphicon-chevron-right')) {
                            $target.find('i:eq(0)')
                                .removeClass('glyphicon glyphicon-chevron-right')
                                .addClass('glyphicon glyphicon-chevron-down');

                            children.show();
                        } else {
                            $target.find('i:eq(0)')
                                .removeClass('glyphicon glyphicon-chevron-down')
                                .addClass('glyphicon glyphicon-chevron-right');

                            reverseHide($table, $row);
                        }
                    });
                }

                $columnName.prepend('' +
                    '<span class="treegrid-indent" style="width:' + 15 * level + 'px"></span>' +
                    ''
                );
            });

            reverseHide = function (table, element) {
                var
                    $element = $(element),
                    id = $element.data('id'),
                    children = table.find('tr[data-parent="' + id + '"]');

                if (children.length) {
                    children.each(function (i, e) {
                        reverseHide(table, e);
                    });

                    $element.find('td:eq(0) i:eq(0).glyphicon-chevron-down')
                        .removeClass('glyphicon glyphicon-chevron-down')
                        .addClass('glyphicon glyphicon-chevron-right');

                    children.hide();
                }
            };
        });
        /*end tree-table*/
    }
    else{
        alert('no hay expediente unico');
    }
}

</script>
