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

    config::verificaLogin();
    
    if ($_SESSION[config::getSessao()]["esocial"] != "S"){
        header("Location: 403.php");
        die;
    }

    $empresa = new empresa();
    $empresa->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                           "A.CODIGO" => $_SESSION[config::getSessao()]["empresa_ativa"]));
    $empresa = $empresa->getItemLista(0);

    $parametro = new parametrosgerais();
    $parametro->buscar();
    $parametro = $parametro->getItemLista(0);
    
    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
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
            if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                  "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                                  //"A.SITUACAO" => "A"
                                ),
                            $limite, array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC"))){
                $totalRegistros = $es->getTotalRegistros();

                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Seq", array("style" => "font-weight: bold; text-align: center; width: 5%"));
                $cab->addItem("Posto de trabalho", array("style" => "font-weight: bold; text-align: center;"));
                $cab->addItem("Setor", array("style" => "font-weight: bold; text-align: center;"));
                $cab->addItem("Data/hora", array("style" => "font-weight: bold; text-align: center; width: 15%"));
                $cab->addItem("Situação", array("style" => "font-weight: bold; text-align: center; width: 15%"));
                $cab->addItem("", array("style" => "font-weight: bold; text-align: center; width: 15%"));
                $tab->addCabecalho($cab);

                $classeBotao = "\" style=\"padding: 0px 10px;";
                $temAberto = false;

                for ($i = 0; $i < $es->getTotalLista(); $i++){
                    if ($es->getItemLista($i)->getSituacao() == "A") {
                        $situacao = "ABERTO";
                        $temAberto = true;
                    } else {
                        $situacao = "BAIXADO";
                    }

                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem($es->getItemLista($i)->getSequencial(), array("style" => "text-align: right;"));
                    $reg->addItem(utf8_encode($es->getItemLista($i)->getPostoTrabalho()->getCodigo() . ' - ' . $es->getItemLista($i)->getPostoTrabalho()->getDescricao()));
                    $reg->addItem(utf8_encode($es->getItemLista($i)->getSetor()->getCodigo() . ' - ' . $es->getItemLista($i)->getSetor()->getNome()));
                    $reg->addItem(funcoes::converterData($es->getItemLista($i)->getDataHora(), true), array("style" => "text-align: center;"));
                    $reg->addItem($situacao, array("style" => "text-align: center;"));

                    $botoes = "<a class=\"btn btn-default btn-xs\" attr=\"baixar-esocial\" situacao=\"{$es->getItemLista($i)->getSituacao()}\" "
                            . "posto=\"{$es->getItemLista($i)->getPostoTrabalho()->getCodigo()}\" "
                            . "setor=\"{$es->getItemLista($i)->getSetor()->getCodigo()}\" "
                            . "sequencial=\"{$es->getItemLista($i)->getSequencial()}\" "
                            . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "")." href=\"javascript: void(0)\">Gerar</a>";

                    $reg->addItem($botoes, array("style" => "text-align: center;"));
                    $tab->addRegistro($reg);
                }

                if ($temAberto)
                    echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s1060')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

                echo $tab->gerar();
                echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's1060')");
            }else{
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

                $es = new esocials2210();
                if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                                      //"A.SITUACAO" => "A"
                                    ),
                                $limite, array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC"))){
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

                    for ($i = 0; $i < $es->getTotalLista(); $i++){
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
                                . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "")." href=\"javascript: void(0)\">Gerar</a>";

                        $reg->addItem($botoes, array("style" => "text-align: center;"));
                        $tab->addRegistro($reg);
                    }

                    if ($temAberto)
                        echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2210')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

                    echo $tab->gerar();
                    echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2210')");
                }else{
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

                    $es = new esocials2220();
                    if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                                          //"A.SITUACAO" => "A"
                                        ),
                                    $limite, array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC"))){
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

                        for ($i = 0; $i < $es->getTotalLista(); $i++){
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
                                    . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "")." href=\"javascript: void(0)\">Gerar</a>";

                            $reg->addItem($botoes, array("style" => "text-align: center;"));
                            $tab->addRegistro($reg);
                        }

                        if ($temAberto)
                            echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2220')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

                        echo $tab->gerar();
                        echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2220')");
                    }else{
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
                        if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                              "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                                              //"A.SITUACAO" => "A"
                                            ),
                                        $limite, array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC"))){
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

                            for ($i = 0; $i < $es->getTotalLista(); $i++){
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
                                        . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "")." href=\"javascript: void(0)\">Gerar</a>";

                                $reg->addItem($botoes, array("style" => "text-align: center;"));
                                $tab->addRegistro($reg);
                            }

                            if ($temAberto)
                                echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2221')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

                            echo $tab->gerar();
                            echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2221')");
                        }else{
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

                            $es = new esocials2240();
                            if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                  "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                                                  //"A.SITUACAO" => "A"
                                                ),
                                            $limite, array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC"))){
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

                                for ($i = 0; $i < $es->getTotalLista(); $i++){
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
                                            . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "")." href=\"javascript: void(0)\">Gerar</a>";

                                    $reg->addItem($botoes, array("style" => "text-align: center;"));
                                    $tab->addRegistro($reg);
                                }

                                if ($temAberto)
                                    echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2240')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

                                echo $tab->gerar();
                                echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2240')");
                            }else{
                                $retorno["codigo"] = 1;
                                $retorno["mensagem"] = "Nenhum registro encontrado!";
                            }

                            if ($retorno["codigo"] > 0)
                                echo "{$retorno["mensagem"]} #{$retorno["codigo"]}";
                        } else
                            if (($acao == "salvar-certificado-digital") && $parametro->getHabilitaGeracaoeSocial()) {
                                $retorno = array("codigo" => 0, "mensagem" => "");

                                if (isset($_FILES["arquivo"]) && isset($_POST["senha"])){
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
                                }else{
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
                                    if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]
                                                          //"A.SITUACAO" => "A"
                                                        ),
                                                    $limite, array("A.DATAHORA" => "DESC", "A.SEQUENCIAL" => "DESC"))){
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

                                        for ($i = 0; $i < $es->getTotalLista(); $i++){
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
                                                    . ((!funcoes::acessoCelular()) ? "style=\"width: 150px;\"" : "")." href=\"javascript: void(0)\">Gerar</a>";

                                            $reg->addItem($botoes, array("style" => "text-align: center;"));
                                            $tab->addRegistro($reg);
                                        }

                                        if ($temAberto)
                                            echo "<button type=\"button\" class=\"btn btn btn-primary\" onclick=\"baixarTodosAbertos('s2245')\"><i class=\"fa fa-download\"></i> Baixar todos os abertos</button>";

                                        echo $tab->gerar();
                                        echo funcoes::gerarPaginacao($pagina + 1, $totalRegistros, "pesquisarESocial('<pagina>', 's2245')");
                                    }else{
                                        $retorno["codigo"] = 1;
                                        $retorno["mensagem"] = "Nenhum registro encontrado!";
                                    }

                                    if ($retorno["codigo"] > 0)
                                        echo "{$retorno["mensagem"]} #{$retorno["codigo"]}";
                                }

        die;
    }

    if (isset($_GET["a"])){
        $acao = $_GET["a"];
        
        if (($acao == "gerar-s1060") && $parametro->getHabilitaGeracaoeSocial()){
            $retorno = array("codigo" => 0, "mensagem" => "");

            if (isset($_GET["posto"]) && isset($_GET["setor"]) && isset($_GET["sequencial"])) {
                $es = new esocials1060();
                if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                      "A.POSTOTRABALHO" => $_GET["posto"],
                                      "A.SETOR" => $_GET["setor"],
                                      "A.SEQUENCIAL" => $_GET["sequencial"]))) {

                    $es->gerarXML($_SESSION[config::getSessao()]["unidade"],
                                  $_SESSION[config::getSessao()]["empresa_ativa"],
                                  $_GET["posto"],
                                  $_GET["setor"],
                                  $_GET["sequencial"],
                                  $_SESSION[config::getSessao()]["codigo"],
                                  $es->getItemLista(0)->getXML());
                    die;
                }
            } else {
                $retorno["codigo"] = 1;
                $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
            }

            if ($retorno["codigo"] > 0){
                echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
            }
        } else
            if (($acao == "gerar-s2210") && $parametro->getHabilitaGeracaoeSocial()) {
                $retorno = array("codigo" => 0, "mensagem" => "");

                if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
                    $es = new esocials2210();
                    if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                          "A.MATRICULA" => $_GET["matricula"],
                                          "A.SEQUENCIAL" => $_GET["sequencial"]))) {

                    $es->gerarXML($_SESSION[config::getSessao()]["unidade"],
                                  $_SESSION[config::getSessao()]["empresa_ativa"],
                                  $_GET["matricula"],
                                  $_GET["sequencial"],
                                  $_SESSION[config::getSessao()]["codigo"],
                                  $es->getItemLista(0)->getXML());
                        die;
                    }
                } else {
                    $retorno["codigo"] = 1;
                    $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                }

                if ($retorno["codigo"] > 0){
                    echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
                }
            } else
                if (($acao == "gerar-s2220") && $parametro->getHabilitaGeracaoeSocial()) {
                    $retorno = array("codigo" => 0, "mensagem" => "");

                    if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
                        $es = new esocials2220();
                        if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                              "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                              "A.MATRICULA" => $_GET["matricula"],
                                              "A.SEQUENCIAL" => $_GET["sequencial"]))) {

                            $es->gerarXML($_SESSION[config::getSessao()]["unidade"],
                                          $_SESSION[config::getSessao()]["empresa_ativa"],
                                          $_GET["matricula"],
                                          $_GET["sequencial"],
                                          $_SESSION[config::getSessao()]["codigo"],
                                          $es->getItemLista(0)->getXML());
                            die;
                        }
                    } else {
                        $retorno["codigo"] = 1;
                        $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                    }

                    if ($retorno["codigo"] > 0){
                        echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
                    }
                } else
                    if (($acao == "gerar-s2221") && $parametro->getHabilitaGeracaoeSocial()) {
                        $retorno = array("codigo" => 0, "mensagem" => "");

                        if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
                            $es = new esocials2221();
                            if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                  "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                  "A.MATRICULA" => $_GET["matricula"],
                                                  "A.SEQUENCIAL" => $_GET["sequencial"]))) {

                                $es->gerarXML($_SESSION[config::getSessao()]["unidade"],
                                              $_SESSION[config::getSessao()]["empresa_ativa"],
                                              $_GET["matricula"],
                                              $_GET["sequencial"],
                                              $_SESSION[config::getSessao()]["codigo"],
                                              $es->getItemLista(0)->getXML());
                                die;
                            }
                        } else {
                            $retorno["codigo"] = 1;
                            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                        }

                        if ($retorno["codigo"] > 0){
                            echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
                        }
                    } else
                        if (($acao == "gerar-s2240") && $parametro->getHabilitaGeracaoeSocial()) {
                            $retorno = array("codigo" => 0, "mensagem" => "");

                            if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
                                $es = new esocials2240();
                                if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                      "A.MATRICULA" => $_GET["matricula"],
                                                      "A.SEQUENCIAL" => $_GET["sequencial"]))) {

                                    $es->gerarXML($_SESSION[config::getSessao()]["unidade"],
                                                  $_SESSION[config::getSessao()]["empresa_ativa"],
                                                  $_GET["matricula"],
                                                  $_GET["sequencial"],
                                                  $_SESSION[config::getSessao()]["codigo"],
                                                  $es->getItemLista(0)->getXML());
                                    die;
                                }
                            } else {
                                $retorno["codigo"] = 1;
                                $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                            }

                            if ($retorno["codigo"] > 0){
                                echo "<script>parent.$.gritter.add({ title: 'Ops!', text: '{$retorno["mensagem"]} #{$retorno["codigo"]}', class_name: 'danger' });</script>";
                            }
                        }  else
                            if (($acao == "gerar-s2245") && $parametro->getHabilitaGeracaoeSocial()) {
                                            $retorno = array("codigo" => 0, "mensagem" => "");

                                            if (isset($_GET["matricula"]) && isset($_GET["sequencial"])) {
                                                $es = new esocials2245();
                                                if ($es->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                      "A.MATRICULA" => $_GET["matricula"],
                                                                      "A.SEQUENCIAL" => $_GET["sequencial"]))) {

                                                    $es->gerarXML($_SESSION[config::getSessao()]["unidade"],
                                                                  $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                  $_GET["matricula"],
                                                                  $_GET["sequencial"],
                                                                  $_SESSION[config::getSessao()]["codigo"],
                                                                  $es->getItemLista(0)->getXML());
                                                    die;
                                                }
                                            } else {
                                                $retorno["codigo"] = 1;
                                                $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                                            }

                                            if ($retorno["codigo"] > 0){
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
<div class="cl-mcont">
    <div class="block-flat">
        Certificado digital valido até <strong><?php echo funcoes::converterData($empresa->getValidadeCertificadoDigital()) ?></strong>
        <hr/>
        Selecione o arquivo: <input type="file" name="arquivo" id="arquivoCertificadoDigital" style="display: initial;">&nbsp;&nbsp;&nbsp;&nbsp;
        Informe a senha: <input type="password" name="senha" id="senhaCertificadoDigital">
        <button type="button" class="btn btn-primary" onclick="atualizarCertificadoDigital()">Atualizar</button>
        <hr/>
        <?php if (!empty($empresa->getSenhaCertificadoDigital())): ?>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#e-social-s1060" data-toggle="tab">S-1060 - Ambientes de trabalho</a></li>
            <li><a href="#e-social-s2210" data-toggle="tab">S-2210 - Comunicação de acidente de trabalho</a></li>
            <li><a href="#e-social-s2220" data-toggle="tab">S-2220 - Monitoramento da saúde</a></li>
            <li><a href="#e-social-s2221" data-toggle="tab">S-2221 - Exame toxicológico</a></li>
            <li><a href="#e-social-s2240" data-toggle="tab">S-2240 - Condições ambientais</a></li>
            <li><a href="#e-social-s2245" data-toggle="tab">S-2245 - Treinamentos e capacitações</a></li>
        </ul>
        <div class="tab-content" style="margin-bottom: 10px;">
            <div id="e-social-s1060" class="tab-pane active cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s1060"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div id="e-social-s2210" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2210"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div id="e-social-s2220" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2220"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div id="e-social-s2221" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2221"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div id="e-social-s2240" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2240"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div id="e-social-s2245" class="tab-pane cont">
                <div class="form-group-sm" style="margin-left: 10px;">
                    <div class="col-sm-12 col-md-12 col-lg-12" id="div-e-social-s2245"></div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<div id="div-e-social-download" style="display: none"></div>
<script>
    var paginaAtual = 1;
    var contDivESocial = 0;
    
    $(document).ready(function(){
        $('.icheck').iCheck({
            radioClass: 'iradio_square-blue'
        });
        
        pesquisarESocial('1', 's1060');
        pesquisarESocial('1', 's2210');
        pesquisarESocial('1', 's2220');
        pesquisarESocial('1', 's2221');
        pesquisarESocial('1', 's2240');
        pesquisarESocial('1', 's2245');
    });
    
    function atualizarCertificadoDigital() {
        if ($('#arquivoCertificadoDigital').val() === '') {
            $.gritter.add({ title: 'Ops!', text: 'Selecione o arquivo!', class_name: 'danger' });
        } else
        if ($('#senhaCertificadoDigital').val() === '') {
            $.gritter.add({ title: 'Ops!', text: 'Informe a senha do certificado digital!', class_name: 'danger' });
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
                success: function(data){
                    if (data.codigo == '0') {
                        $.gritter.add({ title: 'Oba!', text: 'Certificado atualizado!', class_name: 'success' });
                        $('#arquivoCertificadoDigital').val('');
                        $('#senhaCertificadoDigital').val('');
                    } else {
                        $.gritter.add({ title: 'Ops!', text: data.mensagem + ' #' + data.codigo, class_name: 'danger' });
                    }
                },
                error: function() {
                    $.gritter.add({ title: 'Ops!', text: 'Não foi possível enviar o arquivo!', class_name: 'danger' });
                }
            });
        }
    }

    $('#form-button-pesquisar-s1060').on('click', function(){
        pesquisarESocial('1', 's1060');
    });

    $('#form-button-pesquisar-s2210').on('click', function(){
        pesquisarESocial('1', 's2210');
    });

    $('#form-button-pesquisar-s2220').on('click', function(){
        pesquisarESocial('1', 's2220');
    });

    $('#form-button-pesquisar-s2221').on('click', function(){
        pesquisarESocial('1', 's2221');
    });
    
    $('#form-button-pesquisar-s2240').on('click', function(){
        pesquisarESocial('1', 's2240');
    });
    
    $('#form-button-pesquisar-s2245').on('click', function(){
        pesquisarESocial('1', 's2245');
    });

    function baixarTodosAbertos(origem){
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
            
            $('<div/>', {id: nomeDiv}).appendTo($('body'));
            
            $('#' + nomeDiv).html('<iframe style="display:none" src="' + url + '"></iframe>');
            $(obj).remove();
           
            if (habilitaDownload){
                setTimeout(function(){
                    baixarTodosAbertos(origem);
                }, 100);
            }
        } else {
            setTimeout(function(){
                pesquisarESocial('1', origem);
            }, 100);
        }
    }
    
    function pesquisarESocial(pagina, origem){
        paginaAtual = pagina;
    
        $('#div-e-social-' + origem).html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-e-social-' + origem).load('esocial.php', { 'a': 'pesquisar-e-social-' + origem,
                                                            'pagina': pagina
                                                          }, function(){
                                                                $('#div-e-social-' + origem + ' a').each(function(){
                                                                    if (origem == 's1060') {
                                                                        $(this).on('click', function(){
                                                                            var url = 'esocial.php?a=gerar-' + origem +
                                                                                      '&posto=' + $(this).attr('posto') +
                                                                                      '&setor=' + $(this).attr('setor') +
                                                                                      '&sequencial=' + $(this).attr('sequencial');

                                                                            $('#div-e-social-download').html('<iframe id="iframe-e-social" style="display:none" src="' + url + '"></iframe>');
                                                                            
                                                                            setTimeout(function(){
                                                                                pesquisarESocial(paginaAtual, origem);
                                                                            }, 300);
                                                                        });
                                                                    } else {
                                                                        $(this).on('click', function(){
                                                                            var url = 'esocial.php?a=gerar-' + origem +
                                                                                      '&matricula=' + $(this).attr('matricula') +
                                                                                      '&sequencial=' + $(this).attr('sequencial');

                                                                            $('#div-e-social-download').html('<iframe id="iframe-e-social" style="display:none" src="' + url + '"></iframe>');
                                                                            
                                                                            setTimeout(function(){
                                                                                pesquisarESocial(paginaAtual, origem);
                                                                            }, 300);
                                                                        });
                                                                   }
                                                                });
                                                          });
    }

</script>