<?php
require_once("class/config.inc.php");
require_once("class/funcionario.class.php");
require_once("class/postotrabalho.class.php");
require_once("class/setor.class.php");
require_once("class/setorfuncao.class.php");
require_once("class/registro-cat.class.php");
require_once("class/parteatingida.class.php");
require_once("class/funcoes.class.php");
require_once("class/esocials2210.class.php");
require_once("class/cep.class.php");


config::verificaLogin();

$empresa = new empresa();
$empresa->buscar(array(
    "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
    "A.CODIGO" => $_SESSION[config::getSessao()]["empresa_ativa"]
));
$empresa = $empresa->getItemLista(0);


if (isset($_POST["a"])) {
    $acao = $_POST["a"];
    if ($acao == "pesquisa-cep") {
        $filtro = null;

        if (isset($_POST["cep"]))
            $cep = $_POST["cep"];
        if ($cep != "") {
            $filtro["A.CODIGO"] = array($cep, "CONTAINING");
        }

        $cep = new cep();
        if ($cep->buscar($filtro, 1)) {
            $retorno["cep"] = $cep->getItemLista(0)->getCEP();
            $retorno["endereco"] = $cep->getItemLista(0)->getEndereco();
            $retorno["bairro"] = $cep->getItemLista(0)->getBairro();
            $retorno["cidade"] = $cep->getItemLista(0)->getCidade();
            $retorno["uf"] = $cep->getItemLista(0)->getUF();
            echo json_encode($retorno);
        } else {
            $retorno["cep"] = '';
        }
    }

    if ($acao == "gravar") {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (!isset($_POST["cat-input-funcionario-codigo"])) {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Funcionário não encontrado!";
        } else
        if (!isset($_POST["cat-input-funcionario-nome"])) {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Funcionário não encontrado!";
        } else
        if (!isset($_POST["cat-input-data-emissao"])) {
            $retorno["codigo"] = 3;
            $retorno["mensagem"] = "Data de emissão incorreta!";
        } else
            //     if (!isset($_POST["cat-input-tipo-acidente"])) {
            //     $retorno["codigo"] = 3;
            //     $retorno["mensagem"] = "Tipo de acidente incorreto!";
            // } else
            if (!isset($_POST["cat-input-data-acidente"])) {
                $retorno["codigo"] = 4;
                $retorno["mensagem"] = "Data do acidente incorreta!";
            } else
            if (!isset($_POST["cat-input-hora-acidente"])) {
                $retorno["codigo"] = 5;
                $retorno["mensagem"] = "Hora do acidente incorreta!";
            } else
            if (!isset($_POST["cat-input-horas-trabalhadas"])  && $_POST["cat-input-funcionario-tipo-acidente"] == '1') {
                $retorno["codigo"] = 6;
                $retorno["mensagem"] = "Quantidade de horas trabalhadas incorreta!";
            } else
            if (!isset($_POST["cat-input-funcionario-situacao-geradora"])) {
                $retorno["codigo"] = 31;
                $retorno["mensagem"] = "Situação geradora incorreta!";
            } else
            if (!isset($_POST["cat-input-funcionario-situacao-geradora"])) {
                $retorno["codigo"] = 32;
                $retorno["mensagem"] = "Situação geradora incorreta!";
            } else
            if (!isset($_POST["cat-input-agente-causador"])) {
                $retorno["codigo"] = 33;
                $retorno["mensagem"] = "Agente causador incorreto!";
            } else
            if (!isset($_POST["cat-input-funcionario-ultimo-dia-trabalhado"])) {
                $retorno["codigo"] = 35;
                $retorno["mensagem"] = "Último dia trabalhado incorreto!";
            } else
            if (!isset($_POST["cat-input-funcionario-descricao-acidente"])) {
                $retorno["codigo"] = 36;
                $retorno["mensagem"] = "Descrição do acidente incorreta!";
            } else
            if (!isset($_POST["cat-input-funcionario-acidente-cep"])) {
                $retorno["codigo"] = 37;
                $retorno["mensagem"] = "CEP do local do acidente incorreto!";
            } else
            if (!isset($_POST["cat-input-funcionario-acidente-endereco"])) {
                $retorno["codigo"] = 38;
                $retorno["mensagem"] = "Endereço do local do acidente incorreto!";
            } else
            if ((!isset($_POST["cat-input-funcionario-acidente-numero"])) or
                (preg_replace('/[^0-9]/', '', $_POST["cat-input-funcionario-acidente-numero"]) == '')
            ) {
                $retorno["codigo"] = 39;
                $retorno["mensagem"] = "Número do local do acidente incorreto!";
            } else
            if (!isset($_POST["cat-input-funcionario-acidente-bairro"])) {
                $retorno["codigo"] = 40;
                $retorno["mensagem"] = "Bairro do local do acidente incorreto!";
            } else
            if (!isset($_POST["cat-input-funcionario-acidente-codigo-ibge"])) {
                $retorno["codigo"] = 40;
                $retorno["mensagem"] = "Cidade IBGE do local do acidente incorreto!";
            } else
            if (!isset($_POST["cat-input-funcionario-acidente-cidade"])) {
                $retorno["codigo"] = 41;
                $retorno["mensagem"] = "Cidade do local do acidente incorreto!";
            } else
            if (!isset($_POST["cat-input-funcionario-acidente-cnpj"])) {
                $retorno["codigo"] = 42;
                $retorno["mensagem"] = "CNPJ do local do acidente não é válido!";
            } else
            if (!isset($_POST["form-input-codigo-parte-corpo"])) {
                $retorno["codigo"] = 42;
                $retorno["mensagem"] = "Parte do corpo atingida não é válida!";
            } else
            if (!isset($_POST["form-input-codigo-lateralidade"])) {
                $retorno["codigo"] = 43;
                $retorno["mensagem"] = "Lateralidade não é válida!";
            } else
            if (!isset($_POST["cat-input-agente-causador"])) {
                $retorno["codigo"] = 44;
                $retorno["mensagem"] = "Agente Causador não é válido!";
            } else
            if (!isset($_POST["cat-input-atestado-data"])) {
                $retorno["codigo"] = 45;
                $retorno["mensagem"] = "Data do atestado inválida!";
            } else
            if (!isset($_POST["cat-input-atestado-hora"])) {
                $retorno["codigo"] = 46;
                $retorno["mensagem"] = "Hora do atestado inválida!";
            } else
            if (!isset($_POST["cat-input-atestado-dias-internamento"])) {
                $retorno["codigo"] = 47;
                $retorno["mensagem"] = "Quantidade de dias do atestado inválida!";
            } else
            if (!isset($_POST["cat-input-atestado-descricao-natureza-lesao"])) {
                $retorno["codigo"] = 48;
                $retorno["mensagem"] = "Natureza da Lesão inválida!";
            } else
            if (!isset($_POST["cat-input-atestado-diagnostico"])) {
                $retorno["codigo"] = 49;
                $retorno["mensagem"] = "Diagnóstico inválido!";
            } else
            if (!isset($_POST["cat-input-atestado-cid-10"]) or strlen($_POST["cat-input-atestado-cid-10"]) < 3) {
                $retorno["codigo"] = 50;
                $retorno["mensagem"] = "CID do Atestado inválido!";
            } else
            if (!isset($_POST["cat-input-nome-emitente"]) or strlen($_POST["cat-input-nome-emitente"]) < 3) {
                $retorno["codigo"] = 51;
                $retorno["mensagem"] = "Nome do Emitente do Atestado inválido!";
            } else
            if (!isset($_POST["cat-input-atestado-numero-inscricao"])) {
                $retorno["codigo"] = 51;
                $retorno["mensagem"] = "Número de Registro do Emitente do Atestado inválido!";
            };





        if ($retorno["codigo"] == 0) {

            $val = array(
                "UNIDADE"        => $_SESSION[config::getSessao()]["unidade"],
                "EMPRESA"        => $_SESSION[config::getSessao()]["empresa_ativa"],
                "FUNCIONARIO"    => $_POST["cat-input-funcionario-codigo"],
                "LOCALEMISSAO"   => utf8_decode($_POST["cat-input-local-emissao"]),
                "EMITENTE"       => 1, //$_POST[""],
                "TIPOCAT"        => $_POST["cat-input-tipo-cat"],
                "UNIDADETRABALHO" => '000000',
                "NOMEFUNCIONARIO" => utf8_decode($_POST["cat-input-funcionario-nome"]),
                "TIPOACIDENTE"   => $_POST["cat-input-funcionario-tipo-acidente"],
                "HOUVEAFASTAMENTO" => $_POST["cat-input-atestado-select-houve-afastamento"],
                "LOCALACIDENTE"  => utf8_decode($_POST["cat-input-funcionario-local-acidente"]),
                "LOCALESPESIFICA" => substr(utf8_decode($_POST["cat-input-funcionario-descricao-acidente"]), 0, 30),
                "LOCALCNPJ"       => $_POST["cat-input-funcionario-acidente-cnpj"],
                "LOCALCIDADE"     => utf8_decode($_POST["cat-input-funcionario-acidente-cidade"]),
                "LOCALUF"         => $_POST["cat-input-funcionario-acidente-select-uf"],
                "REGISTROPOLICIAL" => $_POST["cat-input-registro-policial"],
                "HOUVEMORTE"       => $_POST["cat-input-select-obito"],
                "SITUACAOGERADORA_CAT" =>  $_POST["cat-input-funcionario-situacao-geradora"],
                "OBSERVACAO" =>  utf8_decode($_POST["cat-input-observacao"]),
                "CODIGOIBGE_CIDADE"    =>  $_POST["cat-input-funcionario-acidente-codigo-ibge"],
                "CODIGOAGENTECAUSADOR" =>  $_POST["cat-input-agente-causador"],
                "CODIGONATUREZALESAO"  =>  $_POST["cat-input-atestado-natureza-lesao"],
                "CODIGOCID10"          =>   $_POST["cat-input-atestado-cid-10"],
                "INICIATIVACAT"        =>  $_POST["cat-input-funcionario-iniciativa-cat"],
                "DESCRICAOLOGRADOURO"  =>  utf8_decode($_POST["cat-input-funcionario-acidente-endereco"]),
                "NUMEROLOGRADOURO"     =>  preg_replace('/[^0-9]/', '', $_POST["cat-input-funcionario-acidente-numero"]),
                "CEPLOCALACIDENTE"     =>  $_POST["cat-input-funcionario-acidente-cep"],
                "ATESTADO_INTERNACAO"  =>  $_POST["cat-input-atestado-select-internamento"],
                "ATESTADO_DIAGNOSTICO" =>  utf8_decode($_POST["cat-input-atestado-diagnostico"]),
                "ATESTADO_NOMEEMITENTE" =>  utf8_decode($_POST["cat-input-nome-emitente"]),
                "ATESTADO_ORGAOCLASSE" =>  $_POST["cat-input-atestado-conselho-classe"],
                "ATESTADO_NUMEROINSCRICAO" =>  $_POST["cat-input-atestado-numero-inscricao"],
                "ATESTADO_UFORGAOCLASSE" =>  $_POST["cat-input-atestado-medico-select-uf"],
                "LOCALBAIRRO" => utf8_decode($_POST["cat-input-funcionario-acidente-bairro"]),
                "CODIGOPARTEATINGIDA" =>  $_POST["form-input-codigo-parte-corpo"],
                "CODIGOLATERALIDADE" =>  $_POST["form-input-codigo-lateralidade"],
                "DESCRICAOCOMPLEMENTARLESAO" =>  $_POST["cat-input-atestado-descricao-natureza-lesao"],
            );

            if ($_POST["cat-input-atestado-observacao"] != '')
                $val["ATESTADO_OBSERVACAO"] = utf8_decode($_POST["cat-input-atestado-observacao"]);
            if ($_POST["cat-input-atestado-dias-internamento"] != '')
                $val["ATESTADO_DURACAODIAS"] = preg_replace('/[^0-9]/', '', $_POST["cat-input-atestado-dias-internamento"]);
            if ($_POST["cat-input-hora-acidente"] != '')
                $val["HORAACIDENTE"] = $_POST["cat-input-hora-acidente"];
            if ($_POST["cat-input-horas-trabalhadas"] != '')
                $val["QTDHORASTRABALHO"] = $_POST["cat-input-horas-trabalhadas"];
            if ($_POST["cat-input-atestado-hora"] != '')
                $val["ATESTADO_HORA"] = $_POST["cat-input-atestado-hora"];

            if ($_POST["cat-input-data-emissao"] != '')
                $val["DATAEMISSAO"] = funcoes::converterData($_POST["cat-input-data-emissao"]);


            if (isset($_POST["cat-input-data-comum-obito"]))
                $val["DATACOMUNOBITO"] = funcoes::converterData($_POST["cat-input-data-comum-obito"]);
            if (isset($_POST["cat-input-data-protocolo"]))
                $val["DATAREGISTRO"] = funcoes::converterData($_POST["cat-input-data-protocolo"]);
            if (isset($_POST["cat-input-funcionario-data-nascimento"]))
                $val["DATANASCIMENTO"] = funcoes::converterData($_POST["cat-input-funcionario-data-nascimento"]);
            if ($_POST["cat-input-data-acidente"] != '')
                $val["DATAACIDENTE"] = funcoes::converterData($_POST["cat-input-data-acidente"]);
            if ($_POST["cat-input-funcionario-ultimo-dia-trabalhado"] != '')
                $val["ULTIMODIATRAB"] = funcoes::converterData($_POST["cat-input-funcionario-ultimo-dia-trabalhado"]);
            if ($_POST["cat-input-atestado-data"] != '')
                $val["ATESTADO_DATA"] = funcoes::converterData($_POST["cat-input-atestado-data"]);

            $matricula = $_POST["cat-input-funcionario-codigo"];
            $cat = new registrocat();


            if ($cat->inserir($val)) {
                // if ($_POST["partes-atingidas"] != '') {
                //     $partes = json_decode($_POST["partes-atingidas"])->registros;
                //     foreach ($partes as $item) {
                //         $reg["UNIDADE"] = $_SESSION[config::getSessao()]["unidade"];
                //         $reg["EMPRESA"] = $_SESSION[config::getSessao()]["empresa_ativa"];
                //         $reg["NUMERO"] = $cat->getNumero();
                //         $reg["CODIGOPARTEATINGIDA"] = $item->parte;
                //         $reg["LATERALIDADE"] = $item->lateralidade;

                //         $cat->inserirParteAtingida($reg);
                //     }
                // }
                // $reg = [];
                // if ($_POST["agentes-causadores"] != '') {
                //     $partes = json_decode($_POST["agentes-causadores"])->registros;
                //     foreach ($partes as $item) {
                //         $reg["UNIDADE"] = $_SESSION[config::getSessao()]["unidade"];
                //         $reg["EMPRESA"] = $_SESSION[config::getSessao()]["empresa_ativa"];
                //         $reg["NUMERO"] = $cat->getNumero();
                //         $reg["CODIGOAGENTE"] = $item->agente;

                //         $cat->inserirAgenteCausador($reg);
                //     }
                // }

                if ($cat->existeCAT($cat->getNumero())) {
                    $xml = $cat->montarXML(
                        $_SESSION[config::getSessao()]["unidade"],
                        $_SESSION[config::getSessao()]["empresa_ativa"],
                        $cat->getNumero()
                    );

                    if (substr($xml->toXml(), 0, 14) == '<eSocial xmlns') {
                        $retorno["codigo"] = 0;
                        $retorno["mensagem"] = "Gravado com sucesso!";
                        $retorno["CAT"] = $cat->getNumero();
                        $retorno["XML"] = $xml->toXml();
                        $regESocial["UNIDADE"] = $_SESSION[config::getSessao()]["unidade"];
                        $regESocial["EMPRESA"] = $_SESSION[config::getSessao()]["empresa_ativa"];
                        $regESocial["MATRICULA"] = $matricula;
                        $regESocial["XML"] = ($xml->toXml());
                        $regESocial["SITUACAO"] = "A";
                        $regESocial["CHAVE"] = $xml->evtid;

                        $evt2210 = new esocials2210();
                        $evt2210->inserir($regESocial);
                    } else {
                        
                        $retorno["codigo"] = 99;
                        $retorno["mensagem"] = $xml;
                    }
                }
            } else {
                $retorno["codigo"] = 1;
                $retorno["mensagem"] = "Não foi possível cadastrar a CAT!";
            }
        }
        echo json_encode($retorno);
        die;
    }
    die;
}
?>

