<?php
    require_once("class/config.inc.php");
    require_once("class/fatura.class.php");
    require_once("class/faturaitem.class.php");
    require_once("class/titreceb.class.php");
    require_once("class/empresaplano.class.php");
    require_once("class/funcoes.class.php");
    require_once("class/tabela.class.php");

    config::verificaLogin();
    
    if ($_SESSION[config::getSessao()]["faturas"] != "S"){
        header("Location: 403.php");
        die;
    }

    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "pesquisar"){
            $fa = new fatura();
            if ($fa->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                  "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]), 3, array("A.NUMERO" => "DESC"))){
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Número", array("style" => "font-weight: bold; text-align: center;"));
                
                if (!funcoes::acessoCelular()){
                    $cab->addItem("Emissão", array("style" => "font-weight: bold; text-align: center;"));
                    $cab->addItem("Mes/Ano", array("style" => "font-weight: bold; text-align: center;"));
                }
                
                $cab->addItem("Vencimento", array("style" => "font-weight: bold; text-align: center;"));
                $cab->addItem("Valor", array("style" => "font-weight: bold; text-align: center;"));
                
                if (!funcoes::acessoCelular())
                    $cab->addItem("Ações", array("style" => "width: 30px; font-weight: bold; text-align: center;"));
                
                $tab->addCabecalho($cab);
                
                for ($i = 0; $i < $fa->getTotalLista(); $i++){
                    $idTr = md5($fa->getItemLista($i)->getNumero().date("Y-m-d H:i:s"));
                    
                    $reg = new registro(array("class" => "no-border-y", "style" => "\" tr=\"item\" idTr=\"{$idTr}"));
                    $reg->addItem($fa->getItemLista($i)->getNumero(), array("style" => "text-align: center;"));
                    if (!funcoes::acessoCelular()){
                        $reg->addItem(funcoes::converterData($fa->getItemLista($i)->getDataEmissao()), array("style" => "text-align: center;"));
                        $reg->addItem($fa->getItemLista($i)->getAnoMesFaturamento(), array("style" => "text-align: center;"));
                    }
                    $reg->addItem(funcoes::converterData($fa->getItemLista($i)->getDataVencimento()), array("style" => "text-align: center;"));
                    $reg->addItem(number_format($fa->getItemLista($i)->getValor(), 2, ",", "."), array("style" => "text-align: right;"));
                    
                    if (!funcoes::acessoCelular())
                        $reg->addItem("<a class=\"btn btn-default btn-xs\" href=\"javascript: void(0)\" idtr=\"{$idTr}\" acao=\"abrir\">"
                                        . "<i class=\"fa fa-search\"></i>"
                                    . "</a>");
                    
                    $tab->addRegistro($reg);
                    
                    // busca itens da fatura                                        
                    $reg = new registro(array("class" => "no-border-y", "style" => "display:none; background-color: #fff\" tr=\"detalhe\" id=\"{$idTr}"));
                    $detalhe = "";
                    $fi = new faturaitem();
                    if ($fi->buscar(array("A.UNIDADE" => $fa->getItemLista($i)->getUnidade()->getCodigo(),
                                          "A.EMPRESA" => $fa->getItemLista($i)->getEmpresa()->getCodigo(),
                                          "A.DATAEMISSAO" => $fa->getItemLista($i)->getDataEmissao(),
                                          "A.NUMERO"      => $fa->getItemLista($i)->getNumero()), null, array("A.ITEM" => "ASC"))){
                        
                        $tab2 = new tabela(array("class" => "table no-border table-hover table-responsive"));
                        $cab2 = new cabecalho();
                        $cab2->addItem("Item", array("style" => "font-weight: bold; text-align: center;"));
                        $cab2->addItem("Descrição", array("style" => "font-weight: bold;"));
                        
                        if (!funcoes::acessoCelular()){
                            $cab2->addItem("Valor unitário", array("style" => "font-weight: bold; text-align: right;"));
                            $cab2->addItem("Quantidade", array("style" => "font-weight: bold; text-align: right;"));
                        }
                        
                        $cab2->addItem("Valor total", array("style" => "font-weight: bold; text-align: right;"));
                        $tab2->addCabecalho($cab2);
                        
                        for ($j = 0; $j < $fi->getTotalLista(); $j++){
                            $reg2 = new registro();
                            $reg2->addItem($fi->getItemLista($j)->getItem(), array("style" => "text-align: center;"));
                            $reg2->addItem(utf8_encode($fi->getItemLista($j)->getDescricao()));
                            
                            if (!funcoes::acessoCelular()){
                                $reg2->addItem(number_format($fi->getItemLista($j)->getValorUnitario(), 2, ",", "."), array("style" => "text-align: right;"));
                                $reg2->addItem(number_format($fi->getItemLista($j)->getQuantidade(), 0), array("style" => "text-align: right;"));
                            }
                            
                            $reg2->addItem(number_format($fi->getItemLista($j)->getValorTotal(), 2, ",", "."), array("style" => "text-align: right;"));
                            $tab2->addRegistro($reg2);
                        }                        
                        $detalhe .= "<div class=\"col-md-7\"><label>Detalhes da fatura</label>".$tab2->gerar()."</div>";
                    }else{
                        $detalhe = "<div class=\"col-md-7\"><h5>Nenhum item da fatura foi encontrado!</h5></div>";
                    }
                    
                    $ti = new titreceb();
                    if ($ti->buscar(array("A.UNIDADE" => $fa->getItemLista($i)->getUnidade()->getCodigo(),
                                          "A.EMPRESA" => $fa->getItemLista($i)->getEmpresa()->getCodigo(),
                                          "A.TITULO"  => $fa->getItemLista($i)->getNumero(),
                                          "A.PARCELA" => "1",
                                          "A.DATAEMISSAO" => $fa->getItemLista($i)->getDataEmissao(),
                                          "A.TIPOTITULO"  => "03",
                                          //"A.CONTABOLETO" => array("NOT NULL", "IS"),
                                          //"A.CONTABOLETO" => array("", "<>"),
                                          //"A.NOSSONUMERO" => array("NOT NULL", "IS"),
                                          "A.SITUACAO"    => array(array("A", "P"), "IN")))){
                        
                        $tab2 = new tabela(array("class" => "table no-border table-hover table-responsive"));
                        $cab2 = new cabecalho();
                        if (!funcoes::acessoCelular())
                            $cab2->addItem("Parcela", array("style" => "font-weight: bold; text-align: center;"));
                        
                        $cab2->addItem("Vencimento", array("style" => "font-weight: bold; text-align: center;"));
                        $cab2->addItem("Valor", array("style" => "font-weight: bold; text-align: right;"));
                        $cab2->addItem("Situação", array("style" => "font-weight: bold; text-align: center;"));
                        $cab2->addItem("", array("style" => "width: 32px;"));
                        $tab2->addCabecalho($cab2);
                        
                        for ($j = 0; $j < $ti->getTotalLista(); $j++){
                            $reg2 = new registro();
                            if (!funcoes::acessoCelular())
                                $reg2->addItem($ti->getItemLista($j)->getParcela(), array("style" => "text-align: center;"));
                            $reg2->addItem(funcoes::converterData($ti->getItemLista($j)->getDataVencimento()), array("style" => "text-align: center;"));
                            $reg2->addItem(number_format($ti->getItemLista($j)->getValorTitulo(), 2, ",", "."), array("style" => "text-align: right;"));
                            
                            $situacao = "";
                            if ($ti->getItemLista($j)->getSituacao() == "A")
                                $situacao = "ABERTO";
                            else
                                if ($ti->getItemLista($j)->getSituacao() == "P")
                                    $situacao = "PAGO";
                            
                            $reg2->addItem($situacao, array("style" => "text-align: center;"));
                            
                            $classeBotao = "";
                            $linkBotao   = "boleto.php?fatura={$ti->getItemLista($j)->getTitulo()}&emissao={$ti->getItemLista($j)->getDataEmissao()}&parcela={$ti->getItemLista($j)->getParcela()}";
                            
                            $diasEmAtraso = time() - strtotime($ti->getItemLista($j)->getDataVencimento());
                            $diasEmAtraso = floor($diasEmAtraso / 60 / 60 / 24);
                            
                            if (($ti->getItemLista($j)->getSituacao() != "A") || ($diasEmAtraso > 30)){
                                $classeBotao = " disabled";
                                $linkBotao   = "javascript: void(0)";
                            }
                            
                            $botao = "<a class=\"btn btn-primary btn-xs {$classeBotao}\" href=\"{$linkBotao}\" target=\"_blank\">"
                                        . "<i class=\"fa fa-print\"></i>"
                                    . "</a>";
                            
                            $reg2->addItem($botao, array("style" => "text-align: center"));
                            $tab2->addRegistro($reg2);
                        }                        
                        $detalhe .= "<div class=\"col-md-5\">"
                                        . "<label>Detalhes de cobrança</label>"
                                        . $tab2->gerar()
                                    . "</div>";
                    }else{
                        $detalhe = "<div class=\"col-md-5\"><h5>Nenhuma cobrança foi encontrada!</h5></div>";
                    }
                    
                    $detalhe = "<div class=\"form-group\">"
                                    . "{$detalhe}"
                                . "</div>";
                                    
                    $reg->addItem($detalhe, array("colspan" => (funcoes::acessoCelular() ? "4" : "6")));
                    $tab->addRegistro($reg);
                    
                }
                
                echo $tab->gerar();
            }else
                echo "<h5>Nenhum resultado foi encontrado!</h5>";
        }
        
        die;
    }
