<?php
    require_once("class/config.inc.php");
    require_once("class/usuarios.class.php");
    require_once("class/funcoes.class.php");
    require_once("class/criptografia.class.php");

    config::verificaLogin();
    
    $retorno = array("codigo" => 0, "mensagem" => "");
    if (isset($_POST["usuario"]) && isset($_POST["senha"])){        
        
        if (trim($_POST["usuario"]) == ""){
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Informe o usuário!";
        }else
            if (trim($_POST["senha"]) == ""){
                $retorno["codigo"] = 2;
                $retorno["mensagem"] = "Informe a senha!";
            }else
                //if (!funcoes::validarEmail($_POST["usuario"]) && !is_numeric($_POST["email"])){
                    //$retorno["codigo"] = 3;
                    //$retorno["mensagem"] = "Informe um e-mail válido!";
                //}else{
                    $u = new usuarios();
                    $usuarioOk = false;
                    
                    if ($u->buscar(array("LOWER(A.EMAIL)" => strtolower($_POST["usuario"])), 1))
                        $usuarioOk = true;
                    else
                        if ($u->buscar(array("A.CODIGO" => $_POST["usuario"]), 1))
                            $usuarioOk = true;
                        
                    if ($usuarioOk){
                        $temAcesso = $u->getItemLista(0)->getEmpresa()->getDataRescisao() == "";
                        
                        if ($u->getItemLista(0)->getEmpresa()->getDataRescisao() != ""){
                            $dataRescisao = strtotime($u->getItemLista(0)->getEmpresa()->getDataRescisao());
                            $dataHoje     = strtotime(date("Y-m-d"));
                            
                            $temAcesso = $dataRescisao > $dataHoje;
                        }
                        
                        if ($temAcesso){                        
                            if (criptografia::deCriptografia($u->getItemLista(0)->getSenhaWeb()) == $_POST["senha"]){
                                $retorno["codigo"] = 0;

                                $_SESSION[config::getSessao()]["unidade"]        = $u->getItemLista(0)->getUnidade()->getCodigo();
                                $_SESSION[config::getSessao()]["empresa"]        = $u->getItemLista(0)->getEmpresa()->getCodigo();
                                $_SESSION[config::getSessao()]["codigo"]         = $u->getItemLista(0)->getCodigo();
                                $_SESSION[config::getSessao()]["usuarioweb"]     = $u->getItemLista(0)->getUsuarioWeb();
                                $_SESSION[config::getSessao()]["documentos"]     = $u->getItemLista(0)->getAcessoDocumentos();
                                $_SESSION[config::getSessao()]["faturas"]        = $u->getItemLista(0)->getAcessoFaturas();
                                $_SESSION[config::getSessao()]["esocial"]        = $u->getItemLista(0)->getAcessoESocial();
                                $_SESSION[config::getSessao()]["interno"]        = $u->getItemLista(0)->getAcessoInterno();
                                $_SESSION[config::getSessao()]["email"]          = $u->getItemLista(0)->getEmail();
                                $_SESSION[config::getSessao()]["empresa_ativa"]  = $u->getItemLista(0)->getEmpresa()->getCodigo();
                                $_SESSION[config::getSessao()]["nome_emp_ativa"] = $u->getitemlista(0)->getEmpresa()->getRazaoSocial();
                                $_SESSION[config::getSessao()]["gera_ficha_medica"]     = $u->getitemlista(0)->getPermiteGerarFichaMedica();
                                $_SESSION[config::getSessao()]["gera_ficha_medica_ocp"] = $u->getitemlista(0)->getPermiteGerarFichaMedicaOcp();
                                $_SESSION[config::getSessao()]["gera_acuidade_visual"]  = $u->getitemlista(0)->getPermiteGerarAcuidadeVisual();
                                $_SESSION[config::getSessao()]["gera_audiometria"]      = $u->getitemlista(0)->getPermiteGerarAudiometria();
                                $_SESSION[config::getSessao()]["gera_KIT"]              = $u->getitemlista(0)->getPermiteGerarKIT();
                                $_SESSION[config::getSessao()]["situacaoFuncionario"]      = "TODOS";
                                
                                header("Location: admin.php");
                                die;
                            }else{
                                $retorno["codigo"] = 6;
                                $retorno["mensagem"] = "Usuário não encontrado!";
                            }      
                        }else{
                            $retorno["codigo"] = 5;
                            $retorno["mensagem"] = "Empresa não tem permissão para acessar essa ferramenta!";
                        }
                        
                    }else{
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Usuário não encontrado!";
                    }
                //}
    }else
        if (isset($_POST["email"])){
            
            if (trim($_POST["email"]) != ""){
                
                if (funcoes::validarEmail($_POST["email"])){
                    
                    $u = new usuarios();
                    if ($u->buscar(array("LOWER(A.EMAIL)" => strtolower($_POST["email"])), 1)){
                        
                        $senha = substr(md5(date("Y-m-d H:i:s".$u->getItemLista(0)->getCodigo())), 0, 6);
                        if ($u->alterarSenha($u->getItemLista(0)->getUnidade()->getCodigo(), 
                                             $u->getItemLista(0)->getEmpresa()->getCodigo(), 
                                             $u->getItemLista(0)->getCodigo(), 
                                             criptografia::criptografar($senha))){
                            
                            $assunto = "Recuperação de senha";
                            $mensagem = "<div style=\"font-family: verdana, arial; font-size: 12px;\">
                                            <b>Recuperação de senha</b><br><br>
                                            Você está recebendo esse e-mail porque solicitou a recuperação da senha 
                                            no sistema de agendamento online.<br><br>
                                            Sua nova senha é : <b>{$senha}</b><br><br>
                                            Atenciosamente.<br>
                                        </div>";
                            
                            if (funcoes::enviarEmail(config::getEmailContato(), $u->getItemLista(0)->getEmail(), utf8_decode($assunto), utf8_decode($mensagem)))
                                $retorno["mensagem"] = "Ok";
                            else{
                                $retorno["codigo"] = 5;
                                $retorno["mensagem"] = "Não foi possível enviar a nova senha!";
                            }
                        }else{
                            $retorno["codigo"] = 4;
                            $retorno["mensagem"] = "Não foi possível gerar uma nova senha!";
                        }
                        
                    }else{
                        $retorno["codigo"] = 3;
                        $retorno["mensagem"] = "E-mail não encontrado!";
                    }
                    
                }else{
                    $retorno["codigo"] = 2;
                    $retorno["mensagem"] = "Informe um e-mail válido!";
                }
                
            }else{
                $retorno["codigo"] = 1;
                $retorno["mensagem"] = "Informe o e-mail!";
            }
            
            echo json_encode($retorno);
            die;
        }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta name="theme-color" content="#2494F2" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="agenda online kayser informática jlk sistemas agenda web ocupacional">
        <meta name="author" content="Kayser Informatica">
        <link rel="shortcut icon" href="img/favicon.png">
        <title><?php echo config::getTituloSite(); ?></title>
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,400italic,700,800">
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Raleway:300,200,100">
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700">
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="css/nanoscroller.css">
        <link rel="stylesheet" type="text/css" href="css/jquery.gritter.css" >
        <link rel="stylesheet" type="text/css" href="css/style.css" >

        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.nanoscroller.js"></script>
        <script type="text/javascript" src="js/cleanzone.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/jquery.gritter.min.js"></script>
        <!--if lt IE 9script(src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js') -->
    </head>
    <body class="texture">
        <div id="cl-wrapper" class="login-container">
            <div class="middle-login" id="div-login">
                <div class="block-flat">
                    <div class="header">
                        <h3 class="text-center"><img src="img/logo.png" alt="logo" class="logo-img"><?php echo config::getTituloSite(); ?></h3>
                    </div>
                    <div>
                        <form method="post">
                            <div class="content">
                                <h4 class="title">Login</h4>

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span>
                                            <input name="usuario" type="input" autocomplete="off" placeholder="usuário" class="form-control" value="<?php if (isset($_POST["usuario"])) echo $_POST["usuario"]; ?>" style="text-transform: lowercase">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="input-group"><span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input name="senha" type="password" placeholder="senha" class="form-control">
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            <div class="foot">
                                <div style="float: left">
                                    <a href="javascript: void(0);" id="login-a-esqueci-minha-senha">Esqueci minha senha</a>
                                </div>
                                <button data-dismiss="modal" type="submit" class="btn btn-primary" id="button-login">Entrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="middle-login" id="div-recuperar-senha" style="display: none">
                <div class="block-flat">
                    <div class="header">
                        <h3 class="text-center"><img src="img/logo.png" alt="logo" class="logo-img"><?php echo config::getTituloSite(); ?></h3>
                    </div>
                    <div>
                        <div class="content">
                            <h4 class="title">Informe seu e-mail para criar uma nova senha!</h4>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-envelope-o"></i>
                                        </span>
                                        <input id="recuperar-usuario" type="email" placeholder="E-mail" class="form-control" style="text-transform: lowercase">
                                    </div>
                                </div>
                            </div>                               
                        </div>
                        <div class="foot">
                            <button data-dismiss="modal" type="button" class="btn btn-primary" id="button-enviar">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function(){
                App.init();
                <?php
                    if (isset($retorno) && is_array($retorno)){
                        if ($retorno["codigo"] > 0){
                            echo "$.gritter.add({
                                    title: 'Ops!',
                                    text: '{$retorno["mensagem"]}' + ' #' + '{$retorno["codigo"]}',
                                    class_name: 'danger'
                                 });";
                                    
                            switch ($retorno["codigo"]) {
                                case 1: 
                                    echo "$('#usuario').focus();";
                                    break;
                                case 2: 
                                    echo "$('#senha').focus();";
                                    break;
                                case 3: 
                                    echo "$('#usuario').focus();";
                                    break;
                                case 4: 
                                    echo "$('#usuario').focus();";
                                    break;
                                case 5: 
                                    echo "$('#usuario').focus();";
                                    break; 
                            }
                        }
                    }
                ?>
            });
            
            $('#login-a-esqueci-minha-senha').on('click', function(){
                $('#recuperar-usuario').val($('#usuario').val());
                $('#div-login').hide(0);
                $('#div-recuperar-senha').show(0);
            })
            
            $('#recuperar-usuario').keypress(function(event){
                if (event.which == 13)
                   $('#button-enviar').click();
            });

            $('#button-enviar').on('click', function(){
                $('#button-enviar').text('Aguarde...');
                $('#button-enviar').addClass('disabled');
                
                $.ajax({
                    type: 'post',
                    url: 'index.php',
                    data: 'email=' + $('#recuperar-usuario').val(),
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
                        $.gritter.add({
                            title: 'Quase!',
                            text: 'Foi enviado uma nova senha para o seu e-mail!',
                            class_name: 'success'
                        });
                        $('#div-login').show(0);
                        $('#div-recuperar-senha').hide(0);
                        $('#usuario').val($('#recuperar-usuario').val());
                    }
    
                    $('#button-enviar').text('Enviar');
                    $('#button-enviar').removeClass('disabled');
                });
            });

            $('#button-login').on('click', function(){
                $('#button-login').text('Aguarde...');
                $('#button-login').addClass('disabled');
            });            
        </script>
    </body>
</html>