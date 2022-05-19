<?php
    require_once("class/config.inc.php");
    require_once("class/agenda.class.php");
    require_once("class/empresa.class.php");
    require_once("class/empresamedico.class.php");
    require_once("class/medico.class.php");
    require_once("class/funcoes.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcaso.class.php");

    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "pesquisar"){
            $data   = date("d/m/Y");
            $pagina = 1;

            if (isset($_POST["data"]))
                if (funcoes::validarData($_POST["data"]))
                    $data = $_POST["data"];
                
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
                
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;

            $medicos = array();
            $em = new empresamedico();
            if ($em->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                  "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]))){
                $temp = array();
                for ($i = 0; $i < $em->getTotalLista(); $i++)
                    array_push($temp, $em->getItemLista($i)->getMedico()->getCodigo());

                $medicos = array($temp, "IN");
            }else{
                $me = new medico();
                if ($me->buscarMedicoNaoFono()){
                    $temp = array();
                    for ($i = 0; $i < $me->getTotalLista(); $i++)
                        array_push($temp, $me->getItemLista($i)->getCodigo());

                    $medicos = array($temp, "IN");
                }
            }

            $data = str_replace("/", ".", $data);
            
            $filtro = [
                "A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                "A.DATA" => $data
            ];
            
            if (count($medicos) > 0)
                $filtro["B.CODIGOMEDICO"] = $medicos;
            
            $ag = new agenda();
            if ($ag->buscar($filtro, $limite, array("A.HORA" => "ASC"))){
                $totalRegistros = $ag->getTotalRegistros();
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Horário", array("style" => "width: 65px; font-weight: bold; text-align: center;"));
                $cab->addItem("Funcionário", array("style" => "font-weight: bold"));
                
                if (!funcoes::acessoCelular()){
                    $cab->addItem("Tipo", array("style" => "width: 150px; font-weight: bold"));
                    $cab->addItem("Setor", array("style" => "font-weight: bold"));
                    $cab->addItem("Função", array("style" => "font-weight: bold"));
                    
                    if ($_SESSION[config::getSessao()]["documentos"] == "S")
                        $cab->addItem("Ações", array("style" => "width: 170px; font-weight: bold; text-align: center;"));
                    else
                        $cab->addItem("Ações", array("style" => "width: 110px; font-weight: bold; text-align: center;"));
                }else
                    $cab->addItem("", array("style" => "width: 90px;"));
                
                $tab->addCabecalho($cab);

                for ($i = 0; $i < $ag->getTotalLista(); $i++){
                    $propCelular = "";
                    
                    //if (funcoes::acessoCelular())
                        //$propCelular = "celular\" codigo=\"{$ag->getItemLista($i)->getCodigo()}\" hora=\"".$ag->getItemLista($i)->getHora();
                    
                    $reg = new registro(array("class" => "no-border-y", "acesso" => $propCelular));
                    $reg->addItem($ag->getItemLista($i)->getHora(), array("style" => "font-weight: bold; text-align: center;"));
                    
                    if (funcoes::acessoCelular())
                        $reg->addItem(utf8_encode(funcoes::separarPrimeiroUltimoNome($ag->getItemLista($i)->getFuncionario()->getNome())));
                    else
                        $reg->addItem(utf8_encode($ag->getItemLista($i)->getFuncionario()->getNome()));
                    
                    if (!funcoes::acessoCelular()){
                        if ($ag->getItemLista($i)->getTipo() == "A")
                            $reg->addItem("ADMISSIONAL");
                        else
                            if ($ag->getItemLista($i)->getTipo() == "D")
                                $reg->addItem("DEMISSIONAL");
                            else
                                if ($ag->getItemLista($i)->getTipo() == "P")
                                    $reg->addItem("PERIÓDICO");
                                else
                                    if ($ag->getItemLista($i)->getTipo() == "M")
                                        $reg->addItem("MUDANÇA DE FUNÇÃO");
                                    else
                                        if ($ag->getItemLista($i)->getTipo() == "R")
                                            $reg->addItem("RETORNO AO TRABALHO");
                                        else
                                            if ($ag->getItemLista($i)->getTipo() == "C")
                                                $reg->addItem("CONSULTA");
                                            else
                                                if ($ag->getItemLista($i)->getTipo() == "I")
                                                    $reg->addItem("INDEFINIDO");

                        $reg->addItem(utf8_encode($ag->getItemLista($i)->getSetor()->getNome()));
                        $reg->addItem(utf8_encode($ag->getItemLista($i)->getFuncao()->getNome()));

                    }
                    
                    $acaoCancelar = "acao=\"cancelar\"";
                    $desabilitarCancelar = "";
                    
                    $dataAgora = strtotime(date("Y-m-d H:i:s"));
                    $dataHoraAgenda = strtotime("{$ag->getItemLista($i)->getData()} {$ag->getItemLista($i)->getHora()}");

                    if ($dataAgora > $dataHoraAgenda){
                        // se data e hora menor que atual não deixa editar nem cancelar
                        $acaoCancelar   = "";
                        $desabilitarCancelar = "disabled";
                    }else{                    
                        if (($ag->getItemLista($i)->getHoraChegada() != "") || ($ag->getItemLista($i)->getStatus() == "F")){
                            // se o funcionário já chegou ou foi marcado como falta não permite editar nem cancelar
                            $acaoCancelar   = "";
                            $desabilitarCancelar = "disabled";
                        }else{
                            $intervaloAgoraEAgenda = $dataHoraAgenda - $dataAgora; // retorna em segundos
                            $intervaloAgoraEAgenda = $intervaloAgoraEAgenda / 60; // converte pra minutos

                            if ($ag->getItemLista($i)->getEmpresa()->getIntervaloCancelamento() > 0)
                                if ($intervaloAgoraEAgenda < $ag->getItemLista($i)->getEmpresa()->getIntervaloCancelamento()){
                                    // se tiver tempo de cancelamento e intervalo entre data agora e agendamento
                                    // maior que o intervalo para cancelamento não deixa cancelar
                                    $acaoCancelar   = "";
                                    $desabilitarCancelar = "disabled";
                                }
                        }
                    }                    
                    
                    $classeBotaoVisualizar = "btn btn-default btn-xs";
                    $classeBotaoCancelar   = "btn btn-danger btn-xs";
                    $classeBotaoImprimir   = "btn btn-primary btn-xs";
                    $classeBotaoDocumentos = "btn btn-primary btn-xs";
                    $classeBotaoGerarASO   = "btn btn-primary btn-xs";
                    
                    if (funcoes::acessoCelular()){
                        $classeBotaoVisualizar = "\" style=\"padding: 0px 10px;";
                        $classeBotaoCancelar   = "\" style=\"padding: 0px 10px;";
                        $classeBotaoImprimir   = "\" style=\"padding: 0px 10px;";
                        $classeBotaoDocumentos = "\" style=\"padding: 0px 10px;";
                        $classeBotaoGerarASO   = "\" style=\"padding: 0px 10px;"; 
                    }
                    
                    $codigoHora = "codigo=\"{$ag->getItemLista($i)->getCodigo()}\" hora=\"{$ag->getItemLista($i)->getHora()}\"";
                    $bVisualizar = "<a class=\"{$classeBotaoVisualizar}\" href=\"javascript: void(0)\" title=\"Visualizar\" "
                                    . "acao=\"visualizar\" {$codigoHora}>"
                                    . (funcoes::acessoCelular() ? "Ver" : "<i class=\"fa fa-search\"></i>")
                                 . "</a>";
                    $bCancelar = "<a class=\"{$classeBotaoCancelar} {$desabilitarCancelar}\" href=\"javascript: void(0)\" title=\"Cancelar\" "
                                    . "{$acaoCancelar} {$codigoHora}>"
                                    . (funcoes::acessoCelular() ? "Cancelar" : "<i class=\"fa fa-times\"></i>")
                                . "</a>";
                    $bImprimir = "<a class=\"{$classeBotaoImprimir}\" href=\"javascript: void(0)\" title=\"Imprimir\" "
                                    . "acao=\"imprimir\" {$codigoHora}>"
                                    . (funcoes::acessoCelular() ? "Imprimir" : "<i class=\"fa fa-print\"></i>")
                                . "</a>";
                                    
                    if ($_SESSION[config::getSessao()]["documentos"] == "S"){
                        $bDocumentos = "<a class=\"{$classeBotaoDocumentos}\" href=\"javascript: void(0)\" title=\"Documentos\" "
                                        . "acao=\"documentos\" matricula=\"".$ag->getItemLista($i)->getFuncionario()->getCodigo()."\" "
                                        . "nome=\"".urlencode(utf8_encode($ag->getItemLista($i)->getFuncionario()->getNome()))."\">"
                                        . (funcoes::acessoCelular() ? "Documentos" : "<i class=\"fa fa-paperclip\"></i>")
                                      . "</a>";
                        $bGerarASO = "<a class=\"{$classeBotaoGerarASO}\" href=\"javascript: void(0)\" title=\"Gerar ASO\" "
                                        . "acao=\"geraraso\" {$codigoHora} matricula=\"".$ag->getItemLista($i)->getFuncionario()->getCodigo()."\" "
                                        . "nome=\"".urlencode(utf8_encode($ag->getItemLista($i)->getFuncionario()->getNome()))."\">"
                                        . (funcoes::acessoCelular() ? "Gerar ASO" : "<i class=\"fa fa-thumb-tack\"></i>")
                                      . "</a>";
                    }else{
                        $bDocumentos = "";
                        $bGerarASO = "";
                    }
                        
                    if (funcoes::acessoCelular() && ($desabilitarCancelar != ""))
                        $bCancelar = "";
                                    
                    $botoes = "";
                    if (funcoes::acessoCelular())
                        $botoes = "<div class=\"btn-group\">
                                    <button type=\"button\" data-toggle=\"dropdown\" class=\"btn btn-default btn-xs\">Ações</button>
                                    <button type=\"button\" data-toggle=\"dropdown\" "
                                    ." style=\"width: 22px; margin-left: -3px; padding-left: 6px; float: initial\" "
                                    ." class=\"btn btn-primary btn-xs dropdown-toggle\">
                                        <span class=\"caret\"></span>
                                    </button>
                                    <ul role=\"menu\" class=\"dropdown-menu dropdown-menu-right\">
                                      <li>{$bVisualizar}</li>
                                      <li>{$bCancelar}</li>
                                      <li>{$bImprimir}</li>
                                      <li>{$bDocumentos}</li>
                                      <li>{$bGerarASO}</li>
                                    </ul>
                                  </div>";
                    else
                        $botoes = $bVisualizar.$bCancelar.$bImprimir.$bDocumentos.$bGerarASO;                                                

                    $reg->addItem($botoes);
                    
                    $tab->addRegistro($reg);
                }

                echo $tab->gerar();
                echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarAgenda('<pagina>')");
            }else
                echo "<h5>Nenhum resultado encontrado!</h5>";
        }else
            if ($acao == "geraraso"){
                $retorno = array("codigo" => 0, "mensagem" => "");
                
                if (isset($_POST["codigo"]) && isset($_POST["hora"])){
                    
                    if (is_numeric($_POST["codigo"]) && funcoes::validarHora($_POST["hora"])){
                       
                        $ag = new agenda();
                        if ($ag->buscar(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                              "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                              "A.CODIGO"        => $_POST["codigo"],
                                              "A.HORA"          => $_POST["hora"]))){
                            $ag = $ag->getItemLista(0);
                            
                            $fa = new funcaso();
                            if (!$fa->buscar(array("A.UNIDADE"   => $_SESSION[config::getSessao()]["unidade"],
                                                   "A.EMPRESA"   => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                   "A.MATRICULA" => $ag->getFuncionario()->getCodigo(),
                                                   "A.SITUACAO"  => "A"))){ // se não tiver nenhum aso em aberto então cria novo
                            
                                $fa->gerarNovoASO($_SESSION[config::getSessao()]["empresa_ativa"], 
                                                  $_SESSION[config::getSessao()]["unidade"], 
                                                  $ag->getPostoTrabalho()->getCodigo(), 
                                                  $ag->getFuncionario()->getCodigo(), 
                                                  $ag->getSetor()->getCodigo(), 
                                                  $ag->getFuncao()->getCodigo(), 
                                                  $ag->getTipo(), 
                                                  $ag->getData(), 
                                                  $retASO);

                                if ($retASO["codigo"] > 0){
                                    $retorno["codigo"] = $retASO["codigo"] + 6;
                                    $retorno["mensagem"] = $retASO["mensagem"];
                                }
                            }else{
                                $retorno["codigo"] = 6;
                                $retorno["mensagem"] = "Funcionário já tem um ASO em aberto!";
                            }
                            
                        }else{
                            $retorno["codigo"] = 5;
                            $retorno["mensagem"] = "Agendamento não encontrado!";
                        }
                        
                    }else{
                        if (!is_numeric($_POST["codigo"]))
                            $retorno["codigo"] = 3;
                        else
                            if (!funcoes::validarHora($_POST["hora"]))
                                $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Alguns parâmetros estão errados!";
                    }
                    
                }else{
                    if (!isset($_POST["codigo"]))
                        $retorno["codigo"] = 1;
                    else
                        if (!isset($_POST["codigo"]))
                            $retorno["codigo"] = 2;
                        
                    $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                }
                
                echo json_encode($retorno);
            }
        
        die;
    }
