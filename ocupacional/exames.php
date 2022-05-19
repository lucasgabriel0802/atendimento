<?php
require_once("class/config.inc.php");
require_once("class/agenda.class.php");
require_once("class/exameweb.class.php");
require_once("class/funcexamesacomp.class.php");
require_once("class/funcionario.class.php");
require_once("class/funcoes.class.php");
require_once("class/tabela.class.php");
require_once("class/funcaso.class.php");

config::verificaLogin();

if (isset($_POST["a"])) {
    $acao = $_POST["a"];

    if ($acao == "pesquisar") {
        $retorno = array("codigo" => 0, "mensagem" => "");
        if (isset($_POST["funcionario"]) && isset($_POST["data"]) && isset($_POST["tipo"])) {

            if (is_numeric($_POST["funcionario"])) {

                if (funcoes::validarData($_POST["data"])) {

                    $ex = new exameweb();
                    if ($ex->buscar(array("UNIDADE" => $_SESSION[config::getSessao()]["unidade"])))
                        $ex = $ex->getItemLista(0);

                    $tipoOk = false;
                    if ($ex->permiteAdmissional() && $_POST["tipo"] == "A")
                        $tipoOk = true;
                    if ($ex->permitePeriodico() && $_POST["tipo"] == "P")
                        $tipoOk = true;
                    if ($ex->permiteDemissional() && $_POST["tipo"] == "D")
                        $tipoOk = true;
                    if ($ex->permiteMudancaFuncao() && $_POST["tipo"] == "M")
                        $tipoOk = true;
                    if ($ex->permiteRetornoTrabalho() && $_POST["tipo"] == "R")
                        $tipoOk = true;
                    if ($ex->permiteConsultaClinica() && $_POST["tipo"] == "C")
                        $tipoOk = true;
                    if ($ex->permiteIndefinido() && $_POST["tipo"] == "I")
                        $tipoOk = true;

                    if ($tipoOk) {
                        $fu = new funcionario();
                        if ($fu->buscar(array(
                            "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                            "A.MATRICULA" => $_POST["funcionario"]
                        ), 1)) {
                            $fu = $fu->getItemLista(0);

                            $fe = new funcexamesacomp();
                            $fe->buscarExames(
                                $_SESSION[config::getSessao()]["empresa_ativa"],
                                $_SESSION[config::getSessao()]["unidade"],
                                $fu->getPostoTrabalho()->getCodigo(),
                                $fu->getCodigo(),
                                $fu->getSetor()->getCodigo(),
                                $fu->getFuncao()->getCodigo(),
                                $_POST["tipo"],
                                funcoes::converterData($_POST["data"])
                            );

                            if ($fe->buscar(array(
                                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                "A.MATRICULA" => $fu->getCodigo()
                            ))) {
                                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                                $cab = new cabecalho(array("class" => "no-border"));
                                $cab->addItem("Descrição", array("style" => "font-weight: bold"));
                                $tab->addCabecalho($cab);

                                for ($i = 0; $i < $fe->getTotalLista(); $i++) {
                                    $reg = new registro();
                                    $reg->addItem(utf8_encode($fe->getItemLista($i)->getExame()->getDescricao()));
                                    $tab->addRegistro($reg);
                                }
                                echo $tab->gerar();

                            $fa = new funcaso();
                            if($fa->buscar(array(
                                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                "A.MATRICULA" => $fu->getCodigo()), null, "A.DATAPEDIDO" )){

        
                                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                                $cab = new cabecalho(array("class" => "no-border"));
                                $cab->addItem("Data Pedido", array("style" => "font-weight: bold;text-align: center"));
                                $cab->addItem("Característica", array("style" => "font-weight: bold"));
                                $tab->addCabecalho($cab);

                                for ($i = 0; $i < $fa->getTotalLista(); $i++) {
                                    $reg = new registro();
                                    $reg->addItem(utf8_encode(date('d/m/Y', strtotime($fa->getItemLista($i)->getDataPedido())) ), array("style" => "text-align: center"));
                                    if($fa->getItemLista($i)->getCaracteristica() == 'A')
                                        $reg->addItem(utf8_encode('Admissional'));
                                    elseif(($fa->getItemLista($i)->getCaracteristica() == 'D'))
                                        $reg->addItem(utf8_encode('Demissional'));
                                    elseif(($fa->getItemLista($i)->getCaracteristica() == 'P'))
                                        $reg->addItem(('Periódico'));
                                    elseif(($fa->getItemLista($i)->getCaracteristica() == 'M'))
                                        $reg->addItem(('Mudança de Função'));
                                    elseif(($fa->getItemLista($i)->getCaracteristica() == 'R'))
                                        $reg->addItem(('Retorno ao Trabalho'));

                                    $linkBotao   = "imprimeaso.php?matricula=".$fu->getCodigo()."&empresa=".$_SESSION[config::getSessao()]["empresa_ativa"]."&unidade=".$_SESSION[config::getSessao()]["unidade"]. "&aso=" . $fa->getItemLista($i)->getNumero();
                                    //<a href="imprimeaso.php?matricula=1&empresa=1&unidade=1&aso=1" target="_blank" class="btn btn-primary">
                                    if (funcoes::acessoCelular()){
                                        $botao = "<a style=\"padding: 0px 10px; \" href=\"{$linkBotao}\" target=\"_blank\">"
                                        . "<i class=\"fa fa-print\"></i>"
                                        . "</a>";
                                    }else{
                                        $botao = "<a class=\"btn btn-primary btn-xs \" href=\"{$linkBotao}\" target=\"_blank\">"
                                        . "<i class=\"fa fa-print\"></i>"
                                        . "</a>";
                                    }
                                    $reg->addItem($botao, array("style" => "text-align: center"));

                                    $tab->addRegistro($reg);
                                }
    
                                echo $tab->gerar();
                               
                            }
    
    
                            } else
                                echo "<h4>Nenhum resultado foi encontrado!</h4>";
                        } else {
                            $retorno["codigo"] = 7;
                            $retorno["mensagem"] = "Funcionário não encontrado!";
                        }
                    } else {
                        $retorno["codigo"] = 6;
                        $retorno["mensagem"] = "Tipo de exame inválido!";
                    }
                } else {
                    $retorno["codigo"] = 5;
                    $retorno["mensagem"] = "Selecione uma data válida!";
                }
            } else {
                $retorno["codigo"] = 4;
                $retorno["mensagem"] = "Informe um funcionário válido!";
            }
        } else {
            if (!isset($_POST["funcionario"]))
                $retorno["codigo"] = 1;
            else
                    if (!isset($_POST["data"]))
                $retorno["codigo"] = 2;
            else
                        if (!isset($_POST["tipo"]))
                $retorno["codigo"] = 3;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        if ($retorno["codigo"] > 0)
            echo "<script>$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
    }

    die;
}
?>

<div class="page-head">
    <h2>Exames</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li class="active">Exames</li>
    </ol>
</div>
<div class="cl-mcont">
    <div class="block-flat">
        <div class="form-group-sm">
            <div class="col-sm-12 col-md-12 col-lg-6" style="height: 58px;">
                <label>Funcionário:</label>
                <div class="input-group">
                    <input type="hidden" id="form-input-funcionario">
                    <input type="text" id="form-input-nome-funcionario" readonly class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-funcionario" style="height: 30px; padding-top: 4px">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group-sm">
            <div class="col-sm-6 col-md-6 col-lg-3" style="height: 58px;">
                <label>Setor:</label>
                <input type="text" id="form-input-setor" readonly class="form-control input-sm" style="background-color: #fff;">
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3" style="height: 58px;">
                <label>Função:</label>
                <input type="text" id="form-input-funcao" readonly class="form-control input-sm" style="background-color: #fff;">
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group-sm">
            <div class="col-sm-1" style="width: 115px; height: 58px;">
                <label>Data:</label>
                <input type="text" id="form-input-data" readonly class="form-control input-sm" style="background-color: #FFF;" value="<?php echo date("d/m/Y"); ?>">
            </div>
            <div class="col-sm-4 col-md-4 col-lg-2" style="height: 58px;">
                <label>Tipo:</label>
                <select id="form-select-tipo" class="form-control input-sm">
                    <?php
                    $ex = new exameweb();
                    if ($ex->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"]), 1)) {
                        if ($ex->getItemLista(0)->permiteAdmissional()) : ?>
                            <option value="A">ADMISSIONAL</option>
                        <?php
                        endif;
                        if ($ex->getItemLista(0)->permitePeriodico()) : ?>
                            <option value="P">PERIÓDICO</option>
                        <?php
                        endif;
                        if ($ex->getItemLista(0)->permiteDemissional()) : ?>
                            <option value="D">DEMISSIONAL</option>
                        <?php
                        endif;
                        if ($ex->getItemLista(0)->permiteMudancaFuncao()) : ?>
                            <option value="M">MUDANÇA DE FUNÇÃO</option>
                        <?php
                        endif;
                        if ($ex->getItemLista(0)->permiteRetornoTrabalho()) : ?>
                            <option value="R">RETORNO AO TRABALHO</option>
                        <?php
                        endif;
                        if ($ex->getItemLista(0)->permiteConsultaClinica()) : ?>
                            <option value="C">CONSULTA CLÍNICA</option>
                        <?php
                        endif;
                        if ($ex->getItemLista(0)->permiteIndefinido()) : ?>
                            <option value="I">INDEFINIDO</option>
                    <?php
                        endif;
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-4" style="height: 58px; padding-top: 25px;">
                <button type="button" class="btn btn-primary" id="form-button-pesquisar-exames">
                    Pesquisar
                </button>
                
                <a href="imprimeaso.php?matricula=1&empresa=1&unidade=1&aso=1" target="_blank" class="btn btn-primary">
                    <i class="fa fa-print" style="width: 15px"></i>
                    ASO
                </a>
            </div>
            <div class="clearfix"></div>
        </div> <br>
        <div class="form-group-sm" style="margin-left: 10px;">
            <div class="col-md-6" id="div-exames"></div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#form-input-data').datepicker();
        $('#form-input-data').mask('99/99/9999');
    });

    $('#form-button-pesquisa-funcionario').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa funcionário');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('funcionario-pesquisa.php');
    });

    $('#form-button-pesquisar-exames').on('click', function() {
        $('#div-exames').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-exames').load('exames.php', {
            'a': 'pesquisar',
            'funcionario': $('#form-input-funcionario').val(),
            'data': $('#form-input-data').val(),
            'tipo': $('#form-select-tipo').find('option:selected').val()
        });
    })
</script>