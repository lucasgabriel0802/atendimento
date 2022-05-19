<?php
    require_once("class/config.inc.php");
    require_once("class/setorfuncao.class.php");
    require_once("class/levantamento.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    
    config::verificaLogin();
    
    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "pesquisa"){
            if (!isset($_POST["setor"]) || ($_POST["setor"] == "")){
                echo "<h4>Selecione o setor!</h4>";
                die;
            }            
            
            $pagina = 1;
            $nome   = "";
            $setor  = $_POST["setor"];
            $posto  = "";
            $levantamento = 0;
            
            if (isset($_POST["posto"]))
                $posto = $_POST["posto"];
            
            $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                            "A.POSTOTRABALHO" => $posto);
        // "A.STATUS" => "1"*/);
            
            $l = new levantamento();
            if ($l->buscar($filtro, null, array("A.DATA" => "DESC"))){
                $levantamento = $l->getItemLista(0)->getIdentificacao();
            }
            
            if (isset($_POST["nome"]))
                $nome = $_POST["nome"];
            
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
            
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;
            
            $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                            "A.EXCLUIDO" => "N",
                            "C.ATIVA" => "S",
                            "A.SETOR" => $setor,
                            "A.IDENTIFICACAO" => $levantamento);
            
            if ($nome != "")
                $filtro["A.NOME"] = array($nome, "CONTAINING");
            
            $sf = new setorfuncao();
            if ($sf->buscar($filtro, $limite, array("A.NOME" => "ASC"))){
                $totalRegistros = $sf->getTotalRegistros();
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Código", array("style" => "width: 65px; font-weight: bold"));
                $cab->addItem("Nome", array("style" => "font-weight: bold"));
                $cab->addItem("", array("style" => "width: 20px"));
                $tab->addCabecalho($cab);
                
                for ($i = 0; $i < $sf->getTotalLista(); $i++){
                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem($sf->getItemLista($i)->getFuncao()->getCodigo());
                    $reg->addItem(utf8_encode($sf->getItemLista($i)->getFuncao()->getNome()));
                    $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" "
                                  . "codigo=\"{$sf->getItemLista($i)->getFuncao()->getCodigo()}\""
                                  . "nome=\"".utf8_encode($sf->getItemLista($i)->getFuncao()->getNome())."\">"
                                          . "<i class=\"fa fa-check\"></i></a>");
                    $tab->addRegistro($reg);
                }
                echo $tab->gerar();
                funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarSetorFuncao('<pagina>');");
            }else
                echo "<h5>Nenhum resultado encontrado!</h5>" ;
        }
        
        die;
    }
?>
<div class="form-group-sm">
    <div class="col-md-12">
        <label>Função:</label>
        <div class="input-group">
            <input type="input" id="modal-input-nome-funcao" class="form-control input-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
</div> 
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-div-pesquisa-funcao"></div>
<script>
    $('#modal-button-pesquisar').on('click', function(){
        pesquisarSetorFuncao('1');
    });
    $('#modal-input-nome-funcao').keypress(function(event){
        if (event.which == 13)
           $('#modal-button-pesquisar').click();
    });
    
    $('document').ready(function(){
        $('#modal-button-pesquisar').click();
    });
    
    function pesquisarSetorFuncao(pagina){
        <?php
            $prefixo = "form";
            if (isset($_POST["prefixo"]))
                $prefixo = $_POST["prefixo"];
        ?>
        $('#modal-div-pesquisa-funcao').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#modal-div-pesquisa-funcao').load('funcao-pesquisa.php', 
                                                    {'a': 'pesquisa', 
                                                     'nome': $('#modal-input-nome-funcao').val(), 
                                                     <?php if ($prefixo == "cadastro"){ ?>
                                                        'setor': $('#<?php echo $prefixo; ?>-input-setor').val(),
                                                        'posto': $('#<?php echo $prefixo; ?>-input-posto').val(),
                                                     <?php }else{ ?>
                                                        'setor': $('#<?php echo $prefixo; ?>-input-novo-setor').val(), 
                                                        'posto': $('#<?php echo $prefixo; ?>-input-posto').val(),
                                                     <?php } ?>
                                                     'pagina': pagina 
                                                    }, function(){
                                                        $('#modal-div-pesquisa-funcao a').each(function(){
                                                            $(this).on('click', function(){
                                                                <?php if ($prefixo == "cadastro"){ ?>
                                                                $('#<?php echo $prefixo; ?>-input-funcao').val($(this).attr('codigo'));
                                                                $('#<?php echo $prefixo; ?>-input-nome-funcao').val($(this).attr('nome'));
                                                                <?php }else{ ?>
                                                                $('#<?php echo $prefixo; ?>-input-nova-funcao').val($(this).attr('codigo'));
                                                                $('#<?php echo $prefixo; ?>-input-nome-nova-funcao').val($(this).attr('nome'));
                                                                <?php } ?>
                                                                $('#div-modal').modal('toggle');
                                                            });
                                                        });
                                                    });
    }
</script>