?>

<div class="page-head">
    <h2>Agenda</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li class="active">Agenda</li>
    </ol>
</div>
<div class="cl-mcont">
    <div class="block-flat">
        <div class="form-group-sm">
            <div class="col-sm-1" style="display: inline-block; <?php if (!funcoes::acessoCelular()) echo " padding-right: 0px; width: 105px;"; ?>">
                <label>Data:</label>
                <input type="input" class="form-control" id="agenda-data" readonly style="width: 85px; background-color: #FFF" value="<?php echo date("d/m/Y"); ?>">
            </div>
            <div class="col-sm-6" style="display: inline-block; padding-left: 0px; <?php if (!funcoes::acessoCelular()) echo "padding-left: 0px; padding-right: 0px; padding-top: 26px;"; ?>">
                <button type="button" class="btn btn-primary" id="button-filtrar-data">Filtrar</button>
                <button type="button" class="btn btn-primary" onclick="carregarPagina('agenda-novo-<?php echo config::getModeloAgendamento() ?>', false, '')">Agendar</button>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
            $em = new empresa();
            if ($em->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                  "A.CODIGO" => $_SESSION[config::getSessao()]["empresa_ativa"]))){
                if ($em->getItemLista(0)->getMensagemWeb() != ""): ?>
                    <div class="form-group" style="text-align: center">
                        <label>
                            <i style="color: #FFD700;" class="fa fa-warning icon-red"></i> 
                            Atenção: <?php echo utf8_encode($em->getItemLista(0)->getMensagemWeb()); ?>
                        </label>
                    </div>
                <?php
                endif;
            }
        ?>
        <div id="div-agenda">
        </div>
    </div>
