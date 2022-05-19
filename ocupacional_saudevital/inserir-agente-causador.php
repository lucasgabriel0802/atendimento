<?php
require_once("class/config.inc.php");
require_once("class/usuarios.class.php");
require_once("class/criptografia.class.php");

config::verificaLogin();


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>


</head>

<body>
    <!-- <form role="form"> -->

    <div class="form-group-sm">
        <div class="col-sm-12" style="height: 58px;">
            <label>Agente Causador:</label>
            <div class="input-group">
                <input type="hidden" id="form-input-inserir-agente-causador">
                <input type="text" id="form-input-inserir-descricao-agente-causador" readonly="" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                <span class="input-group-btn">
                    <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-buscar-agente-causador" style="height: 30px; padding-top: 4px">
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
        if (($('#form-input-inserir-agente-causador').val() != '') ){

            ultimoItem = ultimoItem  +1;

            var corpoTabela = document.getElementById("body-table-agente-causador");
            var registro = document.createElement("tr");
            registro.setAttribute("class","no-border-y");

            var campoCodigoAgente = document.createElement("td");
            campoCodigoAgente.setAttribute("id","codigo-agente-causador");

            var campoDescricaoAgenteCausador = document.createElement("td");
            campoDescricaoAgenteCausador.setAttribute("id","descricao-agente-causador");

            var campoBotao = document.createElement("td");
            campoBotao.setAttribute("id","button-excluir");

            campoCodigoAgente.className = "oculto";

            var codigoAgente = document.createTextNode($('#form-input-inserir-agente-causador').val());
            var descricaoAgente = document.createTextNode($('#form-input-inserir-descricao-agente-causador').val());
            var descricaoBotao = document.createTextNode("Excluir");

            // registro.setAttribute('id', 'parte-'+$('#form-input-codigo-parte-corpo').val()+'-lateralidade-'+$('#form-input-codigo-lateralidade').val()  );
            registro.setAttribute('id', 'agente-causador-linha-'+ultimoItem);

            campoCodigoAgente.appendChild(codigoAgente);
            campoDescricaoAgenteCausador.appendChild(descricaoAgente);

            var botaoExcluir = document.createElement("a");
            botaoExcluir.appendChild(document.createTextNode("Excluir"));
            botaoExcluir.setAttribute('href', 'javascript: void(0)');
            botaoExcluir.setAttribute('class', 'btn btn-primary btn-xs');
            botaoExcluir.setAttribute('id', 'btn-excluir');
            botaoExcluir.setAttribute('onClick','Excluir($(this),\'agente-causador-linha-'+ultimoItem+'\')')
            
            campoBotao.appendChild(botaoExcluir);

            registro.appendChild(campoCodigoAgente)
            registro.appendChild(campoDescricaoAgenteCausador)
            registro.appendChild(campoBotao)  

            corpoTabela.appendChild(registro);
            $('#div-modal').modal('toggle');
        } else {
            if (($('#form-input-inserir-agente-causador').val() == '')) {
                    $.gritter.add({
                    title: 'Ops!',
                    text: 'Informe o agente causador #1',
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

    $('#form-button-buscar-agente-causador').on('click', function(){
        $('#div-modal-1').modal();
        $('#div-modal-1 .modal-header h3').text('Pesquisa parte do corpo');
        $('#div-modal-1 .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal-1 .modal-body').load('agente-causador-pesquisa.php', { 'prefixo': 'form-input-inserir' ,'div':'div-modal-1'});
    });   

