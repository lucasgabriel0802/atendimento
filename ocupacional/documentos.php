<?php
    require_once("class/config.inc.php");
    require_once("class/funcaso.class.php");
    require_once("class/funcexames.class.php");
    require_once("class/funcionario.class.php");
    require_once("class/empresa.class.php");
    require_once("class/parametrosgerais.class.php");
    require_once("class/criptografia.class.php");
    require_once("class/funcoes.class.php");
    require_once("class/tabela.class.php");

    config::verificaLogin();
    
    if ($_SESSION[config::getSessao()]["documentos"] != "S"){
        header("Location: 403.php");
        die;
    }
   
    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "pesquisar-documentos"){
            $retorno = array("codigo" => 0, "mensagem" => "");
            
            if (isset($_POST["funcionario"]) && isset($_POST["situacao"])){
                
                if (is_numeric($_POST["funcionario"])){
                        
                        if (in_array($_POST["situacao"], array("A", "E", "T"))){
                            
                            $pagina  = 1;
                            if (isset($_POST["pagina"]))
                                if (is_numeric($_POST["pagina"]))
                                    $pagina = $_POST["pagina"];

                            $pagina--; // inicia com 0
                            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
                            $totalRegistros = 0;
                            
                            $fu = new funcionario();
                            if ($fu->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                  "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                  "A.MATRICULA" => $_POST["funcionario"]), 1)){
                                $fu = $fu->getItemLista(0);
                                
                                $arrayASO = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                  "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                  "A.MATRICULA" => $_POST["funcionario"]);
                                
                                if ($_POST["situacao"] != "T")
                                    $arrayASO["A.SITUACAO"] = $_POST["situacao"];

                                $arrayASO["A.COMPLETO"] = 'SIM';
                                $arrayASO["COALESCE(A.APTO, '')"] <> "";
                                $arrayASO["A.apto"] = array(["S","N"], "IN");
                                $fa = new funcaso();
                                if ($fa->buscar($arrayASO, $limite, 
                                                array("A.UNIDADE" => "ASC", "A.EMPRESA" => "ASC", 
                                                      "A.MATRICULA" => "ASC", "A.DATAPEDIDO" => "ASC"))){
                                    $totalRegistros = $fa->getTotalRegistros();
                                    
                                    $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                                    $cab = new cabecalho(array("class" => "no-border"));
                                    $cab->addItem("Pedido", array("style" => "font-weight: bold; text-align: center;"));
                                    $cab->addItem("Gerar", array("style" => "font-weight: bold; text-align: center;"));
                                    $tab->addCabecalho($cab);
                                    
                                    //$classeBotao = "btn btn-default btn-xs";

                                    //if (funcoes::acessoCelular())
                                        $classeBotao = "\" style=\"padding: 0px 10px;";
                                    
                                    for ($i = 0; $i < $fa->getTotalLista(); $i++){
                                        $reg = new registro(array("class" => "no-border-y"));
                                        $reg->addItem(date("d/m/Y", strtotime($fa->getItemLista($i)->getDataPedido())), array("style" => "text-align: center;"));
                                        $reg->addItem($fa->getItemLista($i)->getApto(), array("style" => "text-align: center;"));
                                        
                                        $bTodos = "<a class=\"{$classeBotao}\" href=\"javascript: void(0)\" "
                                                    . "acao=\"todos\" numero=\"{$fa->getItemLista($i)->getNumero()}\">Todos"
                                                . "</a>";
                                        $bASO = "<a class=\"{$classeBotao}\" href=\"javascript: void(0)\" "
                                                . "acao=\"aso\" numero=\"{$fa->getItemLista($i)->getNumero()}\">ASO"
                                            . "</a>";
                                        $bFichaMedica = "<a class=\"{$classeBotao}\" href=\"javascript: void(0)\" "
                                                        . "acao=\"fichamedica\" numero=\"{$fa->getItemLista($i)->getNumero()}\">Ficha m??dica"
                                                    . "</a>";
                                        $bFichaMedicaOcup = "<a class=\"{$classeBotao}\" href=\"javascript: void(0)\" "
                                                            . "acao=\"fichamedicaocup\" numero=\"{$fa->getItemLista($i)->getNumero()}\">Ficha m??dica Ocupacional"
                                                        . "</a>";
                                        
                                        

                                        $bAcuidadeVisual = "";
                                        $fe = new funcexames();
                                        if ($fe->buscar(array("A.NUMEROASO" => $fa->getItemLista($i)->getNumero(),
                                                               "A.MATRICULA" => $fu->getCodigo(),
                                                               "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                               "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                               "UPPER(B.DESCRICAO)" => array("ACUIDADE", "CONTAINING"))))
                                            $bAcuidadeVisual = "<a class=\"{$classeBotao}\" href=\"javascript: void(0)\" "
                                                                . "acao=\"acuidadevisual\" numero=\"{$fa->getItemLista($i)->getNumero()}\">Acuidade visual"
                                                            . "</a>";

                                        $bAudiometria = "";
                                        $fe = new funcexames();
                                        if ($fe->buscar(array("A.NUMEROASO" => $fa->getItemLista($i)->getNumero(),
                                                               "A.MATRICULA" => $fu->getCodigo(),
                                                               "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                               "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                               "UPPER(B.DESCRICAO)" => array("AUDIOMETRIA", "CONTAINING"))))
                                                $bAudiometria = "<a class=\"{$classeBotao}\" href=\"javascript: void(0)\" "
                                                                . "acao=\"audiometria\" numero=\"{$fa->getItemLista($i)->getNumero()}\">Audiometria"
                                                            . "</a>";
                                                        
                                        //$botoes = "";
                                        //if (funcoes::acessoCelular())
                                            $botoes = "<div class=\"btn-group\">
                                                        <button type=\"button\" data-toggle=\"dropdown\" class=\"btn btn-default btn-xs\" ".((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "").">
                                                            Baixar
                                                        </button>
                                                        <button type=\"button\" data-toggle=\"dropdown\" "
                                                        ." style=\"width: 22px; margin-left: -3px; padding-left: 6px;\" "
                                                        ." class=\"btn btn-primary btn-xs dropdown-toggle\">
                                                            <span class=\"caret\"></span>
                                                        </button>
                                                        <ul role=\"menu\" class=\"dropdown-menu dropdown-menu-right\">
                                                            <li>{$bTodos}</li>
                                                            <li>{$bASO}</li>
                                                            <li>{$bFichaMedica}</li>
                                                            <li>{$bFichaMedicaOcup}</li>
                                                            <li>{$bAcuidadeVisual}</li>
                                                            <li>{$bAudiometria}</li>
                                                        </ul>
                                                      </div>";
                                        //else
                                            //$botoes = $bTodos.$bASO.$bFichaMedica.$bFichaMedicaOcup.$bAcuidadeVisual.$bAudiometria;
                                        
                                        $reg->addItem($botoes, array("style" => "text-align: center;"));
                                        $tab->addRegistro($reg);
                                    }
                                    
                                    echo $tab->gerar();
                                    echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarDocumentosFuncionario('<pagina>')");
                                }else
                                    echo "<h4>Nenhum resultado foi encontrado!</h4>";
                            }else{
                                $retorno["codigo"] = 5;
                                $retorno["mensagem"] = "Funcion??rio n??o encontrado!";
                            }
                            
                        }else{
                            $retorno["codigo"] = 4;
                            $retorno["mensagem"] = "Selecione uma situa????o v??lida!";
                        }
                    
                }else{
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Informe um funcion??rio v??lido!";
                }
            }else{
                if (!isset($_POST["funcionario"]))
                    $retorno["codigo"] = 1;
                else
                    if (!isset($_POST["situacao"]))
                        $retorno["codigo"] = 2;
                $retorno["mensagem"] = "Alguns par??metros n??o foram encontrados!";
            }
            
            if ($retorno["codigo"] > 0)
                echo "<script>$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
        }
        
        die;
    }

    if (isset($_GET["a"])){
        $acao = $_GET["a"];
        
        if ($acao == "gerar"){
            $retorno = array("codigo" => 0, "mensagem" => "");

            if (isset($_GET["funcionario"]) && isset($_GET["documento"]) && isset($_GET["numero"])){

                if (is_numeric($_GET["funcionario"])){

                        if (in_array($_GET["documento"], array("aso", "fichamedica", "fichamedicaocup", "acuidadevisual", "audiometria", "ppra", "pcmso"))){

                            if (is_numeric($_GET["numero"])){

                                $fu = new funcionario();
                                if ($fu->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                      "A.MATRICULA" => $_GET["funcionario"]), 1)){
                                    $fu = $fu->getItemLista(0);

                                    $arrayASO = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                      "A.MATRICULA" => $_GET["funcionario"],
                                                      "A.NUMERO"    => $_GET["numero"]);

                                    $fa = new funcaso();
                                    if ($fa->buscar($arrayASO, 1)){
                                        
                                        $docOk = (!in_array($_GET["documento"], array("acuidadevisual", "audiometria")));
                                        
                                        if (!$docOk){
                                            if ($_GET["documento"] == "acuidadevisual"){
                                                $fe = new funcexames();
                                                if ($fe->buscar(array("A.NUMEROASO" => $_GET["numero"],
                                                                      "A.MATRICULA" => $_GET["funcionario"],
                                                                      "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                      "UPPER(B.DESCRICAO)" => array("ACUIDADE", "CONTAINING"))))
                                                    $docOk = true;
                                            }else
                                                if ($_GET["documento"] == "audiometria"){
                                                    $fe = new funcexames();
                                                    if ($fe->buscar(array("A.NUMEROASO" => $_GET["numero"],
                                                                          "A.MATRICULA" => $_GET["funcionario"],
                                                                          "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                          "UPPER(B.DESCRICAO)" => array("AUDIOMETRIA", "CONTAINING"))))
                                                        $docOk = true;
                                                }
                                        }
                                        
                                        if ($docOk){
                                            $path    = realpath(dirname(__FILE__))."\\geradoc\\DOC\\";
                                            $prefixo = "{$_SESSION[config::getSessao()]["unidade"]}{$_SESSION[config::getSessao()]["empresa_ativa"]}{$_GET["funcionario"]}";
                                            $arquivoPDF = $path.$prefixo.".pdf";

                                            exec(realpath(dirname(__FILE__))."\\geradoc\\GeraDoc.exe"
                                                . " -unidade={$_SESSION[config::getSessao()]["unidade"]}"
                                                . " -empresa={$_SESSION[config::getSessao()]["empresa_ativa"]}"
                                                . " -documento={$_GET["documento"]}"
                                                . " -funcionario={$_GET["funcionario"]}"
                                                . " -numaso={$_GET["numero"]}");


                                            if (file_exists($arquivoPDF)){
                                                $novoNome = str_replace(" ", "-", funcoes::separarPrimeiroUltimoNome($fu->getNome()))."-".$_GET["documento"];
                                                header("Content-Type: application/pdf");
                                                header("Content-disposition: attachment; filename={$novoNome}.pdf");
                                                readfile($arquivoPDF);
                                                unlink($arquivoPDF);
                                            }else{
                                                $retorno["codigo"] = 10;
                                                $retorno["mensagem"] = "N??o foi poss??vel gerar o arquivo!";
                                            }
                                        }/*else{
                                            $doc = "";
                                            
                                            if ($_GET["documento"] == "acuidadevisual")
                                                $doc = "Acuidade visual";
                                            else
                                                if ($_GET["documento"] == "audiometria")
                                                    $doc = "Audiometria";
                                            
                                            $retorno["codigo"] = 9;
                                            $retorno["mensagem"] = "O documento de {$doc} n??o foi gerado porque n??o tem exames desse tipo!";
                                        }*/
                                        
                                    }else{
                                        $retorno["codigo"] = 8;
                                        $retorno["mensagem"] = "Documento n??o encontrado!";
                                    }

                                }else{
                                    $retorno["codigo"] = 7;
                                    $retorno["mensagem"] = "Funcion??rio n??o encontrado!";
                                }

                            }else{
                                $retorno["codigo"] = 6;
                                $retorno["mensagem"] = "N??mero do ASO inv??lido!";
                            }

                        }else{
                            $retorno["codigo"] = 5;
                            $retorno["mensagem"] = "Selecione um documeto v??lido!";
                        }

                }else{
                    $retorno["codigo"] = 4;
                    $retorno["mensagem"] = "Informe um funcion??rio v??lido!";
                }
            }else{
                if (!isset($_POST["funcionario"]))
                    $retorno["codigo"] = 1;
                else
                    if (!isset($_POST["documento"]))
                        $retorno["codigo"] = 2;
                    else
                        if (!isset($_POST["numero"]))
                            $retorno["codigo"] = 3;

                $retorno["mensagem"] = "Alguns par??metros n??o foram encontrados!";
            }

            if ($retorno["codigo"] > 0){
                if ($retorno["codigo"] == 9)
                    echo "<script>parent.$.gritter.add({ title: 'Olha s??!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'warning' });</script>";
                else
                    echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
            }
        } else
            if (in_array($acao, ["gerar-ppra", "gerar-pcmso"])){
                if ($acao == "gerar-ppra"){
                    $nomeDoc = "ppra";
                }else{
                    $nomeDoc = "pcmso";
                }
                $retorno = array("codigo" => 0, "mensagem" => "");
                
                if (isset($_GET['posto']) && isset($_GET['data'])) {
                    if (is_numeric($_GET['posto'])) {
                        if (funcoes::validarData($_GET['data'])) {
                            ini_set('max_execution_time', 120); // 2 min
                            
                            $path    = realpath(dirname(__FILE__))."\\geradoc\\DOC\\";
                            $nomeArquivo = "{$_SESSION[config::getSessao()]["unidade"]}{$_SESSION[config::getSessao()]["empresa_ativa"]}{$_GET["posto"]}-{$nomeDoc}.pdf";
                            $pathArquivo = $path . $nomeArquivo;

                            exec(realpath(dirname(__FILE__))."\\geradoc\\GeraDoc.exe"
                                . " -unidade={$_SESSION[config::getSessao()]["unidade"]}"
                                . " -empresa={$_SESSION[config::getSessao()]["empresa_ativa"]}"
                                . " -posto={$_GET["posto"]}"
                                . " -data=" . funcoes::converterData($_GET["data"])
                                . " -documento={$nomeDoc}");
                                
                            if (file_exists($pathArquivo)){
                                $retorno["arquivo"] = base64_encode($nomeArquivo);
                            }else{
                                $retorno["codigo"] = 5;
                                $retorno["mensagem"] = "N??o foi poss??vel gerar o arquivo!";
                            }
                        }else{
                            $retorno["codigo"] = 4;
                            $retorno["mensagem"] = "Informe uma data v??lida de um levantamento!";
                        }
                    }else{
                        $retorno["codigo"] = 3;
                        $retorno["mensagem"] = "Informe um posto de trabalho v??lido!";
                    } 
                } else {
                    if (!isset($_GET['posto'])) {
                        $retorno["codigo"] = 1;
                    } else
                        if (!isset($_GET['data'])) {
                            $retorno["codigo"] = 2;
                        }
                        
                    $retorno["mensagem"] = "Alguns par??metros n??o foram encontrados!";
                }
                
                echo json_encode($retorno);
            } else
                if ($acao == "baixar"){
                    $arquivo = base64_decode($_GET['arquivo']);
                    $pathArquivo = realpath(dirname(__FILE__))."\\geradoc\\DOC\\" . $arquivo;
                    
                    header("Content-Type: application/pdf");
                    header("Content-disposition: attachment; filename={$arquivo}");
                    readfile($pathArquivo);
                    unlink($pathArquivo);
                                
                }
            
        die;
    }
    
?>

<div class="page-head">
    <h2>Documentos</h2>
    <ol class="breadcrumb">
        <li>In??cio</li>
        <li class="active">Documentos</li>
    </ol>
</div>
<div class="cl-mcont">
    <div class="block-flat">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#documentos-funcionario" data-toggle="tab">Funcion??rio</a></li>
            <li><a href="#documentos-ppra-pcmso" data-toggle="tab">PPRA/PCMSO</a></li>
        </ul>
        <div class="tab-content" style="margin-bottom: 10px;">
            <div id="documentos-funcionario" class="tab-pane active cont">
                <div class="form-group-sm">
                    <div class="col-sm-12 col-md-12 col-lg-6" style="height: 58px;">
                        <label>Funcion??rio:</label>
                        <div class="input-group">
                            <input type="hidden" id="form-funcionario-input-funcionario"<?php if (isset($_GET["matricula"])) echo "value=\"{$_GET["matricula"]}\""; ?>>
                            <input type="text" id="form-funcionario-input-nome-funcionario" readonly class="form-control input-sm" style="background-color: #fff; border-radius: 3px;"<?php if (isset($_GET["nome"])) echo "value=\"{$_GET["nome"]}\""; ?>>
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-funcionario-button-pesquisa-funcionario" style="height: 30px; padding-top: 4px">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>  
                <div class="form-group-sm">
                    <div class="col-sm-6 col-md-6 col-lg-3" style="height: 58px;">
                        <label>Situa????o:</label>
                        <select class="form-control input-sm" id="form-funcionario-select-situacao">
                            <option value="A"<?php if (isset($_GET["situacao"]) && ($_GET["situacao"] == "A")) echo "selected"; ?>>ABERTOS</option>
                            <option value="E"<?php if (isset($_GET["situacao"]) && ($_GET["situacao"] == "E")) echo "selected"; ?>>ENTREGUES</option>
                            <option value="T"<?php if (isset($_GET["situacao"]) && ($_GET["situacao"] == "T")) echo "selected"; ?>>TODOS</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 text-right" style="height: 58px; padding-top: 25px; padding-left: 0px;">
                        <button type="button" class="btn btn-primary" id="form-funcionario-button-pesquisar-documentos-funcionario">
                            Pesquisar
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div> <br>
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-6" id="div-funcionario-documentos-funcionario"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div id="documentos-ppra-pcmso" class="tab-pane cont">
                <div class="form-group-sm">
                    <div class="col-sm-12 col-md-12 col-lg-6" style="height: 58px;">
                        <label>Posto:</label>
                        <div class="input-group">
                            <input type="hidden" id="form-documentos-input-posto">
                            <input type="text" id="form-documentos-input-descricao-posto" readonly class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" id="form-documentos-button-pesquisa-posto" style="height: 30px; padding-top: 4px">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group-sm">
                    <div class="col-sm-6 col-md-6 col-lg-3" style="height: 58px;">
                        <label>Data:</label>
                        <div class="input-group">
                            <input type="text" id="form-documentos-input-data" readonly class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-xs" id="form-documentos-button-pesquisa-levantamento" style="height: 30px; padding-top: 4px">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 text-right" style="height: 58px; padding-top: 25px; padding-left: 0px;">
                        <button type="button" class="btn btn-primary" id="form-documentos-button-gerar-ppra">
                            Gerar PPRA
                        </button>
                        <button type="button" class="btn btn-primary" id="form-documentos-button-gerar-pcmso">
                            Gerar PCMSO
                        </button>
                    </div>
                    <div class="col-sm-12">
                        <span class="note">* A gera????o desse arquivo pode demorar alguns segudos, por favor aguarde!</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="div-funcionario-download-funcionario" style="display: none"></div>
<div id="div-documentos-download" style="display: none"></div>
<script>
    var paginaAtual = 1;
    
    $(document).ready(function(){
        $('.icheck').iCheck({
            radioClass: 'iradio_square-blue'
        });
        <?php
            if (isset($_GET["pesquisa"]) && ($_GET["pesquisa"] == "1"))
                echo "pesquisarDocumentosFuncionario('1');";
        ?>
    });
    
    $('#form-funcionario-button-pesquisa-funcionario').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa funcion??rio');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('funcionario-pesquisa.php', {'prefixo': 'form-funcionario'});
    });  
    
    $('#form-funcionario-button-pesquisar-documentos-funcionario').on('click', function(){
        pesquisarDocumentosFuncionario('1');
    });
    
    $('#form-documentos-button-pesquisa-posto').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa posto de trabalho');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('posto-trabalho-pesquisa.php', {'prefixo': 'form-documentos'});
    });
    
    $('#form-documentos-button-pesquisa-levantamento').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa levantamento');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('levantamento-pesquisa.php', {'prefixo': 'form-documentos'});
    });
    
    $('#form-documentos-button-gerar-ppra').on('click', function(){
        $('#form-documentos-button-gerar-ppra').addClass('disabled');
        $('#form-documentos-button-gerar-pcmso').addClass('disabled');
        
        var htmlOrig = $('#form-documentos-button-gerar-ppra').text();
        $('#form-documentos-button-gerar-ppra').html('<i class="fa fa-spinner fa-spin"></i> Aguarde...');
        
        $.ajax({
            url: 'documentos.php?a=gerar-ppra&posto=' + $('#form-documentos-input-posto').val() + '&data=' + $('#form-documentos-input-data').val(),
            dataType: 'json',
            cache: false,
            async: true,
            success: function(data){
                if (data.codigo != ''){
                    $.gritter.add({ title: 'Ops!', text: data.mensagem + ' #' + data.codigo, class_name: 'danger' });
                }else{
                    var url = 'documentos.php?a=baixar&arquivo=' + data.arquivo;
                    $('#div-documentos-download').html('<iframe id="iframe-ppra" style="display:none" src="' + url + '"></iframe>');
                }
                $('#form-documentos-button-gerar-ppra').html(htmlOrig);
                $('#form-documentos-button-gerar-ppra').removeClass('disabled');
                $('#form-documentos-button-gerar-pcmso').removeClass('disabled');
            },
            error: function(){
                $.gritter.add({ title: 'Ops!', text: 'N??o foi poss??vel realizar a opera????o!', class_name: 'danger' });
                $('#form-documentos-button-gerar-ppra').html(htmlOrig);
                $('#form-documentos-button-gerar-ppra').removeClass('disabled');
                $('#form-documentos-button-gerar-pcmso').removeClass('disabled');
            }
        });
    });
    
    $('#form-documentos-button-gerar-pcmso').on('click', function(){
        $('#form-documentos-button-gerar-ppra').addClass('disabled');
        $('#form-documentos-button-gerar-pcmso').addClass('disabled');
        
        var htmlOrig = $('#form-documentos-button-gerar-pcmso').text();
        $('#form-documentos-button-gerar-pcmso').html('<i class="fa fa-spinner fa-spin"></i> Aguarde...');
        
        $.ajax({
            url: 'documentos.php?a=gerar-pcmso&posto=' + $('#form-documentos-input-posto').val() + '&data=' + $('#form-documentos-input-data').val(),
            dataType: 'json',
            cache: false,
            async: true,
            success: function(data){
                if (data.codigo != ''){
                    $.gritter.add({ title: 'Ops!', text: data.mensagem + ' #' + data.codigo, class_name: 'danger' });
                }else{
                    var url = 'documentos.php?a=baixar&arquivo=' + data.arquivo;
                    $('#div-documentos-download').html('<iframe id="iframe-ppra" style="display:none" src="' + url + '"></iframe>');
                }
                $('#form-documentos-button-gerar-pcmso').html(htmlOrig);
                $('#form-documentos-button-gerar-ppra').removeClass('disabled');
                $('#form-documentos-button-gerar-pcmso').removeClass('disabled');
            },
            error: function(){
                $.gritter.add({ title: 'Ops!', text: 'N??o foi poss??vel realizar a opera????o!', class_name: 'danger' });
                $('#form-documentos-button-gerar-pcmso').html(htmlOrig);
                $('#form-documentos-button-gerar-ppra').removeClass('disabled');
                $('#form-documentos-button-gerar-pcmso').removeClass('disabled');
            }
        });
    });
        
    function pesquisarDocumentosFuncionario(pagina){
        $('#div-funcionario-documentos-funcionario').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-funcionario-documentos-funcionario').load('documentos.php', { 'a': 'pesquisar-documentos',
                                                                                'funcionario': $('#form-funcionario-input-funcionario').val(),
                                                                                'situacao': $('#form-funcionario-select-situacao').find('option:selected').val(),
                                                                                'pagina': pagina
                                                                              }, function(){
                                                                                  $('#div-funcionario-documentos-funcionario a').each(function(){
                                                                                      $(this).on('click', function(){
                                                                                          var url = 'documentos.php?a=gerar&funcionario=' + $('#form-funcionario-input-funcionario').val() + '&numero=' + $(this).attr('numero');

                                                                                          if ($(this).attr('acao') == 'todos'){
                                                                                              var html = ''; 
                                                                                              html += '<iframe id="iframe-aso" style="display:none" src="' + url + '&documento=aso' + '"></iframe>';
                                                                                              html += '<iframe id="iframe-fichamedica" style="display:none" src="' + url + '&documento=fichamedica' + '"></iframe>';
                                                                                              html += '<iframe id="iframe-fichamedicaocup" style="display:none" style="display:none" src="' + url + '&documento=fichamedicaocup' + '"></iframe>';
                                                                                              html += '<iframe id="iframe-acuidadevisual" style="display:none" style="display:none" src="' + url + '&documento=acuidadevisual' + '"></iframe>';
                                                                                              html += '<iframe id="iframe-audiometria" style="display:none" style="display:none" src="' + url + '&documento=audiometria' + '"></iframe>';

                                                                                              $('#div-funcionario-download-funcionario').html(html);
                                                                                          }else{
                                                                                              if ($('#iframe-' + $(this).attr('acao')))
                                                                                                  $('#iframe-' + $(this).attr('acao')).remove();

                                                                                              $('#div-funcionario-download-funcionario').html('<iframe id="iframe-' + $(this).attr('acao') + '" style="display:none" src="' + url + '&documento=' + $(this).attr('acao') + '"></iframe>');
                                                                                          }
                                                                                      });
                                                                                  });
                                                                              });
    }
</script>