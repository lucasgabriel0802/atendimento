<?php
    require_once("class/config.inc.php");
    require_once("class/empresa.class.php");
    require_once("class/funcoes.class.php");

    config::verificaLogin();
    
    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "sair"){
            unset($_SESSION[config::getSessao()]);
            echo json_encode(array("codigo" => 0));
        }
        
        die;
    }
    //  error_reporting(E_ALL);
    //  ini_set('display_errors', 'On');

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
        <link rel="stylesheet" type="text/css" href="css/bootstrap-switch.css">
        <link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker3.min.css">
        <link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.css">
        <link rel="stylesheet" type="text/css" href="css/jquery.gritter.css">
        
        <link rel="stylesheet" type="text/css" href="css/icheck-blue.css">
        <link rel="stylesheet" type="text/css" href="css/style.css" >
        
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.inputmask.bundle.min.js"></script>
        <script type="text/javascript" src="js/jquery.mask.min.js"></script>
        <script type="text/javascript" src="js/jquery.nanoscroller.js"></script>
        <script type="text/javascript" src="js/cleanzone.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript" src="js/bootstrap-switch.js"></script>
        <script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
        <script type="text/javascript" src="js/jquery.maskedinput.js"></script>
        <script type="text/javascript" src="js/jquery.maskedinput.min.js"></script>
        <script type="text/javascript" src="js/page-form-masks.js"></script>
        <script type="text/javascript" src="js/jquery.easypiechart.js"></script>
        <script type="text/javascript" src="js/jquery.flot.js"></script>
        <script type="text/javascript" src="js/jquery.flot.pie.js"></script>
        <script type="text/javascript" src="js/jquery.flot.resize.js"></script>
        <script type="text/javascript" src="js/jquery.flot.time.js"></script>
        <script type="text/javascript" src="js/icheck.min.js"></script>
        <script type="text/javascript" src="js/moment.min.js"></script>
        <script type="text/javascript" src="js/jquery.gritter.min.js"></script>

        <!--if lt IE 9script(src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js') -->
    </head>
    <body>
        <div id="head-nav" class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" data-toggle="collapse" data-target=".navbar-collapse" class="navbar-toggle"><span class="fa fa-gear"></span></button>
                    <a href="admin.php" class="navbar-brand" style="width: auto">
                        <span><?php echo config::getTituloSite(); ?></span>
                    </a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right user-nav">
                        <li class="dropdown profile_menu">
                            <a id="a-empresa-vinculada">
                                <span>Empresa: <strong><?php echo utf8_encode($_SESSION[config::getSessao()]["nome_emp_ativa"]); ?></strong></span>
                                <i class="fa fa-search"></i>
                            </a>
                        </li>
                    </ul>
          
                </div>
            </div>
        </div>
        <div id="cl-wrapper" class="fixed-menu">
            <div class="cl-sidebar">
                <div class="cl-toggle"><i class="fa fa-bars"></i></div>
                <div class="cl-navblock">
                    <div class="menu-space">
                        <div class="content">
                            <ul class="cl-vnavigation">
                                <li class="active" id="li-dashboard">
                                    <a href="javascript: void(0)" onclick="carregarPagina('dashboard', true, '')">
                                        <i class="fa fa-home"></i><span>Painel de controle</span>
                                    </a>
                                </li>
                                <li id="li-funcionarios">
                                    <a href="javascript: void(0)" onclick="carregarPagina('funcionarios', true, '')">
                                        <i class="fa fa-users"></i><span>Funcionários</span>
                                    </a>
                                </li>
                                <li id="li-agenda">
                                    <a href="javascript: void(0)" onclick="carregarPagina('agenda', true, '')">
                                        <i class="fa fa-calendar"></i><span>Agenda</span>
                                    </a>
                                </li>
                                <?php if ($_SESSION[config::getSessao()]["documentos"] == "S"): ?>
                                    <li id="li-documentos">
                                        <a href="javascript: void(0)" onclick="carregarPagina('documentos', true, '')">
                                            <i class="fa fa-paperclip"></i><span>Documentos</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($_SESSION[config::getSessao()]["esocial"] == "S"): ?>
                                    <li id="li-esocial">
                                        <a href="javascript: void(0)" onclick="carregarPagina('esocial', true, '')">
                                            <i class="fa fa-code"></i><span>e-Social</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li id="li-exames">
                                    <a href="javascript: void(0)" onclick="carregarPagina('exames', true, '')">
                                        <i class="fa fa-medkit"></i><span>Exames</span>
                                    </a>
                                </li>
                                <?php if (config::getExibirExamesVencidosVencer() == "S"): ?>
                                    <li id="li-exames-venc">
                                        <a href="javascript: void(0)" onclick="carregarPagina('exames-vencidos-vencer', true, '')">
                                            <i class="fa fa-calendar"></i><span>Exames Vencidos ou à Vencer</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if ($_SESSION[config::getSessao()]["faturas"] == "S"): ?>
                                <li id="li-faturas">
                                    <a href="javascript: void(0)" onclick="carregarPagina('faturas', true, '')">
                                        <i class="fa fa-money"></i><span>Faturas</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li id="li-registro-cat">
                                    <a href="javascript: void(0)" onclick="carregarPagina('registro-cat', true, '')">
                                        <i class="fa fa-plus-square"></i><span>Registro CAT</span>
                                    </a>
                                </li>

                                <?php if ($_SESSION[config::getSessao()]["email"] != ""): ?>
                                <li id="li-contato">
                                    <a href="javascript: void(0)" onclick="carregarPagina('contato', true, '')">
                                        <i class="fa fa-envelope-o"></i><span>Contato</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li id="li-meus-dados">
                                    <a href="javascript: void(0)" onclick="carregarPagina('meus-dados', true, '')">
                                        <i class="fa fa-user"></i><span>Meus dados</span>
                                    </a>
                                </li>
                                <li id="li-sair">
                                    <a href="javascript: void(0)" onclick="carregarPagina('sair', true, '')">
                                        <i class="fa fa-reply"></i><span>Sair</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php if (!funcoes::acessoCelular()): ?>
                    <div class="search-field collapse-button" style="text-align: center;">
                        <h6 style="color: #c9d4f6">Acesse o site de qualquer dispositivo</h6>
                        <img src="img/dispositivos.png">
                    </div>
                    <?php endif; ?>
                </div>
           </div>
            
            <div id="pcont" class="container-fluid"></div>               
            
            <div id="div-modal" tabindex="-1" role="dialog" class="modal fade colored-header"> 
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="border-radius: 5px 5px 0px 0px;">
                            <h3 style="margin: 0; display: inline;">Form Modal</h3>
                            <button type="button" data-dismiss="modal" aria-hidden="true" class="close md-close">×</button>
                        </div>
                        <div class="modal-body">
                        </div>
                    </div>
                </div>
            </div>
            <div id="div-modal-cadastro" tabindex="-1" style="z-index: 1049;" role="dialog" class="modal fade colored-header"> 
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="border-radius: 5px 5px 0px 0px;">
                            <h3 style="margin: 0; display: inline;">Form Modal</h3>
                            <button type="button" data-dismiss="modal" aria-hidden="true" class="close md-close">×</button>
                        </div>
                        <div class="modal-body">
                        </div>
                    </div>
                </div>
            </div>
            <div id="div-modal-grande" tabindex="-1" role="dialog" class="modal fade colored-header"> 
                <div class="modal-dialog" style="width: 90%">
                    <div class="modal-content">
                        <div class="modal-header" style="border-radius: 5px 5px 0px 0px;">
                            <h3 style="margin: 0; display: inline;">Form Modal</h3>
                            <button type="button" data-dismiss="modal" aria-hidden="true" class="close md-close">×</button>
                        </div>
                        <div class="modal-body">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var paginaAtiva = '';
            
            $.strPad = function(i, l, s) {
                var o = i.toString();
                if (!s) { s = '0'; }
                while (o.length < l)
                    o = s + o;

                return o;
            };
                
            function carregarPagina(pagina, alterarMenu, get){
                if (paginaAtiva == 'agenda-novo-1'){
                    
                    paginaAtiva = '';
                    $('#form-button-cancelar').click();
                    
                    if (pagina != 'agenda')
                        carregarPagina(pagina, alterarMenu, get);
                    
                }else{ 

                    $('#pcont').html('<div style="width: 100px; margin: 30px auto;"><img src="img/loader1.gif"></div>');

                    if (alterarMenu){
                        $('#cl-wrapper ul li').removeClass('active');
                        $('#li-' + pagina).addClass('active'); 
                    }

                    if (get != '')
                        get = '?' + get;

                    <?php
                        if (funcoes::acessoCelular())
                            echo "$('#cl-wrapper .cl-vnavigation').slideUp(300);";
                    ?>

                    $('#pcont').load(pagina + '.php' + get, function(response, status, xhr){ 
                        paginaAtiva = pagina;
                        if (status == 'error')
                            $('#pcont').load('404.php');
                    });
                }
            }
            
            $(document).ready(function(){
                App.init();   
                
                // usado pra cancelar agendamento caso esteja aberto
                $(window).one("beforeunload", function(event){ 
                    event.preventDefault();
                    if (paginaAtiva == 'agenda-novo-1')
                        if ($('#form-button-cancelar'))
                            $('#form-button-cancelar').click();
                });
                
                carregarPagina('dashboard', true, '');
            });
            
            $('#a-empresa-vinculada').on('click', function(){
                $('#div-modal').modal();
                $('#div-modal .modal-header h3').text('Selecionar a empresa');
                $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
                $('#div-modal .modal-body').load('empresa-pesquisa.php');
            });
        </script>
    </body>
</html>