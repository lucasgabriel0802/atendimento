<?php
    require_once("class/config.inc.php");
    require_once("class/setor.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    require_once("class/levantamento.class.php");
    
    config::verificaLogin();
    
    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "pesquisa"){
            if (!isset($_POST["posto"]) || ($_POST["posto"] == "")){
                echo "<h4>Selecione o posto de trabalho!</h4>";
                die;
            }            
            $pagina = 1;
            $nome   = "";
            $posto  = $_POST["posto"];
            
            if (isset($_POST["nome"]))
                $nome = $_POST["nome"];
            
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
            
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;
            
            /*
             * $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                            "A.POSTOTRABALHO" => $posto,
                            //"A.DOCUMENTOBASE" => "S",
                            "A.STATUS" => "1");

            $levantamento = 0;
            if ($l->buscar($filtro, null, array("A.DATA" => "DESC"))){
                $levantamento = $l->getItemLista(0)->getIdentificacao();
                $levantamentoData = $l->getItemLista(0)->getData();
            }
           */
            
            $se = new setor();
            if ($se->buscarSetorLevantamento($_SESSION[config::getSessao()]["empresa_ativa"],
                                             $_SESSION[config::getSessao()]["unidade"],
                                             $posto, date('d.m.Y'), $nome)){
                $totalRegistros = $se->getTotalRegistros();
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Código", array("style" => "width: 65px; font-weight: bold"));
                $cab->addItem("Descrição", array("style" => "font-weight: bold"));
                $cab->addItem("", array("style" => "width: 20px"));
                $tab->addCabecalho($cab);
                
                for ($i = 0; $i < $se->getTotalLista(); $i++){
                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem($se->getItemLista($i)->getCodigo());
                    $reg->addItem(utf8_encode($se->getItemLista($i)->getNome()));
                    $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" "
                                  . "codigo=\"{$se->getItemLista($i)->getCodigo()}\""
                                  . "nome=\"".utf8_encode($se->getItemLista($i)->getNome())."\"><i class=\"fa fa-check\"></i></a>");
                    $tab->addRegistro($reg);
                }
                echo $tab->gerar();
                funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarSetor('<pagina>');");
            }else
                echo "<h5>Nenhum resultado encontrado!</h5>";
        }
        
        die;
    }
?>
<div class="form-group-sm">
    <div class="col-md-12">
        <label>Setor:</label>
        <div class="input-group">
            <input type="input" id="modal-input-nome-setor" class="form-control input-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
</div> 
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-div-pesquisa-setor"></div>
<script>
    $('#modal-button-pesquisar').on('click', function(){
        pesquisarSetor('1');
    });
    $('#modal-input-nome-setor').keypress(function(event){
        if (event.which == 13)
           $('#modal-button-pesquisar').click();
    });
    
    $('document').ready(function(){
        $('#modal-button-pesquisar').click();
    });
    
    function pesquisarSetor(pagina){
        <?php
            $prefixo = "form";
            if (isset($_POST["prefixo"]))
                $prefixo = $_POST["prefixo"];
        ?>
        $('#modal-div-pesquisa-setor').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#modal-div-pesquisa-setor').load('setor-pesquisa.php', 
                                                    {'a': 'pesquisa', 
                                                     'nome': $('#modal-input-nome-setor').val(), 
                                                     'posto': $('#<?php echo $prefixo; ?>-input-posto').val(),
                                                     'pagina': pagina 
                                                    }, function(){
                                                        $('#modal-div-pesquisa-setor a').each(function(){
                                                            $(this).on('click', function(){
                                                                if ($('#<?php echo $prefixo; ?>-input-novo-setor').val() != $(this).attr('codigo')){
                                                                    $('#<?php echo $prefixo; ?>-input-nova-funcao').val('');
                                                                    $('#<?php echo $prefixo; ?>-input-nome-nova-funcao').val('');
                                                                }
                                                                $('#<?php echo $prefixo; ?>-input-novo-setor').val($(this).attr('codigo'));
                                                                $('#<?php echo $prefixo; ?>-input-nome-novo-setor').val($(this).attr('nome'));
                                                                <?php
                                                                    if ($prefixo == "cadastro"):
                                                                    ?>
                                                                        $('#<?php echo $prefixo; ?>-input-setor').val($(this).attr('codigo'));
                                                                        $('#<?php echo $prefixo; ?>-input-nome-setor').val($(this).attr('nome'));                    
                                                                    <?php
                                                                    endif;
                                                                ?>
                                                                $('#div-modal').modal('toggle');
                                                            });
                                                        });
                                                    });
    }
</script>