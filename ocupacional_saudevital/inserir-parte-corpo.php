<?php
require_once("class/config.inc.php");
require_once("class/usuarios.class.php");
require_once("class/criptografia.class.php");

config::verificaLogin();

if (isset($_POST["a"])) {
    $acao = $_POST["a"];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>


</head>

<body>
    <!-- <form role="form"> -->

    <div class="form-group-sm">
        <div class="col-sm-12" style="height: 58px;">
            <label>Parte do Corpo:</label>
            <div class="input-group">
                <input type="hidden" id="form-input-codigo-parte-corpo">
                <input type="text" id="form-input-descricao-parte-corpo" readonly="" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                <span class="input-group-btn">
                    <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-parte-corpo" style="height: 30px; padding-top: 4px">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group-sm">
        <div class="col-sm-12" style="height: 58px;">
            <label>Lateralidade:</label>
            <div class="input-group">
                <input type="hidden" id="form-input-codigo-lateralidade">
                <input type="text" id="form-input-descricao-lateralidade" readonly="" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                <span class="input-group-btn">
                    <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-lateralidade" style="height: 30px; padding-top: 4px">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="form-group-sm" style="padding-top: 10px; text-align: right">
        <div class="col-sm-12 col-md-12 col-lg-6">
            <button type="button" class="btn btn-primary" id="form-button-incluir">Incluir</button>
            <button type="button" class="btn btn-danger" id="form-button-cancelar">Cancelar</button>
        </div>
        <div class="clearfix"></div>
    </div>
    <!-- </form> -->
</body>
<script>

    $(document).ready(function() {
        App.init();
        //App.formElements();
    });

    $('#form-button-incluir').on('click', function() {
        if (($('#form-input-codigo-parte-corpo').val() != '') &&
            ($('#form-input-codigo-lateralidade').val() != '')) {

            ultimoItem = ultimoItem  +1;

            var corpoTabela = document.getElementById("body-table-parte-corpo");
            var registro = document.createElement("tr");
            registro.setAttribute("class","no-border-y");

            var campoCodigoParte = document.createElement("td");
            campoCodigoParte.setAttribute("id","codigo-parte-corpo");

            var campoDescricaoParte = document.createElement("td");
            campoDescricaoParte.setAttribute("id","descricao-parte-corpo");

            var campoCodigoLateralidade = document.createElement("td");
            campoCodigoLateralidade.setAttribute("id","codigo-lateralidade");

            var campoDescricaoLateralidade = document.createElement("td");
            campoDescricaoLateralidade.setAttribute("id","descricao-lateralidade");

            var campoBotao = document.createElement("td");
            campoBotao.setAttribute("id","button-excluir");

            campoCodigoParte.className = "oculto";
            campoCodigoLateralidade.className = "oculto";

            var codigoParte = document.createTextNode($('#form-input-codigo-parte-corpo').val());
            var descricaoParte = document.createTextNode($('#form-input-descricao-parte-corpo').val());
            var codigoLateralidade = document.createTextNode($('#form-input-codigo-lateralidade').val());
            var descricaoLateralidade = document.createTextNode($('#form-input-descricao-lateralidade').val());
            var descricaoBotao = document.createTextNode("Excluir");

            // registro.setAttribute('id', 'parte-'+$('#form-input-codigo-parte-corpo').val()+'-lateralidade-'+$('#form-input-codigo-lateralidade').val()  );
            registro.setAttribute('id', 'parte-atingida-linha-'+ultimoItem);

            campoCodigoParte.appendChild(codigoParte);
            campoDescricaoParte.appendChild(descricaoParte);
            campoCodigoLateralidade.appendChild(codigoLateralidade);
            campoDescricaoLateralidade.appendChild(descricaoLateralidade);

            var botaoExcluir = document.createElement("a");
            botaoExcluir.appendChild(document.createTextNode("Excluir"));
            botaoExcluir.setAttribute('href', 'javascript: void(0)');
            botaoExcluir.setAttribute('class', 'btn btn-primary btn-xs');
            botaoExcluir.setAttribute('id', 'btn-excluir');
            botaoExcluir.setAttribute('onClick','Excluir($(this),\'parte-atingida-linha-'+ultimoItem+'\')')
            
            campoBotao.appendChild(botaoExcluir);

            registro.appendChild(campoCodigoParte)
            registro.appendChild(campoDescricaoParte)
            registro.appendChild(campoCodigoLateralidade)
            registro.appendChild(campoDescricaoLateralidade)
            registro.appendChild(campoBotao)  

            corpoTabela.appendChild(registro);
            $('#div-modal').modal('toggle');
        } else {
            if (($('#form-input-codigo-parte-corpo').val() == '') &&
                ($('#form-input-codigo-lateralidade').val() == '')) {
                    $.gritter.add({
                    title: 'Ops!',
                    text: 'Informe todos os campos #1',
                    class_name: 'danger'
                });
            }else
            if (($('#form-input-codigo-parte-corpo').val() == '')) {
                    $.gritter.add({
                    title: 'Ops!',
                    text: 'Informe a parte do corpo atingida #2',
                    class_name: 'danger'
                });
            }else
            if (($('#form-input-codigo-lateralidade').val() == '')) {
                    $.gritter.add({
                    title: 'Ops!',
                    text: 'Informe a lateralidade #1',
                    class_name: 'danger'
                });
            };

        }


    });



    $('#form-button-cancelar').on('click', function() {
        $.gritter.add({
            title: 'Ok!',
            text: 'A alteração do seus dados foi cancelada!',
            class_name: 'info'
        });
        $('#div-modal').modal('toggle');

    });

    $('#form-button-pesquisa-parte-corpo').on('click', function(){
        $('#div-modal-1').modal();
        $('#div-modal-1 .modal-header h3').text('Pesquisa Parte do Corpo');
        $('#div-modal-1 .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal-1 .modal-body').load('parte-corpo-pesquisa.php', { 'prefixo': 'form-input' });
    });   

    $('#form-button-pesquisa-lateralidade').on('click', function(){
        $('#div-modal-1').modal();
        $('#div-modal-1 .modal-header h3').text('Pesquisa Lateralidade');
        $('#div-modal-1 .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal-1 .modal-body').load('lateralidade-pesquisa.php', { 'prefixo': 'form-input' ,'div':'div-modal-1'});
    });   
