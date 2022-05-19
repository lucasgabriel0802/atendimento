<?php
    require_once("class/config.inc.php");
    require_once("class/cep.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    
    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "pesquisa"){

            $pagina = 1;
            $endereco = "";
            
            if (isset($_POST["endereco"]))
                $endereco = $_POST["endereco"];
            
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
            
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;
            
            $filtro = null;
            
            if ($endereco != ""){
                $filtro["A.ENDERECO"] = array($endereco, "CONTAINING");
            }
            
            $cep = new cep();
            if (!$cep->buscar($filtro, $limite, array("A.ENDERECO" => "ASC"))){
                $filtro = null;
                $filtro["A.CODIGO"] = array($endereco, "CONTAINING");
            }
            
            $cep = null;
            $cep = new cep();
            if ($cep->buscar($filtro, $limite, array("A.ENDERECO" => "ASC"))){
                $totalRegistros = $cep->getTotalRegistros();
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("CEP", array("style" => "width: 85px; font-weight: bold"));
                $cab->addItem("Endereço", array("style" => "font-weight: bold"));
                $cab->addItem("Bairro", array("style" => "font-weight: bold"));
                $cab->addItem("Cidade", array("style" => "font-weight: bold"));
                $cab->addItem("UF", array("style" => "font-weight: bold"));
                $cab->addItem("", array("style" => "width: 20px"));
                $tab->addCabecalho($cab);
                
                for ($i = 0; $i < $cep->getTotalLista(); $i++){
                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem($cep->getItemLista($i)->getCEP());
                    $reg->addItem(utf8_encode($cep->getItemLista($i)->getEndereco()));
                    $reg->addItem(utf8_encode($cep->getItemLista($i)->getBairro()));
                    $reg->addItem(utf8_encode($cep->getItemLista($i)->getCidade()));
                    $reg->addItem(utf8_encode($cep->getItemLista($i)->getUF()));
                    
                    $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" "
                                  . "cep=\"{$cep->getItemLista($i)->getCEP()}\""
                                  . "endereco=\"{$cep->getItemLista($i)->getEndereco()}\""
                                  . "bairro=\"{$cep->getItemLista($i)->getBairro()}\""
                                  . "cidade=\"{$cep->getItemLista($i)->getCidade()}\""
                                  . "uf=\"{$cep->getItemLista($i)->getUF()}\""
                                  ."\"><i class=\"fa fa-check\"></i></a>");
                    $tab->addRegistro($reg);
                }
                echo $tab->gerar();
                funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarCep('<pagina>');");
            }else
                echo "<h5>Nenhum resultado encontrado!</h5>";
        }
        
        die;
    }
?>
<div class="form-group-sm">
    <div class="col-md-12">
        <label>Tipo de Acidente:</label>
        <div class="input-group">
            <input type="input" id="modal-input-endereco" class="form-control input-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
</div> 
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-div-pesquisa"></div>
<script>
    $('#modal-button-pesquisar').on('click', function(){
        pesquisarCep('1');
    });
    $('#modal-input-endereco').keypress(function(event){
        if (event.which == 13)
           $('#modal-button-pesquisar').click();
    });
    
    $('document').ready(function(){
        $('#modal-button-pesquisar').click();
    });
    
    function pesquisarCep(pagina){
        $('#modal-div-pesquisa').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#modal-div-pesquisa').load('cep-pesquisa.php', 
                                                    {'a': 'pesquisa', 
                                                     'endereco': $('#modal-input-endereco').val(), 
                                                     'pagina': pagina 
                                                    }, function(){
                                                        $('#modal-div-pesquisa a').each(function(){
                                                            $(this).on('click', function(){
                                                                <?php
                                                                    $prefixo = "form";
                                                                    if (isset($_POST["prefixo"]))
                                                                        $prefixo = $_POST["prefixo"];
                                                                ?>
                                                                $('#<?php echo $prefixo; ?>-cep').val($(this).attr('cep'));
                                                                $('#<?php echo $prefixo; ?>-endereco').val($(this).attr('endereco'));
                                                                $('#<?php echo $prefixo; ?>-bairro').val($(this).attr('bairro'));
                                                                $('#<?php echo $prefixo; ?>-cidade').val($(this).attr('cidade'));
                                                                $('#<?php echo $prefixo; ?>-select-uf').val($(this).attr('uf'));
                                                                $('#div-modal').modal('toggle');
                                                            });
                                                        });
                                                    });
    }
</script>