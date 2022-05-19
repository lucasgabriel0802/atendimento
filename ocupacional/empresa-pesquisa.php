<?php
    require_once("class/config.inc.php");
    require_once("class/empresa.class.php");
    require_once("class/usuwebemp.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    
    config::verificaLogin();
    
    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "pesquisa"){
            $pagina = 1;
            $razaosocial = "";
            
            if (isset($_POST["razaosocial"]))
                $razaosocial = $_POST["razaosocial"];
            
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
            
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;
            
            $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                            "A.RESCISAOCONTRATO" => array("NULL", "IS"));
            
            if ($_SESSION[config::getSessao()]["interno"] != "S")
                $filtro["A.CLIENTEAGRUPADOR"] = $_SESSION[config::getSessao()]["empresa"];
            
            $filtro2 = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                             "A.USUARIO" => $_SESSION[config::getSessao()]["codigo"],
                             "C.RESCISAOCONTRATO" => array("NULL", "IS"));
            
            if ($razaosocial != ""){
                $filtro["A.RAZAOSOCIAL"] = array($razaosocial, "CONTAINING");
                $filtro2["C.RAZAOSOCIAL"] = array($razaosocial, "CONTAINING");
            }
            
            $em = new empresa();
            $uw = new usuwebemp();
            if ($uw->buscar($filtro2, $limite, array("C.RAZAOSOCIAL" => "ASC"))){
                $totalRegistros = $uw->getTotalRegistros();

                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Código", array("style" => "width: 65px; font-weight: bold"));
                $cab->addItem("Razão social", array("style" => "font-weight: bold"));
                $cab->addItem("", array("style" => "width: 20px"));
                $tab->addCabecalho($cab);

                for ($i = 0; $i < $uw->getTotalLista(); $i++){
                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem($uw->getItemLista($i)->getEmpresa()->getCodigo());
                    $reg->addItem(utf8_encode($uw->getItemLista($i)->getEmpresa()->getRazaoSocial()));
                    $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" id-empresa=\"{$uw->getItemLista($i)->getEmpresa()->getCodigo()}\">"
                                    . "<i class=\"fa fa-check\"></i>"
                                . "</a>");
                    $tab->addRegistro($reg);
                }
                echo $tab->gerar();
                funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarEmpresa('<pagina>');");
            }else
                if ($em->buscar($filtro, $limite, array("A.RAZAOSOCIAL" => "ASC"))){
                    $totalRegistros = $em->getTotalRegistros();

                    $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                    $cab = new cabecalho(array("class" => "no-border"));
                    $cab->addItem("Código", array("style" => "width: 65px; font-weight: bold"));
                    $cab->addItem("Razão social", array("style" => "font-weight: bold"));
                    $cab->addItem("", array("style" => "width: 20px"));
                    $tab->addCabecalho($cab);

                    for ($i = 0; $i < $em->getTotalLista(); $i++){
                        $reg = new registro(array("class" => "no-border-y"));
                        $reg->addItem($em->getItemLista($i)->getCodigo());
                        $reg->addItem(utf8_encode($em->getItemLista($i)->getRazaoSocial()));
                        $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" id-empresa=\"{$em->getItemLista($i)->getCodigo()}\">"
                                        . "<i class=\"fa fa-check\"></i>"
                                    . "</a>");
                        $tab->addRegistro($reg);
                    }
                    echo $tab->gerar();
                    funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarEmpresa('<pagina>');");
                }else
                    echo "<h5>Nenhum resultado encontrado!</h5>";
        }else
            if ($acao == "trocaEmpresaAtiva"){
                $retorno = array("codigo" => 0, "mensagem" => "");
                $idEmpresa = $_POST["idEmpresa"];

                if (!is_numeric($idEmpresa)){
                    $retorno["codigo"] = 1;
                    $retorno["mensagem"] = "Código da empresa inválido!";
                }else{
                    $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                    "A.CODIGO"  => $idEmpresa);
                    
                    $filtro2 = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                     "A.USUARIO" => $_SESSION[config::getSessao()]["codigo"],
                                     "A.EMPRESA" => $idEmpresa);
                    
                    if ($_SESSION[config::getSessao()]["interno"] != "S")
                        $filtro["A.CLIENTEAGRUPADOR"] = $_SESSION[config::getSessao()]["empresa"];
                    
                    $em = new empresa();
                    $uw = new usuwebemp();
                    if ($uw->buscar($filtro2, 1)){
                        $_SESSION[config::getSessao()]["empresa_ativa"]  = $uw->getItemLista(0)->getEmpresa()->getCodigo();
                        $_SESSION[config::getSessao()]["nome_emp_ativa"] = $uw->getItemLista(0)->getEmpresa()->getRazaoSocial();

                        $retorno["codigo"] = 0;
                        $retorno["mensagem"] = "OK";
                    }else
                        if ($em->buscar($filtro, 1)){
                            $_SESSION[config::getSessao()]["empresa_ativa"]  = $em->getItemLista(0)->getCodigo();
                            $_SESSION[config::getSessao()]["nome_emp_ativa"] = $em->getItemLista(0)->getRazaoSocial();

                            $retorno["codigo"] = 0;
                            $retorno["mensagem"] = "OK";
                        }else{
                            $retorno["codigo"] = 2;
                            $retorno["mensagem"] = "Empresa não vinculada!";
                        }

                }

                echo json_encode($retorno);
            }
        
        die;
    }
?>
<div class="form-group-sm">
    <div class="col-md-12">
        <label>Razão social:</label>
        <div class="input-group">
            <input type="input" id="modal-input-nome-fantasia" class="form-control input-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
</div> 
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-div-pesquisa-empresa"></div>
<script>
    $('#modal-button-pesquisar').on('click', function(){
        pesquisarEmpresa('1');
    });
    $('#modal-input-nome-fantasia').keypress(function(event){
        if (event.which == 13)
           $('#modal-button-pesquisar').click();
    });
    
    $('document').ready(function(){
        $('#modal-button-pesquisar').click();
    });
    
    function pesquisarEmpresa(pagina){
        $('#modal-div-pesquisa-empresa').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#modal-div-pesquisa-empresa').load('empresa-pesquisa.php', 
                                                    {'a': 'pesquisa', 
                                                     'razaosocial': $('#modal-input-nome-fantasia').val(), 
                                                     'pagina': pagina 
                                                    }, function(){
                                                        $('#modal-div-pesquisa-empresa a').on('click', function(){
                                                            $.ajax({
                                                                type: 'post',
                                                                url: 'empresa-pesquisa.php',
                                                                data: 'a=trocaEmpresaAtiva&idEmpresa=' + $(this).attr('id-empresa'),
                                                                dataType: 'json',
                                                                async: true,
                                                                cache: false
                                                            }).done(function(data){
                                                                if (data.codigo > 0){
                                                                    $.gritter.add({
                                                                        title: 'Ops!',
                                                                        text: data.mensagem + ' #' + data.codigo,
                                                                        class_name: 'danger'
                                                                    });
                                                                }else
                                                                    window.location.href = 'admin.php';
                                                            });
                                                        });
                                                    });
    }
</script>