</div>
<script>
    $('#button-filtrar-data').on('click', function(){ pesquisarAgenda('1'); });

    function pesquisarAgenda(pagina){
        $('#div-agenda').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-agenda').load('agenda.php', 
                                { 'a': 'pesquisar', 'data': $('#agenda-data').val(), 'pagina': pagina }, 
                                function(){ 
                                    $('#div-agenda a').tooltip();
                                    $('#div-agenda a[acao="visualizar"]').each(function(){
                                        $(this).on('click', function(){
                                            $('#div-modal').modal();
                                            $('#div-modal .modal-header h3').text('Visualizar agendamento');
                                            $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
                                            $('#div-modal .modal-body').load('agenda-visualiza.php', { 'codigo': $(this).attr('codigo'), 
                                                                                                       'hora': $(this).attr('hora') });
                                        });
                                    });
                                    $('#div-agenda a[acao="cancelar"]').each(function(){
                                        $(this).on('click', function(){
                                            $('#div-modal').modal();
                                            $('#div-modal .modal-header h3').text('Cancelar agendamento');
                                            $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
                                            $('#div-modal .modal-body').load('agenda-cancela.php', { 'codigo': $(this).attr('codigo'), 
                                                                                                     'hora': $(this).attr('hora') });
                                        });
                                    });
                                    $('#div-agenda a[acao="documentos"]').each(function(){
                                        $(this).on('click', function(){
                                            carregarPagina('documentos', true, 'matricula=' + $(this).attr('matricula') + '&nome=' + $(this).attr('nome') + '&situacao=T&pesquisa=1');
                                        });
                                    });
                                    $('#div-agenda a[acao="geraraso"]').each(function(){
                                        $(this).on('click', function(){
                                            sGet = 'matricula=' + $(this).attr('matricula') + '&nome=' + $(this).attr('nome') + '&situacao=A&pesquisa=1';
                                            $.ajax({
                                                type: 'post',
                                                url: 'agenda.php',
                                                data: 'a=geraraso&codigo=' + $(this).attr('codigo') + '&hora=' + $(this).attr('hora'),
                                                dataType: 'json',
                                                async: false,
                                                cache: false
                                            }).done(function(data){
                                                if (data.codigo > 0){
                                                    $.gritter.add({
                                                        title: 'Ops!',
                                                        text: data.mensagem + ' #' + data.codigo,
                                                        class_name: 'danger'
                                                    }); 
                                                }else
                                                    carregarPagina('documentos', true, sGet);
                                            });
                                        });
                                    });
                                    $($('#div-agenda a[acao="imprimir"]')).each(function(){
                                        $(this).on('click', function(){
                                            <?php if (!funcoes::acessoCelular()): ?>
                                                $('#div-modal-grande').modal();
                                                $('#div-modal-grande .modal-header h3').text('Requisição de exames');
                                            <?php endif; ?>
                                            $('#div-modal-grande .modal-body').html('<iframe style=" border: 0px; width: 100%; min-height: 400px;" src="relatorio.php?r=requisicao&codigo=' + $(this).attr('codigo') + '&hora=' + $(this).attr('hora') + '&a=.pdf"></iframe>');
                                            $('#div-modal-grande iframe').css('height', $(window).height() - 170);
                                        });
                                    });
                                });
    }

    $('#agenda-data').keypress(function(event){
        if (event.which == 13)
           $('#button-filtrar-data').click(); 
    });

    $(document).ready(function(){
        $("#agenda-data").datepicker();
        $('#agenda-data').mask("99/99/9999");
        
        <?php
            if (isset($_GET["data"]))
                echo "$(\"#agenda-data\").val('{$_GET["data"]}');";
        ?>
        
        $('#button-filtrar-data').click();
    });
</script>