?>

<div class="page-head">
    <h2>Faturas</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li class="active">Faturas</li>
    </ol>
</div>
<div class="cl-mcont">
    <div class="block-flat">
        <div class="form-group-sm" style="margin-left: 10px;">
            <div class="col-md-12">
                <h4>Planos</h4>
                <?php
                    $ep = new empresaplano();
                    if ($ep->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]), 1,
                                    array("A.POSTOTRABALHO" => "ASC"))){
                        
                        $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                        $cab = new cabecalho(array("class" => "no-border"));
                        $cab->addItem("Posto", array("style"=> "font-weight: bold"));
                        $cab->addItem("Plano", array("style"=> "font-weight: bold"));
                        $cab->addItem("Tipo", array("style"=> "font-weight: bold"));
                        $cab->addItem("Cobertura", array("style"=> "font-weight: bold"));
                        $tab->addCabecalho($cab);
                        
                        for ($i = 0; $i < $ep->getTotalLista(); $i++){
                            $reg = new registro(array("class" => "no-border-y"));
                            if (funcoes::acessoCelular())
                                $reg->addItem($ep->getItemLista($i)->getPostoTrabalho()->getCodigo());
                            else
                                $reg->addItem(utf8_encode("{$ep->getItemLista($i)->getPostoTrabalho()->getCodigo()} - {$ep->getItemLista($i)->getPostoTrabalho()->getDescricao()}"));
                                
                            $reg->addItem(utf8_encode($ep->getItemLista($i)->getPlano()->getDescricao()));
                            $reg->addItem(utf8_encode($ep->getItemLista($i)->getPlano()->getTipo()->getDescricao()));
                            switch ($ep->getItemLista($i)->getPlano()->getTipo()->getCobertura()){
                                case 1: $reg->addItem('TOTAL');
                                        break;
                                case 2: $reg->addItem('PARCIAL');
                                        break;
                                case 3: $reg->addItem('PCMSO');
                                        break;
                                default: $reg->addItem('NÃO TEM');
                                        break;
                                    
                            }
                            
                            $tab->addRegistro($reg);
                        }
                        
                        echo $tab->gerar();
                    }else{
                        echo "<h5>Nenhum plano foi encontrado!</h5>";
                    }
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="block-flat">
        <div class="form-group-sm" style="margin-left: 10px;">
            <div class="col-md-12">
                <h4>Faturas</h4>
                <div id="div-faturas"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        pesquisarFaturas();
    });
    
    function pesquisarFaturas(){
        $('#div-faturas').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-faturas').load('faturas.php', 
                                { 'a': 'pesquisar' }, function(){
                                    <?php if (funcoes::acessoCelular()): ?>
                                        $('#div-faturas tr[tr="item"]').each(function(){
                                            $(this).on('click', function(){
                                                $('#div-modal').modal();
                                                $('#div-modal .modal-header h3').text('Detalhe da fatura');
                                                $('#div-modal .modal-body').html($('#'+$(this).attr('idtr')).html());
                                            });
                                        });
                                    <?php endif; ?>
                                    
                                    <?php if (!funcoes::acessoCelular()): ?>
                                        $('#div-faturas a[acao="abrir"]').each(function(){
                                            $(this).on('click', function(){
                                                $('#div-faturas table tbody tr[tr="detalhe"]').fadeOut(0);
                                                $('#' + $(this).attr('idtr')).fadeIn(500);
                                            });   
                                        }); 
                                    <?php endif; ?>                                   
                                });
    }
</script>