<?php
    require_once("class/config.inc.php");
    require_once("class/funcoes.class.php");
    require_once("class/empresa.class.php");

    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "enviar"){
            $retorno = array("codigo" => 0, "mensagem" => "");

            if (!isset($_POST["assunto"]))
                $retorno["codigo"] = 1;
            else
                if (!isset($_POST["mensagem"]))
                    $retorno["codigo"] = 2;
                
            if ($retorno["codigo"] > 0){
                $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                echo json_encode($retorno);
                die;
            }
            
            if (trim($_POST["assunto"]) != ""){
                
                if (trim($_POST["mensagem"]) != ""){

                    
                    $email = "";
                    
                    if (config::getModeloAgendamento() == 1)
                        $email = config::getEmailContato();
                    else{
                        $em = new empresa();
                        if ($em->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                              "A.CODIGO"  => $_SESSION[config::getSessao()]["empresa_ativa"]), 1))
                            $email = $em->getItemLista(0)->getResponsavel()->getEmail();
                    }
                    
                    if ($email != ""){
                        
                        $mensagem = "Usuário: ".utf8_encode($_SESSION[config::getSessao()]["usuarioweb"])."<br>".
                                    "Empresa: ".utf8_encode($_SESSION[config::getSessao()]["nome_emp_ativa"])."<br><br>".
                                    $_POST["mensagem"];
                        
                        $erro = "";
                        if (funcoes::enviarEmail($_SESSION[config::getSessao()]["email"], 
                                                 $email, 
                                                 utf8_decode($_POST["assunto"]),
                                                 utf8_decode(str_replace("\n", "<br>", $mensagem)), $erro)){

                            $retorno["mensagem"] = "Ok";

                        }else{
                            $retorno["codigo"] = 6;
                            $retorno["mensagem"] = "Não foi possível enviar o e-mail!<br>".$erro;
                        }
                        
                    }else{
                        $retorno["codigo"] = 5;
                        $retorno["mensagem"] = "E-mail do técnico responsável pela empresa não informado!";
                    }
                    
                }else{
                    $retorno["codigo"] = 4;
                    $retorno["mensagem"] = "Informe a mensagem!";
                }
                
            }else{
                $retorno["codigo"] = 3;
                $retorno["mensagem"] = "Informe o assunto!";
            }
            
            echo json_encode($retorno);
        }
        
        die;
    }
?>

<div class="page-head">
    <h2>Contato</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li class="active">Contato</li>
    </ol>
</div>
<div class="cl-mcont">
    <div class="block-flat">
        <div class="form-group-sm">
            <div class="col-sm-12 col-md-12 col-lg-6">
                <label>Nome:</label>
                <input type="text" readonly class="form-control input-sm" value="<?php echo $_SESSION[config::getSessao()]["usuarioweb"]; ?>">
            </div>
            <div class="clearfix"></div>
        </div>  
        <div class="form-group-sm">
            <div class="col-sm-12 col-md-12 col-lg-6">
                <label>E-mail:</label>
                <input type="text" readonly class="form-control input-sm" value="<?php echo $_SESSION[config::getSessao()]["email"]; ?>">
            </div>
            <div class="clearfix"></div>
        </div>   
        <div class="form-group-sm">
            <div class="col-sm-12 col-md-12 col-lg-6">
                <label>Assunto:</label>
                <input type="text" class="form-control input-sm" id="form-input-assunto">
            </div>
            <div class="clearfix"></div>
        </div> 
        <div class="form-group-sm">
            <div class="col-sm-12 col-md-12 col-lg-6">
                <label>Mensagem:</label>
                <textarea id="form-textarea-mensagem" class="form-control input-sm" style="height: 70px;"></textarea>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group-sm" style="padding-top: 10px; text-align: right">
            <div class="col-sm-12 col-md-12 col-lg-6">
                <button type="button" class="btn btn-primary" id="form-button-gravar">Enviar</button>
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
            url: 'contato.php',
            data: 'a=enviar&assunto=' + $('#form-input-assunto').val() + '&mensagem=' + $('#form-textarea-mensagem').val(),
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
                carregarPagina('contato', true, '');
                $.gritter.add({
                    title: 'Oba!',
                    text: 'Sua mensagem foi enviada com sucesso!',
                    class_name: 'success'
                });
            }
            
            $('#form-button-gravar').removeClass('disabled');
            $('#form-button-gravar').text('Gravar');
        });
    });
</script>