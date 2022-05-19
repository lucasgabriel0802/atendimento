<?php
    require_once("class/config.inc.php");
    require_once("class/levantamento.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    
    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "pesquisa"){
            if (!isset($_POST["posto"]) || ($_POST["posto"] == "")){
                echo "<h4>Selecione o posto de trabalho!</h4>";
                die;
            }            
            
            $pagina = 1;
            $posto  = $_POST["posto"];
            
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
            
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;
            
            $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                            "A.POSTOTRABALHO" => $posto,
                            "A.STATUS" => "1");
            
            $le = new levantamento();
            if ($le->buscar($filtro, $limite, array("A.DATA" => "DESC"))){
                $ultimaData = date('d/m/Y', strtotime($le->getItemLista(0)->getData() . ' + 1 year'));
                $totalRegistros = $le->getTotalRegistros();
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Data", array("style" => "font-weight: bold"));
                $cab->addItem("Status", array("style" => "font-weight: bold"));
                $cab->addItem("", array("style" => "width: 20px"));
                $tab->addCabecalho($cab);
                
                for ($i = 0; $i < $le->getTotalLista(); $i++){
                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem(funcoes::converterData($le->getItemLista($i)->getData()));
                    $reg->addItem(($le->getItemLista($i)->getStatus() == '0') ? 'Em andamento' : 'OK');
                    $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" "
                                  . "data=\"" . funcoes::converterData($le->getItemLista($i)->getData()) . "\""
                                  . "ultima-data=\"" . $ultimaData . "\""
                                  . "identificacao=\"".utf8_encode($le->getItemLista($i)->getIdentificacao())."\"><i class=\"fa fa-check\"></i></a>");
                    $tab->addRegistro($reg);
                }
                echo $tab->gerar();
                funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarLevantamento('<pagina>');");
            }else
                echo "<h5>Nenhum resultado encontrado!</h5>";
        }
        
        die;
    }
?>
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-div-pesquisa-levantamento"></div>
<script>
    $('#modal-button-pesquisar').on('click', function(){
        pesquisarLevantamento('1');
    });
    
    $('document').ready(function(){
        $('#modal-button-pesquisar').click();
        
        pesquisarLevantamento('1');
    });
    
    function pesquisarLevantamento(pagina){
        <?php
            $prefixo = "form";
            if (isset($_POST["prefixo"]))
                $prefixo = $_POST["prefixo"];
        ?>
        $('#modal-div-pesquisa-levantamento').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#modal-div-pesquisa-levantamento').load('levantamento-pesquisa.php', 
                                                    {'a': 'pesquisa', 
                                                     'nome': $('#modal-input-nome-levantamento').val(), 
                                                     'posto': $('#<?php echo $prefixo; ?>-input-posto').val(),
                                                     'pagina': pagina 
                                                    }, function(){
                                                        $('#modal-div-pesquisa-levantamento a').each(function(){
                                                            $(this).on('click', function(){
                                                                $('#<?php echo $prefixo; ?>-input-data').val($(this).attr('data'));
                                                                
                                                                <?php if ($prefixo == 'form-e-social-s1060'): ?>
                                                                    $('#<?= $prefixo ?>-input-data-vencimento').val($(this).attr('ultima-data'));
                                                                <?php endif; ?>
                                                                
                                                                $('#div-modal').modal('toggle');
                                                            });
                                                        });
                                                    });
    }
</script>