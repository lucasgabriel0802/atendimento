<?php
    require_once("class/config.inc.php");
    require_once("class/postotrabalho.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    
    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "pesquisa"){

            $pagina = 1;
            $descricao = "";
            
            if (isset($_POST["descricao"]))
                $descricao = $_POST["descricao"];
            
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
            
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;
            
            $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                            "COALESCE(A.ATIVO, 'S')" => "S");
            
            if ($descricao != "")
                $filtro["A.DESCRICAO"] = array($descricao, "CONTAINING");
            
            $pt = new postotrabalho();
            if ($pt->buscar($filtro, $limite, array("A.DESCRICAO" => "ASC"))){
                $totalRegistros = $pt->getTotalRegistros();
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Código", array("style" => "width: 65px; font-weight: bold"));
                $cab->addItem("Descrição", array("style" => "font-weight: bold"));
                $cab->addItem("", array("style" => "width: 20px"));
                $tab->addCabecalho($cab);
                
                for ($i = 0; $i < $pt->getTotalLista(); $i++){
                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem($pt->getItemLista($i)->getCodigo());
                    $reg->addItem(utf8_encode($pt->getItemLista($i)->getDescricao()));
                    $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" "
                                  . "codigo=\"{$pt->getItemLista($i)->getCodigo()}\""
                                  . "descricao=\"".utf8_encode($pt->getItemLista($i)->getDescricao())."\"><i class=\"fa fa-check\"></i></a>");
                    $tab->addRegistro($reg);
                }
                echo $tab->gerar();
                funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarPosto('<pagina>');");
            }else
                echo "<h5>Nenhum resultado encontrado!</h5>";
        }
        
        die;
    }
?>
<div class="form-group-sm">
    <div class="col-md-12">
        <label>Posto de trabalho:</label>
        <div class="input-group">
            <input type="input" id="modal-input-descricao-posto" class="form-control input-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
</div> 
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-div-pesquisa-posto"></div>
<script>
    $('#modal-button-pesquisar').on('click', function(){
        pesquisarPosto('1');
    });
    $('#modal-input-descricao-posto').keypress(function(event){
        if (event.which == 13)
           $('#modal-button-pesquisar').click();
    });
    
    $('document').ready(function(){
        $('#modal-button-pesquisar').click();
    });
    
    function pesquisarPosto(pagina){
        $('#modal-div-pesquisa-posto').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#modal-div-pesquisa-posto').load('posto-trabalho-pesquisa.php', 
                                                    {'a': 'pesquisa', 
                                                     'descricao': $('#modal-input-descricao-posto').val(), 
                                                     'pagina': pagina 
                                                    }, function(){
                                                        $('#modal-div-pesquisa-posto a').each(function(){
                                                            $(this).on('click', function(){
                                                                <?php
                                                                    $prefixo = "form";
                                                                    if (isset($_POST["prefixo"]))
                                                                        $prefixo = $_POST["prefixo"];
                                                                ?>
                                                                if ($('#<?php echo $prefixo; ?>-input-posto').val() != $(this).attr('codigo')){
                                                                    $('#<?php echo $prefixo; ?>-input-novo-setor').val('');
                                                                    $('#<?php echo $prefixo; ?>-input-nome-novo-setor').val('');
                                                                    $('#<?php echo $prefixo; ?>-input-nova-funcao').val('');
                                                                    $('#<?php echo $prefixo; ?>-input-nome-nova-funcao').val('');
                                                                }
                                                                
                                                                $('#<?php echo $prefixo; ?>-input-posto').val($(this).attr('codigo'));
                                                                $('#<?php echo $prefixo; ?>-input-descricao-posto').val($(this).attr('descricao'));
                                                                $('#div-modal').modal('toggle');
                                                            });
                                                        });
                                                    });
    }
</script>