<style>
    .oculto {
        visibility: hidden;
        display: none;
    }
</style>


<div class="page-head">
    <h2>Registro CAT</h2>
</div>

<div class="form-group-sm" style="padding: 12px; " id="div-cadastro-cat" autocomplete="no">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#cat-identificacao" data-toggle="tab">Identificação</a></li>
        <li><a href="#cat-acidentado-doenca" data-toggle="tab">Acidentado ou Doença</a></li>
        <li><a href="#cat-partes-agentes" data-toggle="tab">Partes Atingidas/Agentes</a></li>
        <li><a href="#cat-atestado" data-toggle="tab">Atestado</a></li>
    </ul>
    <div class="tab-content" style="margin-bottom: 10px;">
        <div id="cat-identificacao" class="tab-pane active cont">
            <div class="form-group-sm">
                <div class="col-sm-12" style="height: 58px;">
                    <label>Funcionário:</label>
                    <div class="input-group">
                        <input type="hidden" id="cat-input-funcionario-codigo">
                        <input type="text" id="cat-input-funcionario-nome" readonly="" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
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
                <div class="col-sm-3" style="height: 58px;">
                    <label>Data Emissão:</label>
                    <div class="input-group">
                        <input type="text" data-mask="date" autocomplete="no" placeholder="DD/MM/YYYY" id="cat-input-data-emissao" class="form-control datetime" style="background-color: #fff; border-radius: 3px;">
                    </div>
                </div>
                <div class="col-sm-3" style="height: 58px;">
                    <label>Data Acidente:</label>
                    <div class="input-group">
                        <input type="text" data-mask="date" autocomplete="no" placeholder="DD/MM/YYYY" id="cat-input-data-acidente" class="form-control datetime" style="background-color: #fff; border-radius: 3px;">
                    </div>
                </div>
                <div class="col-sm-3" style="height: 58px;">
                    <label>Hora Acidente:</label>
                    <div class="input-group">
                        <input type="text" autocomplete="no" data-mask="time-short" placeholder="HH:MM" id="cat-input-hora-acidente" class="form-control datetime" style="background-color: #fff; border-radius: 3px;">
                    </div>
                </div>
                <div class="col-sm-3" style="height: 58px;">
                    <label>Horas Trabalhadas:</label>
                    <div class="input-group">
                        <input type="text" autocomplete="no" id="cat-input-horas-trabalhadas" data-mask="time-short" placeholder="HH:MM" class="form-control datetime" style="background-color: #fff; border-radius: 3px;">
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-4">
                    <label>Tipo CAT:</label>
                    <select class="form-control input-sm" id="cat-input-tipo-cat">
                        <option value="1">Inicial</option>
                        <option value="2">Reabertura</option>
                        <option value="3">Comunicado de Óbito</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label>Óbito:</label>
                    <select class="form-control input-sm" id="cat-input-select-obito">
                        <option value="S">Sim</option>
                        <option value="N">Não</option>
                    </select>
                </div>
                <div class="col-sm-4" style="height: 58px;">
                    <label>Data Comunicado Óbito:</label>
                    <div class="input-group">
                        <input type="text" data-mask="date" autocomplete="no" placeholder="DD/MM/YYYY" id="cat-input-data-comum-obito" class="form-control datetime" style="background-color: #fff; border-radius: 3px;">
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-4">
                    <label>Registro Policial:</label>
                    <select class="form-control input-sm" id="cat-input-registro-policial">
                        <option value="S">Sim</option>
                        <option value="N">Não</option>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-12 col-md-12">
                    <label>Local Emissão:</label>
                    <input type="text" autocomplete="no" id="cat-input-local-emissao" maxlength=30 class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="form-group-sm">
                <div class="col-sm-12 col-md-12">
                    <label>Observação:</label>
                    <input type="text" autocomplete="no" id="cat-input-observacao" class="form-control input-sm">
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div id="cat-acidentado-doenca" class="tab-pane cont">
            <div class="form-group-sm">
                <div class="col-sm-8" style="height: 58px;">
                    <label>Situacao Geradora:</label>
                    <div class="input-group">
                        <input type="hidden" id="cat-input-funcionario-situacao-geradora">
                        <input type="text" readonly="" autocomplete="no" id="cat-input-funcionario-descricao-situacao-geradora" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-situacao-geradora-funcionario" style="height: 30px; padding-top: 4px">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-4" style="height: 58px;">
                    <label>Iniciativa CAT:</label>
                    <select class="form-control input-sm" id="cat-input-funcionario-iniciativa-cat">
                        <option value="1">Empregador</option>
                        <option value="2">Ordem judicial</option>
                        <option value="3">Determinação de Órgão Fiscalizador</option>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-2" style="height: 58px;">
                    <label>Tipo:</label>
                    <select class="form-control input-sm" id="cat-input-funcionario-tipo-acidente">
                        <option value="1">Típico </option>
                        <option value="2">Doença</option>
                        <option value="3">Trajeto</option>
                    </select>
                </div>
                <div class="col-sm-3" style="height: 58px;">
                    <label>Último dia Trabalhado:</label>
                    <div class="input-group">
                        <input type="text" id="cat-input-funcionario-ultimo-dia-trabalhado" placeholder="DD/MM/YYYY" class="form-control datetime" style="background-color: #fff; border-radius: 3px;">
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <hr>
            <div class="form-group-sm">
                <label>Acidente:</label>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group-sm">
                <div class="col-sm-5" style="height: 58px;">
                    <label>Local:</label>
                    <select class="form-control input-sm" id="cat-input-funcionario-local-acidente">
                        <option value="1">Estabelecimento do empregador no Brasil; </option>
                        <option value="2">Estabelecimento do empregador no Exterior; </option>
                        <option value="3">Estabelecimento de terceiros onde o empregador presta serviços; </option>
                        <option value="4">Via pública; </option>
                        <option value="5">Área rural;</option>
                        <option value="6">Embarcação; </option>
                        <option value="9">Outros.</option>
                    </select>
                </div>
                <div class="col-sm-7" style="height: 58px;">
                    <label>Descrição:</label>
                    <input type="text" id="cat-input-funcionario-descricao-acidente" autocomplete="no" maxlength=30 class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-2" style="height: 58px;">
                    <label>CNPJ:</label>
                    <input type="text" id="cat-input-funcionario-acidente-cnpj" autocomplete="no" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;" maxlength="18">
                </div>

                <div class="col-sm-2" style="height: 58px;">
                    <label>CEP:</label>
                    <div class="input-group">
                        <input type="text" id="cat-input-funcionario-acidente-cep" autocomplete="no" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-acidente-cep-funcionario" style="height: 30px; padding-top: 4px">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>

                <div class="col-sm-7">
                    <label>Endereço:</label>
                    <input type="text" class="form-control input-sm" id="cat-input-funcionario-acidente-endereco" autocomplete="no">
                </div>
                <div class="col-sm-1">
                    <label>Número:</label>
                    <input type="text" class="form-control input-sm" id="cat-input-funcionario-acidente-numero" autocomplete="no">
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="form-group-sm">
                <div class="col-sm-4">
                    <label>Bairro:</label>
                    <input type="text" class="form-control input-sm" id="cat-input-funcionario-acidente-bairro" autocomplete="no" maxlength="90">
                </div>
                <div class="col-sm-2">
                    <label>Cidade:</label>
                    <div class="input-group">
                        <input type="text" id="cat-input-funcionario-acidente-codigo-ibge" autocomplete="no" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-cidade" style="height: 30px; padding-top: 4px">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label style="visibility: hidden">.</label>
                    <input type="text" class="form-control input-sm" id="cat-input-funcionario-acidente-cidade" autocomplete="no">
                </div>

                <div class="col-sm-2">
                    <label>UF:</label>
                    <select class="form-control input-sm" id="cat-input-funcionario-acidente-select-uf">
                        <option value="AC">AC</option>
                        <option value="AL">AL</option>
                        <option value="AP">AP</option>
                        <option value="AM">AM</option>
                        <option value="BA">BA</option>
                        <option value="CE">CE</option>
                        <option value="DF">DF</option>
                        <option value="ES">ES</option>
                        <option value="GO">GO</option>
                        <option value="MA">MA</option>
                        <option value="MT">MT</option>
                        <option value="MS">MS</option>
                        <option value="MG">MG</option>
                        <option value="PA">PA</option>
                        <option value="PB">PB</option>
                        <option value="PR">PR</option>
                        <option value="PE">PE</option>
                        <option value="PI">PI</option>
                        <option value="RJ">RJ</option>
                        <option value="RN">RN</option>
                        <option value="RS">RS</option>
                        <option value="RO">RO</option>
                        <option value="RR">RR</option>
                        <option value="SC">SC</option>
                        <option value="SP">SP</option>
                        <option value="SE">SE</option>
                        <option value="TO">TO</option>

                    </select>
                </div>
                <div class="clearfix"></div>


            </div>

        </div>

        <div id="cat-partes-agentes" class="tab-pane cont">
            <div class="form-group-sm">
                <div class="col-sm-12" style="height: 58px;">
                    <label>Parte do Corpo:</label>
                    <div class="input-group">
                        <input type="hidden" id="form-input-codigo-parte-corpo">
                        <input type="text" id="form-input-descricao-parte-corpo" readonly="" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-parte-corpo" style="height: 30px; padding-top: 4px">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-12" style="height: 58px;">
                    <label>Lateralidade:</label>
                    <div class="input-group">
                        <input type="hidden" id="form-input-codigo-lateralidade">
                        <input type="text" id="form-input-descricao-lateralidade" readonly="" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-lateralidade" style="height: 30px; padding-top: 4px">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="form-group-sm">
                <div class="col-sm-12" style="height: 58px;">
                    <label>Agente Causador:</label>
                    <div class="input-group">
                        <input type="hidden" id="cat-input-agente-causador">
                        <input type="text" id="cat-input-descricao-agente-causador" readonly="" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-buscar-agente-causador" style="height: 30px; padding-top: 4px">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div id="cat-atestado" class="tab-pane cont">
            <div class="form-group-sm">
                <div class="col-sm-3" style="height: 58px;">
                    <label>Data:</label>
                    <div class="input-group">
                        <input type="text" autocomplete="no" id="cat-input-atestado-data" placeholder="DD/MM/YYYY" class="form-control datetime" style="background-color: #fff; border-radius: 3px;">
                    </div>
                </div>
                <div class="col-sm-3" style="height: 58px;">
                    <label>Hora:</label>
                    <input type="text" id="cat-input-atestado-hora" autocomplete="no" class="form-control input-sm" placeholder="HH:MM" autocomplete="no" style="background-color: #fff; border-radius: 3px;">
                </div>
                <div class="col-sm-3" style="height: 58px;">
                    <label>Internamento:</label>
                    <select class="form-control input-sm" id="cat-input-atestado-select-internamento">
                        <option value="S">Sim</option>
                        <option value="N">Não</option>
                    </select>
                </div>
                <div class="col-sm-3" style="height: 58px;">
                    <label>Dias de atestado:</label>
                    <input type="text" id="cat-input-atestado-dias-internamento" class="form-control input-sm" autocomplete="no">
                </div>

                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-12" style="height: 58px;">
                    <label>Descrição da Lesão:</label>
                    <div class="input-group">
                        <input type="hidden" id="cat-input-atestado-natureza-lesao">
                        <input type="text" id="cat-input-atestado-descricao-natureza-lesao" readonly="" autocomplete="no" class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-xs" href="javascript: void(0)" id="form-button-pesquisa-natureza-lesao" style="height: 30px; padding-top: 4px">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-12">
                    <label>Observação</label>
                    <input type="text" class="form-control input-sm" id="cat-input-atestado-observacao" maxlength="250" autocomplete="no">
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-3" style="height: 58px;">
                    <label>Houve Afastamento:</label>
                    <select class="form-control input-sm" id="cat-input-atestado-select-houve-afastamento">
                        <option value="S">Sim</option>
                        <option value="N">Não</option>
                    </select>
                </div>
                <div class="col-sm-8">
                    <label>Diagnóstico</label>
                    <input type="text" class="form-control input-sm" id="cat-input-atestado-diagnostico" maxlength="100" autocomplete="no">
                </div>
                <div class="col-sm-1">
                    <label>CID-10</label>
                    <input type="text" class="form-control input-sm" id="cat-input-atestado-cid-10" autocomplete="no">
                </div>

                <div class="clearfix"></div>
            </div>

            <div class="form-group-sm">
                <div class="col-sm-5">
                    <label>Nome Médico</label>
                    <input type="text" class="form-control input-sm" id="cat-input-nome-emitente" maxlength="70" autocomplete="no">
                </div>
                <div class="col-sm-3" style="height: 58px;">
                    <label>Conselho de Classe:</label>
                    <select class="form-control input-sm" id="cat-input-atestado-conselho-classe">
                        <option value="1">Conselho Regional de Medicina (CRM)</option>
                        <option value="2">Registro do Ministério da Saúde (RMS)</option>
                        <option value="3">Conselho Regional de Odontologia (CRO)</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Número Registro</label>
                    <input type="text" class="form-control input-sm" id="cat-input-atestado-numero-inscricao" maxlength="14" autocomplete="no">
                </div>
                <div class="col-sm-2">
                    <label>UF:</label>
                    <select class="form-control input-sm" id="cat-input-atestado-medico-select-uf">
                        <option value="AC">AC</option>
                        <option value="AL">AL</option>
                        <option value="AP">AP</option>
                        <option value="AM">AM</option>
                        <option value="BA">BA</option>
                        <option value="CE">CE</option>
                        <option value="DF">DF</option>
                        <option value="ES">ES</option>
                        <option value="GO">GO</option>
                        <option value="MA">MA</option>
                        <option value="MT">MT</option>
                        <option value="MS">MS</option>
                        <option value="MG">MG</option>
                        <option value="PA">PA</option>
                        <option value="PB">PB</option>
                        <option value="PR">PR</option>
                        <option value="PE">PE</option>
                        <option value="PI">PI</option>
                        <option value="RJ">RJ</option>
                        <option value="RN">RN</option>
                        <option value="RS">RS</option>
                        <option value="RO">RO</option>
                        <option value="RR">RR</option>
                        <option value="SC">SC</option>
                        <option value="SP">SP</option>
                        <option value="SE">SE</option>
                        <option value="TO">TO</option>

                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- <label>* Campos obrigatórios</label> -->
    <div class="form-group-sm">
        <div class="col-sm-12" style="text-align: right">
            <button type="submit" class="btn btn-primary" id="cadastro-button-gravar">Gravar</button>
            <button type="button" class="btn btn-danger" id="cadastro-button-cancelar">Cancelar</button>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<!-- <?php if (empty($empresa->getSenhaCertificadoDigital())) : ?>
    <div class="page-head">
        <h2>Informe o certificado digital da empresa ou do procurador na opção e-Social</h2>
    </div>
