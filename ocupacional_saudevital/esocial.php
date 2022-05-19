<?php
require_once("class/config.inc.php");
require_once("class/empresa.class.php");
require_once("class/esocials1060.class.php");
require_once("class/esocials2210.class.php");
require_once("class/esocials2220.class.php");
require_once("class/esocials2221.class.php");
require_once("class/esocials2240.class.php");
require_once("class/esocials2245.class.php");
require_once("class/parametrosgerais.class.php");
require_once("class/criptografia.class.php");
require_once("class/funcoes.class.php");
require_once("class/tabela.class.php");
require_once("class/ocorrencia.class.php");

config::verificaLogin();

if ($_SESSION[config::getSessao()]["esocial"] != "S") {
    header("Location: 403.php");
    die;
}

$empresa = new empresa();
$empresa->buscar(array(
    "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
    "A.CODIGO" => $_SESSION[config::getSessao()]["empresa_ativa"]
));
$empresa = $empresa->getItemLista(0);

$parametro = new parametrosgerais();
$parametro->buscar();
$parametro = $parametro->getItemLista(0);

if (isset($_POST["a"])) {
    $acao = $_POST["a"];
    if (($acao == "reprocessar-e-social")) {
        $chave = $_POST["chave"];
        $oc = new ocorrenciaESocial();
        $oc->deletarOcorrencias(
            $_SESSION[config::getSessao()]["unidade"],
            $_SESSION[config::getSessao()]["empresa_ativa"],
            $chave,
            true
        );

        // echo $chave;
        die;
    } else
    if (($acao == "pesquisar-e-social-ocorrencia")) {
        $chave = $_POST["chave"];
        config::setLimitePagina(100);

        $pagina--; // inicia com 0
        $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
        $totalRegistros = 0;

        $oc = new ocorrenciaESocial();
        if ($oc->buscar(array("A.CHAVE" => $chave), $limite, array("A.ITEM" => "ASC"))) {
            $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
            $cab = new cabecalho(array("class" => "no-border"));
            $cab->addItem("Código", array("style" => "font-weight: bold; text-align: center; width: 5%"));
            $cab->addItem("Descrição", array("style" => "font-weight: bold; text-align: center;"));
            $cab->addItem("Localização", array("style" => "font-weight: bold; text-align: center;"));
            $tab->addCabecalho($cab);

            for ($i = 0; $i < $oc->getTotalLista(); $i++) {
                $reg = new registro(array("class" => "no-border-y"));
                $reg->addItem(utf8_encode($oc->getItemLista($i)->getCodigo()), array("style" => "text-align: right;"));
                $reg->addItem(utf8_encode($oc->getItemLista($i)->getDescricao()));
                $reg->addItem(utf8_encode($oc->getItemLista($i)->getLocalizacao()));
                $tab->addRegistro($reg);
            }
            echo $tab->gerar();
        }
        die;
    } else
    if (($acao == "pesquisar-e-social-s1060") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        $pagina  = 1;
        if (isset($_POST["pagina"]))
            if (is_numeric($_POST["pagina"]))
                $pagina = $_POST["pagina"];

        config::setLimitePagina(100);

        $pagina--; // inicia com 0
        $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
        $totalRegistros = 0;

        $es = new esocials1060();
        if ($es->buscar(
            array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                //"A.SITUACAO" => "A"
            ),
            $limite,
            array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC")
        )) {
            $totalRegistros = $es->getTotalRegistros();

            $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
            $cab = new cabecalho(array("class" => "no-border"));
            $cab->addItem("Seq", array("style" => "font-weight: bold; text-align: center; width: 5%"));
            $cab->addItem("Posto de trabalho", array("style" => "font-weight: bold; text-align: center;"));
            $cab->addItem("Setor", array("style" => "font-weight: bold; text-align: center;"));
            $cab->addItem("Data/hora", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("Situação", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("Sit. Transmissão", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $tab->addCabecalho($cab);

            $classeBotao = "\" style=\"padding: 0px 10px;";
            $temAberto = false;

            for ($i = 0; $i < $es->getTotalLista(); $i++) {
                if ($es->getItemLista($i)->getSituacao() == "A") {
                    $situacao = "ABERTO";
                    $temAberto = true;
                } else {
                    $situacao = "BAIXADO";
                }

                if ($es->getItemLista($i)->getSituacaoTransmissao() == "P") {
                    $situacaoTransmissao = "PENDENTE";
                } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "E") {
                    $situacaoTransmissao = "ENVIADO";
                } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                    $situacaoTransmissao = "REJEITADO";
                } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "A") {
                    $situacaoTransmissao = "AUTORIZADO";
                }

                $reg = new registro(array("class" => "no-border-y"));
                $reg->addItem($es->getItemLista($i)->getSequencial(), array("style" => "text-align: right;"));
                $reg->addItem(utf8_encode($es->getItemLista($i)->getPostoTrabalho()->getCodigo() . ' - ' . $es->getItemLista($i)->getPostoTrabalho()->getDescricao()));
                $reg->addItem(utf8_encode($es->getItemLista($i)->getSetor()->getCodigo() . ' - ' . $es->getItemLista($i)->getSetor()->getNome()));
                $reg->addItem(funcoes::converterData($es->getItemLista($i)->getDataHora(), true), array("style" => "text-align: center;"));
                $reg->addItem($situacao, array("style" => "text-align: center;"));
                $reg->addItem($situacaoTransmissao, array("style" => "text-align: center;"));

                $botoes = "<a class=\"btn btn-default btn-xs\" attr=\"baixar-esocial\" situacao=\"{$es->getItemLista($i)->getSituacao()}\" "
                    . "posto=\"{$es->getItemLista($i)->getPostoTrabalho()->getCodigo()}\" "
                    . "setor=\"{$es->getItemLista($i)->getSetor()->getCodigo()}\" "
                    . "sequencial=\"{$es->getItemLista($i)->getSequencial()}\" "
                    . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">Gerar</a>";

                $reg->addItem($botoes, array("style" => "text-align: center;"));
                $tab->addRegistro($reg);
            }

            if ($temAberto)
                echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s1060')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

            echo $tab->gerar();
            echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's1060')");
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Nenhum registro encontrado!";
        }

        if ($retorno["codigo"] > 0)
            echo "{$retorno["mensagem"]} #{$retorno["codigo"]}";
    } else
    if (($acao == "pesquisar-e-social-s2210") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        $pagina  = 1;
        if (isset($_POST["pagina"]))
            if (is_numeric($_POST["pagina"]))
                $pagina = $_POST["pagina"];

        config::setLimitePagina(100);

        $pagina--; // inicia com 0
        $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
        $totalRegistros = 0;

        $filtro = [
            "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
        ];
        if ($_POST["dataInicial"] != '')
            $filtro["a.DATAHORA"] = array("" . funcoes::converterData($_POST["dataInicial"]) . " 00:00:00", ">=");
        if ($_POST["dataFinal"] != '')
            $filtro["DATAHORA"] = array("" . funcoes::converterData($_POST["dataFinal"]) . " 23:59:59", "<=");
        if ($_POST["situacao"] != '' and $_POST["situacao"] != 'T') {
            $tipo = array();
            if ($_POST["situacao"] == 'C') {
                array_push($tipo, 'A');
            }
            if ($_POST["situacao"] == 'I') {
                array_push($tipo, 'R');
            }
            $tipo = array($tipo, "IN");
            $filtro["C.SITUACAO"] = $tipo;
        }

        if ($_POST["nomefuncionario"] != '')
            $filtro["B.NOME"] = array($_POST["nomefuncionario"] , "CONTAINING"); ;

        $es = new esocials2210();
        if ($es->buscar(
            $filtro,
            $limite,
            array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC")
        )) {
            $totalRegistros = $es->getTotalRegistros();

            $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
            $cab = new cabecalho(array("class" => "no-border"));
            $cab->addItem("Seq", array("style" => "font-weight: bold; text-align: center; width: 5%"));
            $cab->addItem("Funcionário", array("style" => "font-weight: bold; text-align: center;"));
            $cab->addItem("Data/hora", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("Situação", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            if ($es->getItemLista(0)->getTransmiteESocial() == "S") {
                $cab->addItem("Sit. Transmissão", array("style" => "font-weight: bold; text-align: center; width: 15%"));
                $cab->addItem("#", array("style" => "font-weight: bold; text-align: center; width: 5%"));
                $cab->addItem("Número Recibo", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            }
            $cab->addItem("", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $tab->addCabecalho($cab);

            $classeBotao = "\" style=\"padding: 0px 10px;";
            $temAberto = false;

            for ($i = 0; $i < $es->getTotalLista(); $i++) {
                if ($es->getItemLista($i)->getSituacao() == "A") {
                    $situacao = "ABERTO";
                    $temAberto = true;
                } else {
                    $situacao = "BAIXADO";
                }

                $chave = $es->getItemLista($i)->getChave();
                $reg = new registro(array("class" => "no-border-y", "id" => "registro-" . $chave . ""));
                $reg->addItem($es->getItemLista($i)->getSequencial(), array("style" => "text-align: right;"));
                $reg->addItem(utf8_encode($es->getItemLista($i)->getFuncionario()->getCodigo() . ' - ' . $es->getItemLista($i)->getFuncionario()->getNome()));
                $reg->addItem(funcoes::converterData($es->getItemLista($i)->getDataHora(), true), array("style" => "text-align: center;"));
                $reg->addItem($situacao, array("style" => "text-align: center;"));


                if ($es->getItemLista($i)->getTransmiteESocial() == "S") {
                    $situacaoTransmissao = "..." . $es->getItemLista($i)->getSituacaoTransmissao();
                    if ($es->getItemLista($i)->getSituacaoTransmissao() == "P") {
                        $situacaoTransmissao = "PENDENTE";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "E") {
                        $situacaoTransmissao = "ENVIADO";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                        $situacaoTransmissao = "INCONSISTENTE";

                        $situacaoTransmissao = "<a class=\"btn btn-danger btn-xs\" onclick=\"ocorrenciaESocial('" . $es->getItemLista($i)->getChave() . "')  \""
                            . ((!funcoes::acessoCelular()) ? " style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">REJEITADO</a>";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "A") {
                        $situacaoTransmissao = "CONFORME";
                    }

                    $reg->addItem($situacaoTransmissao, array("style" => "text-align: center;"));

                    $botoes = "";
                    if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                        $botoes = "<button id=\"btn-reprocessar-" . $es->getItemLista($i)->getChave() . "\" " .
                            "type=\"button\" class=\"btn btn-info btn-xs\" " .
                            "onclick=reprocessar('" . $es->getItemLista($i)->getChave() . "')> " .
                            "<i class=\"fa fa-repeat\"></i>Processar</button> ";
                    } else {
                        $botoes = "<a class=\"btn btn-default btn-xs disabled \"  href=\"javascript: void(0)\">Processar</a>";
                    }
                    $reg->addItem($botoes, array("style" => "text-align: center;"));

                    $reg->addItem($es->getItemLista($i)->getNumeroRecibo(), array("style" => "text-align: center;"));
                }

                $botoes = "";
                if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                    $botoes = "<a class=\"btn btn-success btn-xs disabled\" attr=\"baixar-esocial\"  style=\"width: 150px;\" >Gerar</a>";
                } else {
                    $botoes = "<a class=\"btn btn-success btn-xs\" attr=\"baixar-esocial\" situacao=\"{$es->getItemLista($i)->getSituacao()}\" "
                        . "matricula=\"{$es->getItemLista($i)->getFuncionario()->getCodigo()}\" "
                        . "sequencial=\"{$es->getItemLista($i)->getSequencial()}\" "
                        . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">Gerar</a>";
                }

                $reg->addItem($botoes, array("style" => "text-align: center;"));
                $tab->addRegistro($reg);
            }

            if ($temAberto)
                echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2210')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

            echo $tab->gerar();
            echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2210')");
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Nenhum registro encontrado!";
        }

        if ($retorno["codigo"] > 0)
            echo "{$retorno["mensagem"]} #{$retorno["codigo"]}";
    } else
    if (($acao == "pesquisar-e-social-s2220") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        $pagina  = 1;
        if (isset($_POST["pagina"]))
            if (is_numeric($_POST["pagina"]))
                $pagina = $_POST["pagina"];

        config::setLimitePagina(100);

        $pagina--; // inicia com 0
        $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
        $totalRegistros = 0;

        $filtro = [
            "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
        ];
        if ($_POST["dataInicial"] != '')
            $filtro["a.DATAASO"] = array("" . funcoes::converterData($_POST["dataInicial"]) . "", ">=");
        if ($_POST["dataFinal"] != '')
            $filtro["a.DATAASO"] = array("" . funcoes::converterData($_POST["dataFinal"]) . "", "<=");

        if ($_POST["situacao"] != '' and $_POST["situacao"] != 'T') {
            $tipo = array();
            if ($_POST["situacao"] == 'C') {
                array_push($tipo, 'A');
            }
            if ($_POST["situacao"] == 'I') {
                array_push($tipo, 'R');
            }
            $tipo = array($tipo, "IN");
            $filtro["C.SITUACAO"] = $tipo;
        }
        if ($_POST["nomefuncionario"] != '')
            $filtro["B.NOME"] = array($_POST["nomefuncionario"] , "CONTAINING"); ;

        $es = new esocials2220();
        if ($es->buscar(
            $filtro,
            $limite,
            array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC")
        )) {
            $totalRegistros = $es->getTotalRegistros();

            $tab = new tabela(array("class" => "panel-group accordion accordion-semi"));
            $cab = new cabecalho(array("class" => "no-border"));
            $cab->addItem("Seq", array("style" => "font-weight: bold; text-align: center; width: 5%"));
            $cab->addItem("Funcionário", array("style" => "font-weight: bold; text-align: center;"));
            $cab->addItem("Data ASO", array("style" => "font-weight: bold; text-align: center; width: 10%"));
            $cab->addItem("Situação", array("style" => "font-weight: bold; text-align: center; width: 10%"));
            if ($es->getItemLista(0)->getTransmiteESocial() == "S") {
                $cab->addItem("Sit. Transmissão", array("style" => "font-weight: bold; text-align: center; width: 5%"));
                $cab->addItem("#", array("style" => "font-weight: bold; text-align: center; width: 5%"));
                $cab->addItem("Número Recibo", array("style" => "font-weight: bold; text-align: center; width: 10%"));
            }
            $cab->addItem("", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $tab->addCabecalho($cab);

            $classeBotao = "\" style=\"padding: 0px 10px;";
            $temAberto = false;

            for ($i = 0; $i < $es->getTotalLista(); $i++) {
                if ($es->getItemLista($i)->getSituacao() == "A") {
                    $situacao = "ABERTO";
                    $temAberto = true;
                } else {
                    $situacao = "BAIXADO";
                }
                $chave = $es->getItemLista($i)->getChave();
                $reg = new registro(array("class" => "no-border-y", "id" => "registro-" . $chave . ""));
                $reg->addItem($es->getItemLista($i)->getSequencial(), array("style" => "text-align: right;"));
                $reg->addItem(utf8_encode($es->getItemLista($i)->getFuncionario()->getCodigo() . ' - ' . $es->getItemLista($i)->getFuncionario()->getNome()));
                $reg->addItem(funcoes::converterData($es->getItemLista($i)->getDataHora(), true), array("style" => "text-align: center;"));
                $reg->addItem($situacao, array("style" => "text-align: center;"));
                if ($es->getItemLista($i)->getTransmiteESocial() == "S") {
                    if ($es->getItemLista($i)->getSituacaoTransmissao() == "P") {
                        $situacaoTransmissao = "PENDENTE";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "E") {
                        $situacaoTransmissao = "ENVIADO";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                        $situacaoTransmissao = "INCONSISTENTE";

                        $situacaoTransmissao = "<a class=\"btn btn-danger btn-xs\" onclick=\"ocorrenciaESocial('" . $es->getItemLista($i)->getChave() . "')  \""
                            . ((!funcoes::acessoCelular()) ? " style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">REJEITADO</a>";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "A") {
                        $situacaoTransmissao = "CONFORME";
                    }

                    $reg->addItem($situacaoTransmissao, array("style" => "text-align: center;"));

                    $botoes = "";
                    if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                        $botoes = "<button id=\"btn-reprocessar-" . $es->getItemLista($i)->getChave() . "\" " .
                            "type=\"button\" class=\"btn btn-info btn-xs\" " .
                            "onclick=reprocessar('" . $es->getItemLista($i)->getChave() . "')> " .
                            "<i class=\"fa fa-repeat\"></i>Processar</button> ";
                    } else {
                        $botoes = "<a class=\"btn btn-default btn-xs disabled \"  href=\"javascript: void(0)\">Processar</a>";
                    }
                    $reg->addItem($botoes, array("style" => "text-align: center;"));

                    $reg->addItem($es->getItemLista($i)->getNumeroRecibo(), array("style" => "text-align: center;"));
                }

                $botoes = "";
                if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                    $botoes = "<a class=\"btn btn-success btn-xs disabled\" attr=\"baixar-esocial\" style=\"width: 150px;\" >Gerar</a>";
                } else {
                    $botoes = "<a class=\"btn btn-success btn-xs\" attr=\"baixar-esocial\" situacao=\"{$es->getItemLista($i)->getSituacao()}\" "
                        . "matricula=\"{$es->getItemLista($i)->getFuncionario()->getCodigo()}\" "
                        . "sequencial=\"{$es->getItemLista($i)->getSequencial()}\" "
                        . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">Gerar</a>";
                }

                $reg->addItem($botoes, array("style" => "text-align: center;"));
                $tab->addRegistro($reg);
            }

            if ($temAberto)
                echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2220')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

            echo $tab->gerar();
            echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2220')");
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Nenhum registro encontrado!";
        }

        if ($retorno["codigo"] > 0)
            echo "{$retorno["mensagem"]} #{$retorno["codigo"]}";
    } else
    if (($acao == "pesquisar-e-social-s2221") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        $pagina  = 1;
        if (isset($_POST["pagina"]))
            if (is_numeric($_POST["pagina"]))
                $pagina = $_POST["pagina"];

        config::setLimitePagina(100);

        $pagina--; // inicia com 0
        $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
        $totalRegistros = 0;

        $es = new esocials2221();
        if ($es->buscar(
            array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                //"A.SITUACAO" => "A"
            ),
            $limite,
            array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC")
        )) {
            $totalRegistros = $es->getTotalRegistros();

            $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
            $cab = new cabecalho(array("class" => "no-border"));
            $cab->addItem("Seq", array("style" => "font-weight: bold; text-align: center; width: 5%"));
            $cab->addItem("Funcionário", array("style" => "font-weight: bold; text-align: center;"));
            $cab->addItem("Data/hora", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("Situação", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $tab->addCabecalho($cab);

            $classeBotao = "\" style=\"padding: 0px 10px;";
            $temAberto = false;

            for ($i = 0; $i < $es->getTotalLista(); $i++) {
                if ($es->getItemLista($i)->getSituacao() == "A") {
                    $situacao = "ABERTO";
                    $temAberto = true;
                } else {
                    $situacao = "BAIXADO";
                }

                $reg = new registro(array("class" => "no-border-y"));
                $reg->addItem($es->getItemLista($i)->getSequencial(), array("style" => "text-align: right;"));
                $reg->addItem(utf8_encode($es->getItemLista($i)->getFuncionario()->getCodigo() . ' - ' . $es->getItemLista($i)->getFuncionario()->getNome()));
                $reg->addItem(funcoes::converterData($es->getItemLista($i)->getDataHora(), true), array("style" => "text-align: center;"));
                $reg->addItem($situacao, array("style" => "text-align: center;"));

                $botoes = "<a class=\"btn btn-default btn-xs\" attr=\"baixar-esocial\" situacao=\"{$es->getItemLista($i)->getSituacao()}\" "
                    . "matricula=\"{$es->getItemLista($i)->getFuncionario()->getCodigo()}\" "
                    . "sequencial=\"{$es->getItemLista($i)->getSequencial()}\" "
                    . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">Gerar</a>";

                $reg->addItem($botoes, array("style" => "text-align: center;"));
                $tab->addRegistro($reg);
            }

            if ($temAberto)
                echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2221')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

            echo $tab->gerar();
            echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2221')");
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Nenhum registro encontrado!";
        }

        if ($retorno["codigo"] > 0)
            echo "{$retorno["mensagem"]} #{$retorno["codigo"]}";
    } else
    if (($acao == "pesquisar-e-social-s2240") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        $pagina  = 1;
        if (isset($_POST["pagina"]))
            if (is_numeric($_POST["pagina"]))
                $pagina = $_POST["pagina"];

        config::setLimitePagina(100);

        $pagina--; // inicia com 0
        $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
        $totalRegistros = 0;

        $filtro = [
            "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
        ];
        if ($_POST["dataInicial"] != '')
            $filtro["a.DATAINICIO_CONDICAO"] = array("" . funcoes::converterData($_POST["dataInicial"]) . "", ">=");
        if ($_POST["dataFinal"] != '')
            $filtro["a.DATAINICIO_CONDICAO"] = array("" . funcoes::converterData($_POST["dataFinal"]) . "", "<=");
        if ($_POST["nomefuncionario"] != '')
            $filtro["B.NOME"] = array($_POST["nomefuncionario"] , "CONTAINING"); 

        if ($_POST["situacao"] != '' and $_POST["situacao"] != 'T'){
            $tipo = array();
            if ($_POST["situacao"] == 'C'){
                array_push($tipo , 'A');
            }
            if ($_POST["situacao"] == 'I'){
                array_push($tipo ,'R');
            }
            $tipo = array($tipo, "IN");
            $filtro["C.SITUACAO"] = $tipo; 
        }

    
        $es = new esocials2240();
        if ($es->buscar(
            $filtro,
            $limite,
            array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC")
        )) {
            $totalRegistros = $es->getTotalRegistros();

            $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
            $cab = new cabecalho(array("class" => "no-border"));
            $cab->addItem("Seq", array("style" => "font-weight: bold; text-align: center; width: 5%"));
            $cab->addItem("Funcionário", array("style" => "font-weight: bold; text-align: center;"));
            $cab->addItem("Data Início Exp.", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("Situação", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            if ($es->getItemLista(0)->getTransmiteESocial() == "S") {
                $cab->addItem("Sit. Transmissão", array("style" => "font-weight: bold; text-align: center; width: 15%"));
                $cab->addItem("#", array("style" => "font-weight: bold; text-align: center; width: 5%"));
                $cab->addItem("Número Recibo", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            }
            $cab->addItem("", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $tab->addCabecalho($cab);

            $classeBotao = "\" style=\"padding: 0px 10px;";
            $temAberto = false;

            for ($i = 0; $i < $es->getTotalLista(); $i++) {
                if ($es->getItemLista($i)->getSituacao() == "A") {
                    $situacao = "ABERTO";
                    $temAberto = true;
                } else {
                    $situacao = "BAIXADO";
                }
                $chave = $es->getItemLista($i)->getChave();
                $reg = new registro(array("class" => "no-border-y", "id" => "registro-" . $chave . ""));
                $reg->addItem($es->getItemLista($i)->getSequencial(), array("style" => "text-align: right;"));
                $reg->addItem(utf8_encode($es->getItemLista($i)->getFuncionario()->getCodigo() . ' - ' . $es->getItemLista($i)->getFuncionario()->getNome()));
                $reg->addItem(funcoes::converterData($es->getItemLista($i)->getDataHora(), true), array("style" => "text-align: center;"));
                $reg->addItem($situacao, array("style" => "text-align: center;"));

                if ($es->getItemLista($i)->getTransmiteESocial() == "S") {
                    if ($es->getItemLista($i)->getSituacaoTransmissao() == "P") {
                        $situacaoTransmissao = "PENDENTE";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "E") {
                        $situacaoTransmissao = "ENVIADO";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                        $situacaoTransmissao = "INCONSISTENTE";

                        $situacaoTransmissao = "<a class=\"btn btn-danger btn-xs\" onclick=\"ocorrenciaESocial('" . $es->getItemLista($i)->getChave() . "')  \""
                            . ((!funcoes::acessoCelular()) ? " style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">REJEITADO</a>";
                    } else if ($es->getItemLista($i)->getSituacaoTransmissao() == "A") {
                        $situacaoTransmissao = "CONFORME";
                    }

                    $reg->addItem($situacaoTransmissao, array("style" => "text-align: center;"));

                    $botoes = "";
                    if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                        $botoes = "<button id=\"btn-reprocessar-" . $es->getItemLista($i)->getChave() . "\" " .
                            "type=\"button\" class=\"btn btn-info btn-xs\" " .
                            "onclick=reprocessar('" . $es->getItemLista($i)->getChave() . "')> " .
                            "<i class=\"fa fa-repeat\"></i>Processar</button> ";
                    } else {
                        $botoes = "<a class=\"btn btn-default btn-xs disabled \"  href=\"javascript: void(0)\">Processar</a>";
                    }
                    $reg->addItem($botoes, array("style" => "text-align: center;"));

                    $reg->addItem($es->getItemLista($i)->getNumeroRecibo(), array("style" => "text-align: center;"));
                }

                $botoes = "";
                if ($es->getItemLista($i)->getSituacaoTransmissao() == "R") {
                    $botoes = "<a class=\"btn btn-success btn-xs disabled\" attr=\"baixar-esocial\" style=\"width: 150px;\" >Gerar</a>";
                } else {
                    $botoes = "<a class=\"btn btn-success btn-xs\" attr=\"baixar-esocial\" situacao=\"{$es->getItemLista($i)->getSituacao()}\" "
                        . "matricula=\"{$es->getItemLista($i)->getFuncionario()->getCodigo()}\" "
                        . "sequencial=\"{$es->getItemLista($i)->getSequencial()}\" "
                        . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">Gerar</a>";
                }

                $reg->addItem($botoes, array("style" => "text-align: center;"));
                $tab->addRegistro($reg);
            }

            if ($temAberto)
                echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2240')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

            echo $tab->gerar();
            echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2240')");
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Nenhum registro encontrado!";
        }

        if ($retorno["codigo"] > 0)
            echo "{$retorno["mensagem"]} #{$retorno["codigo"]}";
    } else
    if (($acao == "salvar-certificado-digital") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_FILES["arquivo"]) && isset($_POST["senha"])) {
            $senha = $_POST["senha"];

            if ($_FILES["arquivo"]["type"] === "application/x-pkcs12") {
                $pathTemp = sys_get_temp_dir() . "\\" . time() . ".pfx";

                if (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $pathTemp)) {
                    $certificado = file_get_contents($pathTemp);

                    if (\openssl_pkcs12_read($certificado, $ret, $senha)) {
                        $dados = \openssl_x509_parse(\openssl_x509_read($ret['cert']));

                        $validade = strtotime(date('Y-m-d H:i:s', $dados['validTo_time_t']));
                        $dataHoje = time();

                        if ($validade > $dataHoje) {
                            $senha = criptografia::criptografar($senha);
                            $validade = date('Y-m-d', $validade);

                            if (!$empresa->salvarCertificadoDigital($_SESSION[config::getSessao()]["unidade"], $_SESSION[config::getSessao()]["empresa_ativa"], $certificado, $senha, $validade)) {
                                $retorno["codigo"] = 6;
                                $retorno["mensagem"] = "Não foi possível salvar o certificado!";
                            }
                        } else {
                            $retorno["codigo"] = 5;
                            $retorno["mensagem"] = "Certificado expirado!";
                        }
                    } else {
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Não foi possível ler o arquivo, verifique a senha!";
                    }
                } else {
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Não foi possível fazer o upload!";
                }
            } else {
                $retorno["codigo"] = 2;
                $retorno["mensagem"] = "Arquivo não suportado!";
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        echo json_encode($retorno);
        die;
    } else
    if (($acao == "pesquisar-e-social-s2245") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        $pagina  = 1;
        if (isset($_POST["pagina"]))
            if (is_numeric($_POST["pagina"]))
                $pagina = $_POST["pagina"];

        config::setLimitePagina(100);

        $pagina--; // inicia com 0
        $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
        $totalRegistros = 0;

        $es = new esocials2245();
        if ($es->buscar(
            array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                //"A.SITUACAO" => "A"
            ),
            $limite,
            array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC")
        )) {
            $totalRegistros = $es->getTotalRegistros();

            $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
            $cab = new cabecalho(array("class" => "no-border"));
            $cab->addItem("Seq", array("style" => "font-weight: bold; text-align: center; width: 5%"));
            $cab->addItem("Funcionário", array("style" => "font-weight: bold; text-align: center;"));
            $cab->addItem("Data/hora", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("Situação", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $cab->addItem("", array("style" => "font-weight: bold; text-align: center; width: 15%"));
            $tab->addCabecalho($cab);

            $classeBotao = "\" style=\"padding: 0px 10px;";
            $temAberto = false;

            for ($i = 0; $i < $es->getTotalLista(); $i++) {
                if ($es->getItemLista($i)->getSituacao() == "A") {
                    $situacao = "ABERTO";
                    $temAberto = true;
                } else {
                    $situacao = "BAIXADO";
                }

                $reg = new registro(array("class" => "no-border-y"));
                $reg->addItem($es->getItemLista($i)->getSequencial(), array("style" => "text-align: right;"));
                $reg->addItem(utf8_encode($es->getItemLista($i)->getFuncionario()->getCodigo() . ' - ' . $es->getItemLista($i)->getFuncionario()->getNome()));
                $reg->addItem(funcoes::converterData($es->getItemLista($i)->getDataHora(), true), array("style" => "text-align: center;"));
                $reg->addItem($situacao, array("style" => "text-align: center;"));

                $botoes = "<a class=\"btn btn-default btn-xs\" attr=\"baixar-esocial\" situacao=\"{$es->getItemLista($i)->getSituacao()}\" "
                    . "matricula=\"{$es->getItemLista($i)->getFuncionario()->getCodigo()}\" "
                    . "sequencial=\"{$es->getItemLista($i)->getSequencial()}\" "
                    . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "") . " href=\"javascript: void(0)\">Gerar</a>";

                $reg->addItem($botoes, array("style" => "text-align: center;"));
                $tab->addRegistro($reg);
            }

            if ($temAberto)
                echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2245')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

            echo $tab->gerar();
            echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2245')");
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Nenhum registro encontrado!";
        }

        if ($retorno["codigo"] > 0)
            echo "{$retorno["mensagem"]} #{$retorno["codigo"]}";
    }

    die;
}

if (isset($_GET["a"])) {
    $acao = $_GET["a"];

    if (($acao == "gerar-s1060") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_GET["posto"]) && isset($_GET["setor"]) && isset($_GET["sequencial"])) {
            $es = new esocials1060();
            if ($es->buscar(array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                "A.POSTOTRABALHO" => $_GET["posto"],
                "A.SETOR" => $_GET["setor"],
                "A.SEQUENCIAL" => $_GET["sequencial"]
            ))) {

                $es->gerarXML(
                    $_SESSION[config::getSessao()]["unidade"],
                    $_SESSION[config::getSessao()]["empresa_ativa"],
                    $_GET["posto"],
                    $_GET["setor"],
                    $_GET["sequencial"],
                    $_SESSION[config::getSessao()]["codigo"],
                    $es->getItemLista(0)->getXML()
                );
                die;
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        if ($retorno["codigo"] > 0) {
            echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
        }
    } else
    if (($acao == "gerar-s2210") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
            $es = new esocials2210();
            if ($es->buscar(array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                "A.MATRICULA" => $_GET["matricula"],
                "A.SEQUENCIAL" => $_GET["sequencial"]
            ))) {

                $es->gerarXML(
                    $_SESSION[config::getSessao()]["unidade"],
                    $_SESSION[config::getSessao()]["empresa_ativa"],
                    $_GET["matricula"],
                    $_GET["sequencial"],
                    $_SESSION[config::getSessao()]["codigo"],
                    $es->getItemLista(0)->getXML()
                );
                die;
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        if ($retorno["codigo"] > 0) {
            echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
        }
    } else
    if (($acao == "gerar-s2220") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
            $es = new esocials2220();
            if ($es->buscar(array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                "A.MATRICULA" => $_GET["matricula"],
                "A.SEQUENCIAL" => $_GET["sequencial"]
            ))) {

                $es->gerarXML(
                    $_SESSION[config::getSessao()]["unidade"],
                    $_SESSION[config::getSessao()]["empresa_ativa"],
                    $_GET["matricula"],
                    $_GET["sequencial"],
                    $_SESSION[config::getSessao()]["codigo"],
                    $es->getItemLista(0)->getXML()
                );
                die;
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        if ($retorno["codigo"] > 0) {
            echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
        }
    } else
    if (($acao == "gerar-s2221") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
            $es = new esocials2221();
            if ($es->buscar(array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                "A.MATRICULA" => $_GET["matricula"],
                "A.SEQUENCIAL" => $_GET["sequencial"]
            ))) {

                $es->gerarXML(
                    $_SESSION[config::getSessao()]["unidade"],
                    $_SESSION[config::getSessao()]["empresa_ativa"],
                    $_GET["matricula"],
                    $_GET["sequencial"],
                    $_SESSION[config::getSessao()]["codigo"],
                    $es->getItemLista(0)->getXML()
                );
                die;
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        if ($retorno["codigo"] > 0) {
            echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
        }
    } else
    if (($acao == "gerar-s2240") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
            $es = new esocials2240();
            if ($es->buscar(array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                "A.MATRICULA" => $_GET["matricula"],
                "A.SEQUENCIAL" => $_GET["sequencial"]
            ))) {

                $es->gerarXML(
                    $_SESSION[config::getSessao()]["unidade"],
                    $_SESSION[config::getSessao()]["empresa_ativa"],
                    $_GET["matricula"],
                    $_GET["sequencial"],
                    $_SESSION[config::getSessao()]["codigo"],
                    $es->getItemLista(0)->getXML()
                );
                die;
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        if ($retorno["codigo"] > 0) {
            echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
        }
    } else
    if (($acao == "gerar-s2245") && $parametro->getHabilitaGeracaoeSocial()) {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
            $es = new esocials2245();
            if ($es->buscar(array(
                "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                "A.MATRICULA" => $_GET["matricula"],
                "A.SEQUENCIAL" => $_GET["sequencial"]
            ))) {

                $es->gerarXML(
                    $_SESSION[config::getSessao()]["unidade"],
                    $_SESSION[config::getSessao()]["empresa_ativa"],
                    $_GET["matricula"],
                    $_GET["sequencial"],
                    $_SESSION[config::getSessao()]["codigo"],
                    $es->getItemLista(0)->getXML()
                );
                die;
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        if ($retorno["codigo"] > 0) {
            echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
        }
    }

    die;
}
?>

<div class="page-head">
    <h2>e-Social</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li class="active">e-Social</li>
    </ol>
</div>

<div class="block-flat">
    Certificado digital valido até <strong><?php echo funcoes::converterData($empresa->getValidadeCertificadoDigital()) ?></strong>
    <hr />
    Selecione o arquivo: <input type="file" name="arquivo" id="arquivoCertificadoDigital" style="display: initial;">&nbsp;&nbsp;&nbsp;&nbsp;
    Informe a senha: <input type="password" name="senha" id="senhaCertificadoDigital">
    <button type="button" class="btn btn-primary" onclick="atualizarCertificadoDigital()">Atualizar</button>
    <hr />
    <?php if ($empresa->getGerarXMLEsocial() == "S") :?>
        <div class="form-group-sm">
            <h4 style="font-weight: bold;">Filtro</h4>
            <div class="col-sm-2" style="height: 58px;">
                <label>Data Inicial:</label>
                <div class="input-group">
                    <input type="text" data-mask="date" autocomplete="no" placeholder="DD/MM/YYYY" id="filtro-input-data-inferior" class="form-control datetime" style="background-color: #fff; border-radius: 3px; text-align: center;">
                </div>
            </div>
            <div class="col-sm-2" style="height: 58px;">
                <label>Data Final:</label>
                <div class="input-group">
                    <input type="text" data-mask="date" autocomplete="no" placeholder="DD/MM/YYYY" id="filtro-input-data-superior" class="form-control datetime" style="background-color: #fff; border-radius: 3px; text-align: center;">
                </div>
            </div>
            <div class="col-sm-6" style="height: 58px;">
                <label>Nome Funcionário:</label>
                <input type="text" id="filtro-input-nome-funcionario" class="form-control input-sm">
            </div>
            <div class="col-sm-2" style="height: 58px;">
                <label>Situação:</label>
                <select id="form-select-situacao" class="form-control input-sm">
                    <option value="T">Todos</option>
                    <option value="C">Conforme</option>
                    <option value="I">Inconsistente</option>
                </select>
            </div>

            <div class="col-sm-2" style="height: 58px;">
                <label>.</label>
                <div class="input-group">
                    <button type="button" onclick="atualizarTabs()" data-dismiss="modal" class="btn btn-primary" style="height: 30px; padding-top: 4px">Filtrar/Atualizar</button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr />
        <ul class="nav nav-tabs">
            <!-- <li><a href="#e-social-s1060" data-toggle="tab">S-1060 - Ambientes de trabalho</a></li> -->
            <li><a href="#e-social-s2210" data-toggle="tab">S-2210 - Comunicação de acidente de trabalho</a></li>
            <li class="active"><a href="#e-social-s2220" data-toggle="tab">S-2220 - Monitoramento da saúde</a></li>
            <!-- <li><a href="#e-social-s2221" data-toggle="tab">S-2221 - Exame toxicológico</a></li> -->
            <li><a href="#e-social-s2240" data-toggle="tab">S-2240 - Condições ambientais</a></li>
            <!-- <li><a href="#e-social-s2245" data-toggle="tab">S-2245 - Treinamentos e capacitações</a></li> -->
        </ul>
        <div class="tab-content" style="margin-bottom: 10px;">
            <!-- <div id="e-social-s1060" class="tab-pane active cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s1060"></div>
                    <div class="clearfix"></div>
                </div>
            </div> -->
            <div id="e-social-s2210" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2210"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div id="e-social-s2220" class="tab-pane cont active">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2220"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- <div id="e-social-s2221" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2221"></div>
                    <div class="clearfix"></div>
                </div>
            </div> -->
            <div id="e-social-s2240" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2240"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- <div id="e-social-s2245" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2245"></div>
                    <div class="clearfix"></div>
                </div>
            </div> -->
        </div>
    <?php endif; ?>
</div>
</div>
<div id="div-e-social-download" style="display: none"></div>

<div id="div-modal-ocorrencias" tabindex="-1" role="dialog" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close">×</button>
                <h3>Ocorrências</h3>
            </div>
            <div id="div-body-ocorrencia" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary" style="height: 30px; padding-top: 4px">OK</button>
            </div>
        </div>
    </div>
</div>
<script>
    var paginaAtual = 1;
    var contDivESocial = 0;

    $(document).ready(function() {
        $('.icheck').iCheck({
            radioClass: 'iradio_square-blue'
        });
        var dateOffset = (24 * 60 * 60 * 1000) * 30; //30 days
        var data = new Date();
        data.setTime(data.getTime() - dateOffset);
        var dia = String(data.getDate()).padStart(2, '0');
        // if ((data.getMonth()+1)= 13)
        //     var mes = '01'
        // else    
        var mes = String(data.getMonth() + 1).padStart(2, '0')
        var ano = data.getFullYear();
        dataInicio = '01/' + mes + '/' + ano;

        var data = new Date();
        // data.setTime(data.getTime() + dateOffset);
        var dia = String(data.getDate()).padStart(2, '0');
        var mes = String(data.getMonth() + 1).padStart(2, '0');
        var ano = data.getFullYear();
        dataFinal = '01/' + mes + '/' + ano;

        $('#filtro-input-data-inferior').val(dataInicio);
        $('#filtro-input-data-inferior').datepicker({
            autoclose: true
        });
        $('#filtro-input-data-inferior').mask('99/99/9999');

        $('#filtro-input-data-superior').val(dataFinal);
        $('#filtro-input-data-superior').datepicker({
            autoclose: true
        });
        $('#filtro-input-data-superior').mask('99/99/9999');

        // pesquisarESocial('1', 's1060');
        pesquisarESocial('1', 's2210', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
        pesquisarESocial('1', 's2220', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
        // pesquisarESocial('1', 's2221');
        pesquisarESocial('1', 's2240', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
        // pesquisarESocial('1', 's2245');
    });

    function atualizarCertificadoDigital() {
        if ($('#arquivoCertificadoDigital').val() === '') {
            $.gritter.add({
                title: 'Ops!',
                text: 'Selecione o arquivo!',
                class_name: 'danger'
            });
        } else
        if ($('#senhaCertificadoDigital').val() === '') {
            $.gritter.add({
                title: 'Ops!',
                text: 'Informe a senha do certificado digital!',
                class_name: 'danger'
            });
        } else {
            var data = new FormData();
            data.append('arquivo', $('#arquivoCertificadoDigital').prop('files')[0]);
            data.append('a', 'salvar-certificado-digital');
            data.append('senha', $('#senhaCertificadoDigital').val());

            $.ajax({
                url: 'esocial.php',
                type: 'post',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.codigo == '0') {
                        $.gritter.add({
                            title: 'Oba!',
                            text: 'Certificado atualizado!',
                            class_name: 'success'
                        });
                        $('#arquivoCertificadoDigital').val('');
                        $('#senhaCertificadoDigital').val('');
                    } else {
                        $.gritter.add({
                            title: 'Ops!',
                            text: data.mensagem + ' #' + data.codigo,
                            class_name: 'danger'
                        });
                    }
                },
                error: function() {
                    $.gritter.add({
                        title: 'Ops!',
                        text: 'Não foi possível enviar o arquivo!',
                        class_name: 'danger'
                    });
                }
            });
        }
    }

    $('#form-button-pesquisar-s1060').on('click', function() {
        pesquisarESocial('1', 's1060', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
    });

    $('#form-button-pesquisar-s2210').on('click', function() {
        pesquisarESocial('1', 's2210', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val()), $('#filtro-input-nome-funcionario').val();
    });

    $('#form-button-pesquisar-s2220').on('click', function() {
        pesquisarESocial('1', 's2220', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
    });

    $('#form-button-pesquisar-s2221').on('click', function() {
        pesquisarESocial('1', 's2221', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
    });

    $('#form-button-pesquisar-s2240').on('click', function() {
        pesquisarESocial('1', 's2240', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
    });

    $('#form-button-pesquisar-s2245').on('click', function() {
        pesquisarESocial('1', 's2245', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
    });

    function atualizarTabs() {
        pesquisarESocial('1', 's2210', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
        pesquisarESocial('1', 's2220', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
        pesquisarESocial('1', 's2240', $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());

    }

    function baixarTodosAbertos(origem) {
        var habilitaDownload = false;

        if ($('#div-e-social-' + origem).find('a[attr="baixar-esocial"][situacao="A"]').length) {
            var obj = $('#div-e-social-' + origem).find('a[attr="baixar-esocial"][situacao="A"]').first();
            habilitaDownload = true;

            contDivESocial++;
            var nomeDiv = 'div-e-social-download-' + contDivESocial;
            var url = '';

            if (origem == 's1060') {
                url = 'esocial.php?a=gerar-' + origem +
                    '&posto=' + $(obj).attr('posto') +
                    '&setor=' + $(obj).attr('setor') +
                    '&sequencial=' + $(obj).attr('sequencial');
            } else {
                url = 'esocial.php?a=gerar-' + origem +
                    '&matricula=' + $(obj).attr('matricula') +
                    '&sequencial=' + $(obj).attr('sequencial');
            }

            $('<div/>', {
                id: nomeDiv
            }).appendTo($('body'));

            $('#' + nomeDiv).html('<iframe style="display:none" src="' + url + '"></iframe>');
            $(obj).remove();

            if (habilitaDownload) {
                setTimeout(function() {
                    baixarTodosAbertos(origem);
                }, 100);
            }
        } else {
            setTimeout(function() {
                pesquisarESocial('1', origem, $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
            }, 100);
        }
    }

    function pesquisarESocial(pagina, origem, dataInicial, dataFinal, situacao, nomefuncionario) {
        paginaAtual = pagina;
        // console.log(dataInicial);
        // console.log(nomefuncionario);

        $('#div-e-social-' + origem).html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-e-social-' + origem).load('esocial.php', {
            'a': 'pesquisar-e-social-' + origem,
            'pagina': pagina,
            'dataInicial': dataInicial,
            'dataFinal': dataFinal,
            'situacao': situacao,
            'nomefuncionario': nomefuncionario
        }, function() {
            $('#div-e-social-' + origem + ' a').each(function() {
                if (origem == 's1060') {
                    $(this).on('click', function() {
                        var url = 'esocial.php?a=gerar-' + origem +
                            '&posto=' + $(this).attr('posto') +
                            '&setor=' + $(this).attr('setor') +
                            '&sequencial=' + $(this).attr('sequencial');

                        $('#div-e-social-download').html('<iframe id="iframe-e-social" style="display:none" src="' + url + '"></iframe>');

                        setTimeout(function() {
                            pesquisarESocial(paginaAtual, origem, $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
                        }, 300);
                    });
                } else {
                    $(this).on('click', function() {
                        var url = 'esocial.php?a=gerar-' + origem +
                            '&matricula=' + $(this).attr('matricula') +
                            '&sequencial=' + $(this).attr('sequencial');

                        $('#div-e-social-download').html('<iframe id="iframe-e-social" style="display:none" src="' + url + '"></iframe>');

                        setTimeout(function() {
                            pesquisarESocial(paginaAtual, origem, $('#filtro-input-data-inferior').val(), $('#filtro-input-data-superior').val(), $('#form-select-situacao').find('option:selected').val(), $('#filtro-input-nome-funcionario').val());
                        }, 300);
                    });
                }
            });
        });
    }

    function ocorrenciaESocial(chave) {
        // alert(chave);
        $('#div-modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-body-ocorrencia').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal-ocorrencias').modal('show');


        $.ajax({
            type: 'post',
            url: 'esocial.php',
            data: 'a=pesquisar-e-social-ocorrencia&chave=' + chave,
            // dataType: 'json',
            async: true,
            cache: false
        }).done(function(data) {
            // console.log(data);
            $('#div-body-ocorrencia').html(data);
            // $('#div-modal-cadastro').modal('toggle');
        });
    }

    function reprocessar(chave) {
        // console.log(chave);
        $.ajax({
            type: 'post',
            url: 'esocial.php',
            data: 'a=reprocessar-e-social&chave=' + chave,
            // dataType: 'json',
            async: true,
            cache: false
        }).done(function(data) {
            $.gritter.add({
                title: 'Oba!',
                text: 'O registro foi enviado para reprocessamento. Atualize a página em alguns instantes!',
                class_name: 'success'
            });
            $('#btn-reprocessar-' + chave).addClass('disabled');
            $('#btn-reprocessar-' + chave).find('i').attr('class', 'fa fa-spinner fa-spin');

            // var lista = $('#table-parte-corpo tbody');
            // $(lista).find("tr").each(function(index, tr) {
            //     partesAtingidas.push([$(tr).find('#codigo-parte-corpo').html(),
            //         $(tr).find('#codigo-lateralidade').html()
            //     ])
            // });
        });
    }
</script>