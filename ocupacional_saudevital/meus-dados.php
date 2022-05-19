<?php
    require_once("class/config.inc.php");
    require_once("class/usuarios.class.php");
    require_once("class/criptografia.class.php");

    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "gravar"){
            $retorno = array("codigo" => 0, "mensagem" => "");

            if (!isset($_POST["senha"]))
                $retorno["codigo"] = 1;
            else
                if (!isset($_POST["confirma-senha"]))
                    $retorno["codigo"] = 2;
                
            if ($retorno["codigo"] > 0){
                $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                echo json_encode($retorno);
                die;
            }
            
            if (trim($_POST["senha"]) != ""){
                
                if (trim($_POST["confirma-senha"]) != ""){
                    
                    if ($_POST["senha"] == $_POST["confirma-senha"]){
                        
                        $us = new usuarios();
                        if ($us->alterarSenha($_SESSION[config::getSessao()]["unidade"], 
                                              $_SESSION[config::getSessao()]["empresa_ativa"], 
                                              $_SESSION[config::getSessao()]["codigo"], 
                                              criptografia::criptografar($_POST["senha"]))){
                            $retorno["mensagem"] = "Ok";
                        }else{
                            $retorno["codigo"] = 6;
                            $retorno["mensagem"] = "Não foi possível alterar a senha!";
                        }
                        
                    }else{
                        $retorno["codigo"] = 5;
                        $retorno["mensagem"] = "As senhas não coincidem!";
                    }
                    
                }else{
                    $retorno["codigo"] = 4;
                    $retorno["mensagem"] = "Confirme a senha!";
                }
                
            }else{
                $retorno["codigo"] = 3;
                $retorno["mensagem"] = "Informe a senha!";
            }
            
            echo json_encode($retorno);
        }
        
        die;
    }
?>

<div class="page-head">
    <h2>Meus dados</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li class="active">Meus dados</li>
    </ol>
</div>
<div class="cl-mcont">
    <div class="block-flat">
        <div class="form-group-sm">
            <div class="col-sm-12 col-md-12 col-lg-6" style="height: 58px;">
                <label>Nome:</label>
                <input type="text" readonly class="form-control input-sm" value="<?php echo $_SESSION[config::getSessao()]["usuarioweb"]; ?>">
            </div>
            <div class="clearfix"></div>
        </div>  
        <div class="form-group-sm">
            <div class="col-sm-12 col-md-12 col-lg-6" style="height: 58px;">
                <label>E-mail:</label>
                <input type="text" readonly class="form-control input-sm" value="<?php echo $_SESSION[config::getSessao()]["email"]; ?>">
            </div>
            <div class="clearfix"></div>
        </div>  
        <div class="form-group-sm">
            <div class="col-sm-6 col-md-6 col-lg-3" style="height: 58px;">
                <label>Senha:</label>
                <input type="password" id="form-input-senha" class="form-control input-sm">
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3" style="height: 58px;">
                <label>Confirme a senha:</label>
                <input type="password" id="form-input-confirme-senha" class="form-control input-sm">
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group-sm" style="padding-top: 10px; text-align: right">
            <div class="col-sm-12 col-md-12 col-lg-6">
                <button type="button" class="btn btn-primary" id="form-button-gravar">Gravar</button>
                <button type="button" class="btn btn-danger" id="form-button-cancelar">Cancelar</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#form-input-data').datepicker();
        $('#form-input-data').mask('99/99/9999');
    });
    
    $('#form-button-gravar').on('click', function(){
        $('#form-button-gravar').addClass('disabled');
        $('#form-button-gravar').text('Aguarde...');
        
        $.ajax({
            type: 'post',
            url: 'meus-dados.php',
            data: 'a=gravar&senha=' + $('#form-input-senha').val() + '&confirma-senha=' + $('#form-input-confirme-senha').val(),
            dataType: 'json',
            async: true,
            cache: false
        }).done(function(data){
            if (data.codigo > 0){
                $.gritter.add({
                    title: 'Ops!',
                    text: data.mensagem + ' #' + data.codigo,
                    class_name: 'danger'
                });
            }else{
                carregarPagina('meus-dados', true, '');
                $.gritter.add({
                    title: 'Oba!',
                    text: 'Sua senha foi alterada com sucesso!',
                    class_name: 'success'
                });
            }
            
            $('#form-button-gravar').removeClass('disabled');
            $('#form-button-gravar').text('Gravar');
        });
    });
    
    $('#form-button-cancelar').on('click', function(){
        carregarPagina('meus-dados', true, '');
        $.gritter.add({
            title: 'Ok!',
            text: 'A alteração do seus dados foi cancelada!',
            class_name: 'info'
        });
    });
</script>