<?php endif; ?>  -->


<script>
    var ultimoItem = 0;

    $(document).ready(function() {
        // let camposInput = document.querySelectorAll('input');
        // for (i = 0; i < camposInput.length; ++i) {
        //     var campo = camposInput[i].id; 
        //     var valor = $('#'+camposInput[i].id).val(1); 
        // }
        // $('#cat-input-data-emissao').val('19/10/2021');
        // $('#cat-input-data-acidente').val('19/10/2021');
        // $('#cat-input-data-comum-obito').val('19/10/2021');
        // $('#cat-input-data-protocolo').val('19/10/2021');
        // $('#cat-input-data-nascimento').val('19/10/2021');
        // $('#cat-input-funcionario-data-emissao-ctps').val('19/10/2021');
        // $('#cat-input-data-emissao-rg').val('19/10/2021');
        // $('#cat-input-funcionario-ultimo-dia-trabalhado').val('19/10/2021');
        // $('#cat-input-atestado-data').val('19/10/2021');
        // $('#cat-input-hora-acidente').val('12:00');
        // $('#cat-input-atestado-hora').val('12:00');
        // $('#cat-input-horas-trabalhadas').val('12:00');


        // vParte = [[]];

        $('#cat-input-data-emissao').datepicker({
            autoclose: true
        });
        $('#cat-input-data-acidente').datepicker({
            autoclose: true
        });
        $('#cat-input-data-comum-obito').datepicker({
            autoclose: true
        });
        $('#cat-input-data-protocolo').datepicker({
            autoclose: true
        });
        $('#cat-input-data-nascimento').datepicker({
            autoclose: true
        });
        $('#cat-input-funcionario-data-emissao-ctps').datepicker({
            autoclose: true
        });
        $('#cat-input-data-emissao-rg').datepicker({
            autoclose: true
        });
        $('#cat-input-funcionario-ultimo-dia-trabalhado').datepicker({
            autoclose: true
        });
        $('#cat-input-atestado-data').datepicker({
            autoclose: true
        });

        $('#cat-input-data-emissao').mask('99/99/9999');
        $('#cat-input-data-acidente').mask('99/99/9999');
        $('#cat-input-data-comum-obito').mask('99/99/9999');
        $('#cat-input-data-protocolo').mask('99/99/9999');
        $('#cat-input-data-nascimento').mask('99/99/9999');
        $('#cat-input-data-nascimento').mask('99/99/9999');
        $('#cat-input-funcionario-data-emissao-ctps').mask('99/99/9999');
        $('#cat-input-data-emissao-rg').mask('99/99/9999');
        $('#cat-input-funcionario-ultimo-dia-trabalhado').mask('99/99/9999');

        $('#cat-input-hora-acidente').mask('99:99');
        $('#cat-input-atestado-hora').mask('99:99');
        $('#cat-input-horas-trabalhadas').mask('99:99');

        formatarDecimal($('#cat-input-funcionario-remuneracao'), 2);
        //formatarDecimal2($('#cat-input-funcionario-remuneracao'));

        // App.masks();
        // $('#cadastro-input-pis').mask('999.99999.99-9');
        // $('#cadastro-input-cpf').mask('999.999.999-99');
    });

    function formatarDecimal(obj, casas) {
        $(obj).unbind('blur');

        $(obj).on('blur', function() {
            if ($(this).val().trim() == '') {
                var valor = 0;
                $(this).val(valor.toFixed(casas)).trigger('change');
            }
        });

        $(obj).inputmask({
            alias: 'numeric',
            radixPoint: ',',
            autoGroup: false,
            digits: casas,
            digitsOptional: false,
            prefix: '',
            placeholder: ''
        });
    }

    function cpf(v) {
        v = v.replace(/\D/g, "") //Remove tudo o que não é dígito
        v = v.replace(/(\d{3})(\d)/, "$1.$2") //Coloca um ponto entre o terceiro e o quarto dígitos
        v = v.replace(/(\d{3})(\d)/, "$1.$2") //Coloca um ponto entre o terceiro e o quarto dígitos
        //de novo (para o segundo bloco de números)
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos
        return v
    }

    function cnpj(v) {
        v = v.replace(/\D/g, "") //Remove tudo o que não é dígito
        v = v.replace(/^(\d{2})(\d)/, "$1.$2") //Coloca ponto entre o segundo e o terceiro dígitos
        v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3") //Coloca ponto entre o quinto e o sexto dígitos
        v = v.replace(/\.(\d{3})(\d)/, ".$1/$2") //Coloca uma barra entre o oitavo e o nono dígitos
        v = v.replace(/(\d{4})(\d)/, "$1-$2") //Coloca um hífen depois do bloco de quatro dígitos
        return v
    }


    // $('#cat-input-funcionario-remuneracao').keypress(function(event){
    //     console.log($('#cat-input-funcionario-remuneracao').val());
    //     $('#cat-input-funcionario-remuneracao').val(number_format( $('#cat-input-funcionario-remuneracao').val(), 2, ',', '.' ));
    // });

    $('#cat-funcionario-input-acidente-cnpj').keypress(function(event) {
        $('#cat-funcionario-input-acidente-cnpj').val(cnpj($('#cat-funcionario-input-acidente-cnpj').val()));
    });

    $('#form-button-pesquisa-funcionario').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa funcionário');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('funcionario-pesquisa.php', {
            'prefixo': 'cat-input-funcionario'
        });
    });

    function Excluir(button, id) {
        if (confirm('Deseja excluir ?')) {
            $('#' + id).remove();
            $(button).parent().parent().remove();
        }
    }

    $('#form-button-pesquisa-tipo-acidente').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa tipo de acidente');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('tipo-acidente-pesquisa.php', {
            'prefixo': 'cat-input'
        });
    });

    $('#form-button-pesquisa-cep-funcionario').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa CEP');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('cep-pesquisa.php', {
            'prefixo': 'cat-input-funcionario'
        });
    });

    $('#form-button-pesquisa-acidente-cep-funcionario').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa CEP');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('cep-pesquisa.php', {
            'prefixo': 'cat-input-funcionario-acidente'
        });
    });

    $('#form-button-pesquisa-cep-testemunha1').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa CEP');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('cep-pesquisa.php', {
            'prefixo': 'cat-input-testemunha1'
        });
    });


    $('#form-button-buscar-agente-causador').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa parte do corpo');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('agente-causador-pesquisa.php', {
            'prefixo': 'cat-input',
            'div': 'div-modal'
        });
    });

    $('#form-button-pesquisa-parte-corpo').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa Parte do Corpo');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('parte-corpo-pesquisa.php', {
            'prefixo': 'form-input'
        });
    });

    $('#form-button-pesquisa-lateralidade').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa Lateralidade');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('lateralidade-pesquisa.php', {
            'prefixo': 'form-input'
        });
    });


    $('#form-button-adicionar-parte-corpo').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Adicionar parte do corpo');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('inserir-parte-corpo.php', {
            'prefixo': 'cat-input'
        });
    });

    $('#form-button-adicionar-agente-causador').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Adicionar Agente Causador ');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('inserir-agente-causador.php', {
            'prefixo': 'inserir-agente-causador',
            'div': 'div-modal-1'
        });
    });

    $('#form-button-pesquisa-cep-testemunha2').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa CEP');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('cep-pesquisa.php', {
            'prefixo': 'cat-input-testemunha2'
        });
    });

    $('#form-button-pesquisa-cidade').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa Município');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('cidade-pesquisa.php', {
            'prefixo': 'cat-input-funcionario-acidente'
        });
    });

    $('#form-button-pesquisa-cbo-funcionario').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa CBO');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('cbo-pesquisa.php', {
            'prefixo': 'cat-input-funcionario'
        });
    });

    $('#form-button-pesquisa-situacao-geradora-funcionario').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa Situacao Geradora');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('situacao-geradora-pesquisa.php', {
            'prefixo': 'cat-input-funcionario'
        });
    });

    $('#form-button-pesquisa-agente-causador').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa Agente Causador');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('agente-causador-pesquisa.php', {
            'prefixo': 'form-input'
        });
    });

    $('#form-button-pesquisa-natureza-lesao').on('click', function() {
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa Natureza Lesão');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('natureza-lesao-pesquisa.php', {
            'prefixo': 'cat-input-atestado'
        });
    });


    function sleep(milliseconds) {
        const date = Date.now();
        let currentDate = null;
        do {
            currentDate = Date.now();
        } while (currentDate - date < milliseconds);
    }

    $('#cadastro-button-gravar').on('click', function() {
        $('#cadastro-button-gravar').addClass('disabled');
        $('#cadastro-button-gravar').text('Aguarde...');


        let camposInput = document.querySelectorAll('input');
        camposPost = 'a=gravar&';

        for (i = 0; i < camposInput.length; ++i) {
            var campo = camposInput[i].id;
            var valor = $('#' + camposInput[i].id).val();
            if (valor != '') {
                camposPost = camposPost + campo + '=' + valor + '&';
            }
        }

        let camposSelect = document.querySelectorAll('select');
        for (i = 0; i < camposSelect.length; ++i) {
            var campo = camposSelect[i].id;
            var valor = $('#' + camposSelect[i].id).find('option:selected').val();
            if (valor != '') {
                camposPost = camposPost + campo + '=' + valor + '&';
            }
        }

        var partesAtingidas = [];
        var lista = $('#table-parte-corpo tbody');
        $(lista).find("tr").each(function(index, tr) {
            partesAtingidas.push([$(tr).find('#codigo-parte-corpo').html(),
                $(tr).find('#codigo-lateralidade').html()
            ])
        });

        var agentesCausadores = [];
        var lista = $('#table-agente-causador tbody');
        $(lista).find("tr").each(function(index, tr) {
            agentesCausadores.push([$(tr).find('#codigo-agente-causador').html()])
        });

        camposPost = camposPost.substring(0, camposPost.length - 1);

        if (partesAtingidas.length > 0) {
            camposPost = camposPost + '&partes-atingidas={\"registros\":[';
            for (var cont = 0; cont in partesAtingidas; cont++)
                camposPost = camposPost + '{\"parte\":' + partesAtingidas[cont][0] + ', \"lateralidade\":' + partesAtingidas[cont][1] + ' },';

            camposPost = camposPost.substring(0, camposPost.length - 1); // Apagar a última ','
            camposPost = camposPost + ']}';
        }

        if (agentesCausadores.length > 0) {
            camposPost = camposPost + '&agentes-causadores={\"registros\":[';
            for (var cont = 0; cont in agentesCausadores; cont++)
                camposPost = camposPost + ' {\"agente\":' + agentesCausadores[cont] + '},';
            camposPost = camposPost.substring(0, camposPost.length - 1);
            camposPost = camposPost + ']}';
        }


        // console.log(camposPost);

        $('#cadastro-button-gravar').removeClass('disabled');
        $('#cadastro-button-gravar').text('Gravar');
        $.ajax({
            type: 'post',
            url: 'registro-cat.php',
            data: camposPost,
            dataType: 'json',
            async: true,
            cache: false
        }).done(function(data) {
            console.log(data.responseText);
            if (data.codigo > 0) {
                $.gritter.add({
                    title: 'Ops!',
                    text: data.mensagem + ' #' + data.codigo,
                    class_name: 'danger'
                });
            } else {
                $.gritter.add({
                    title: 'Oba!',
                    text: 'CAT cadastrado com sucesso!',
                    class_name: 'success'
                });
                // sleep(5000);
                carregarPagina('dashboard', true, '');
            }
            $('#cadastro-button-gravar').removeClass('disabled');
            $('#cadastro-button-gravar').text('Gravar');

        }).error(function(data) {
            $.gritter.add({
                title: 'Ops!',
                text: 'Verifique: ' + data.responseText,
                class_name: 'danger'
            });

            console.log(data.responseText);
        });
    });

    $('#cadastro-button-cancelar').on('click', function() {
        location.reload();
        $('#div-modal-cadastro').modal('toggle');
    });

    $('#cat-input-funcionario-acidente-cep').on('blur', function() {
        $.ajax({
            type: 'post',
            url: 'registro-cat.php',
            data: 'a=pesquisa-cep&cep=' + $('#cat-input-funcionario-acidente-cep').val(),
            dataType: 'json',
            async: true,
            cache: false
        }).done(function(data) {

            if (data.cep != '') {
                $('#cat-input-funcionario-acidente-cep').val(data.cep);
                $('#cat-input-funcionario-acidente-endereco').val(data.endereco);
                $('#cat-input-funcionario-acidente-bairro').val(data.bairro);
                $('#cat-input-funcionario-acidente-cidade').val(data.cidade);
                $('#cat-input-funcionario-acidente-select-uf').val(data.uf);
            }
        });
    });
</script>