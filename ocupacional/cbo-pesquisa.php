<?php
    require_once("class/config.inc.php");
    require_once("class/cbo.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    
    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "pesquisa"){

            $pagina = 1;
            $codigo = "";
            $descricao = "";

            if (isset($_POST["codigo"]))
                $codigo = $_POST["codigo"];
            
            if (isset($_POST["descricao"]))
                $descricao = $_POST["descricao"];
            
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
            
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;
            
            $filtro = null;
            
            if ($codigo != "")
                $filtro["A.CODIGO"] = array($codigo, "CONTAINING");

            if ($descricao != "")
                $filtro["A.DESCRICAO"] = array($descricao, "CONTAINING");
            
            $ca = new cbo();
            if ($ca->buscar($filtro, $limite, array("A.CODIGO" => "ASC"))){
                $totalRegistros = $ca->getTotalRegistros();
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Código", array("style" => "width: 65px; font-weight: bold"));
                $cab->addItem("Descrição", array("style" => "font-weight: bold"));
                $cab->addItem("", array("style" => "width: 20px"));
                $tab->addCabecalho($cab);
                
                for ($i = 0; $i < $ca->getTotalLista(); $i++){
                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem($ca->getItemLista($i)->getCodigo());
                    $reg->addItem(utf8_encode($ca->getItemLista($i)->getDescricao()));
                    $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" "
                                  . "codigo=\"{$ca->getItemLista($i)->getCodigo()}\""
                                  . "descricao=\"".utf8_encode($ca->getItemLista($i)->getDescricao())."\"><i class=\"fa fa-check\"></i></a>");
                    $tab->addRegistro($reg);
                }
                echo $tab->gerar();
                funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarCbo('<pagina>');");
            }else
                echo "<h5>Nenhum resultado encontrado!</h5>";
        }
        
        die;
    }
?>
<div class="form-group-sm">
    <div class="col-md-4">
        <label>Código CBO:</label>
        <div class="input-group">
            <input type="input" id="modal-input-codigo-cbo" class="form-control input-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
    <div class="col-md-8">
        <label>Descrição:</label>
        <div class="input-group">
            <input type="input" id="modal-input-descricao-cbo" class="form-control input-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
</div> 
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-div-pesquisa-cbo"></div>
<script>
    $('#modal-button-pesquisar').on('click', function(){
        pesquisarCbo('1');
    });
    $('#modal-input-descricao-cbo').keypress(function(event){
        if (event.which == 13)
           $('#modal-button-pesquisar').click();
    });
    $('#modal-input-codigo-cbo').keypress(function(event){
        if (event.which == 13)
           $('#modal-button-pesquisar').click();
    });
    
    $('document').ready(function(){
        $('#modal-button-pesquisar').click();
    });
    
    function pesquisarCbo(pagina){
        $('#modal-div-pesquisa-cbo').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#modal-div-pesquisa-cbo').load('cbo-pesquisa.php', 
                                                    {'a': 'pesquisa', 
                                                     'codigo': $('#modal-input-codigo-cbo').val(), 
                                                     'descricao': $('#modal-input-descricao-cbo').val(), 
                                                     'pagina': pagina 
                                                    }, function(){
                                                        $('#modal-div-pesquisa-cbo a').each(function(){
                                                            $(this).on('click', function(){
                                                                <?php
                                                                    $prefixo = "form";
                                                                    if (isset($_POST["prefixo"]))
                                                                        $prefixo = $_POST["prefixo"];
                                                                ?>
                                                                $('#<?php echo $prefixo; ?>-cbo').val($(this).attr('codigo'));
                                                                $('#<?php echo $prefixo; ?>-descricao-cbo').val($(this).attr('codigo') +'-'+$(this).attr('descricao'));
                                                                $('#div-modal').modal('toggle');
                                                            });
                                                        });
                                                    });
    }
</script>