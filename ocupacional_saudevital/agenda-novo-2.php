<?php
    require_once("class/config.inc.php");
    require_once("class/agenda.class.php");
    require_once("class/unidade.class.php");
    require_once("class/empresa.class.php");
    require_once("class/exameweb.class.php");
    require_once("class/empresamedico.class.php");
    require_once("class/medico.class.php");
    require_once("class/setorfuncao.class.php");
    require_once("class/funcionario.class.php");
    require_once("class/funcoes.class.php");
    require_once("class/exameweb.class.php");
    require_once("class/empresaemail.class.php");
    require_once("class/tabela.class.php");
    require_once("class/titreceb.class.php");
    require_once("class/parametrosgerais.class.php");
     /*testessssssssssssssss*/
    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "hora"){
            $retorno = array("codigo" => 0, "mensagem" => "", "retorno" => array());

            $data = date("d/m/Y");

            if (isset($_POST["data"]) && isset($_POST["funcionario"]) && isset($_POST["tipo"]) && isset($_POST["prestador"])){

                if (funcoes::validarData($_POST["data"])){

                    if (is_numeric($_POST["funcionario"])){

                        if (in_array($_POST["tipo"], array("A", "P", "D", "M", "R"))){

                            if (is_numeric($_POST["prestador"])){
                                
                                $data = $_POST["data"];
                                $data = funcoes::converterData($data);

                                $funcionario = $_POST["funcionario"];
                                $tipo        = $_POST["tipo"];
                                $medico      = $_POST["prestador"];
                                $novosetor   = $_POST["novo-setor"];
                                $novafuncao  = $_POST["nova-funcao"];
                                
                              
                                $intervaloAgendamento = 0;
                                $limiteAgendamento = 0;
                                $em = new empresa();
                                if ($em->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                      "A.CODIGO" => $_SESSION[config::getSessao()]["empresa_ativa"]))){
                                    $limiteAgendamento = $em->getItemLista(0)->getLimiteAgendamentoWeb();
                                    $intervaloAgendamento = $em->getItemLista(0)->getIntervaloAgendamento();
                                }

                                $totalAgendado = 0;
                                $ag = new agenda();
                                if ($ag->buscar(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                      "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                      "A.DATA" => $data))){
                                    $totalAgendado = $ag->getTotalRegistros();
                                }

                                if (($limiteAgendamento == 0) || ($totalAgendado < $limiteAgendamento)){
                                    $hora = date("00:00:00");

                                    if (($data == date("Y-m-d")) && ($intervaloAgendamento > 0)){
                                        $hora = date("H:i:s", strtotime("+{$intervaloAgendamento} minutes"));
                                        // adicionar a quantidade de minutos no intervalo
                                    }else
                                        if ($data == date("Y-m-d")){
                                            $hora = date("H:i:s");
                                        }

                                    $ag->buscarLimiteHorario($funcionario, $data, $tipo, $medico,
                                                             $horarioMinManha, $horarioMaxManha,
                                                             $horarioMinTarde, $horarioMaxTarde,
                                                             $novosetor, $novafuncao);
                                                            

                                    $filtro = array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                    "A.DATA"          => $data,
                                                    "B.CODIGOMEDICO"  => $medico,
                                                    "((A.HORA BETWEEN '{$horarioMinManha}' AND '$horarioMaxManha') OR "
                                                      . "(A.HORA BETWEEN '{$horarioMinTarde}' AND '$horarioMaxTarde')) AND A.HORA" => array($hora, ">="));

                                                        $ag = new agenda();
                                    if ($ag->buscarHorariosLivres($filtro)){
                                        $temp = array();
                                        for ($i = 0; $i < $ag->getTotalLista(); $i++)
                                            array_push($temp, array("hora"       => $ag->getItemLista($i)->getHora(),
                                                                    "codigo"     => $ag->getItemLista($i)->getCodigo(),
                                                                    "medico"     => $ag->getItemLista($i)->getMedico()->getCodigo(),
                                                                    "nomemedico" => utf8_encode($ag->getItemLista($i)->getMedico()->getNome())));

                                        $retorno["retorno"] = $temp;
                                    }else{
                                        $telefone = "";
                                        $un = new unidade();
                                        if ($un->buscar(array("A.CODIGO" => $_SESSION[config::getSessao()]["unidade"]), 1))
                                            $telefone = $un->getItemLista(0)->getTelefone();

                                        $filtro = "A.CODIGOUNIDADE =" . $_SESSION[config::getSessao()]["unidade"].
                                            "A.DATA = "  .  $data.
                                            "B.CODIGOMEDICO = "  . $medico.
                                            "((A.HORA BETWEEN '{$horarioMinManha}' AND '$horarioMaxManha') OR ".
                                            "(A.HORA BETWEEN '{$horarioMinTarde}' AND '$horarioMaxTarde')) AND A.HORA >=" .$hora.
                                            ' tipo   ' . $tipo. '  novo setor '. $novosetor;

                                        $retorno["codigo"] = 8;
                                        $retorno["mensagem"] = "Nenhum horário disponível. <br>"
                                                             . "Em caso de urgência no agendamento, ou dúvidas, "
                                                             . "contate a central de atendimento".$filtro;

                                        if ($telefone != "")
                                            $retorno["mensagem"] .= ": {$telefone}<br>";
                                    }
                                }else{
                                    $retorno["codigo"] = 7;
                                    $retorno["mensagem"] = "O limite de agendamento para esse dia foi excedido!";
                                }
                            }else{
                                $retorno["codigo"] = 6;
                                $retorno["mensagem"] = "Prestador não informado!";
                            }
                        }else{
                            $retorno["codigo"] = 5;
                            $retorno["mensagem"] = "Tipo informado não é válido!";
                        }
                    }else{
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Funcionário informado não é válido!";
                    }
                }else{
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Data informada não é válida!";
                }
            }else{
                if (!isset($_POST["data"])){
                    $retorno["codigo"] = 1;
                    $retorno["mensagem"] = "Data não informada!";
                }else
                    if (!isset($_POST["funcionario"])){
                        $retorno["codigo"] = 2;
                        $retorno["mensagem"] = "Funcionário não informado!";
                    }else
                        if (!isset($_POST["tipo"])){
                            $retorno["codigo"] = 3;
                            $retorno["mensagem"] = "Tipo não informado!";
                        }
            }
            echo json_encode($retorno);
        }else
            if ($acao == "gravar"){
                $retorno = array("codigo" => 0, "mensagem" => "");

                // valida se os campos existem no parametro
                // pra evitar que os um mané envie dados de outra página
                if (!isset($_POST["codigo"]))
                    $retorno["codigo"] = 1;
                else
                    if (!isset($_POST["hora"]))
                        $retorno["codigo"] = 2;
                    else
                        if (!isset($_POST["tipo"]))
                            $retorno["codigo"] = 3;
                        else
                            if (!isset($_POST["posto"]))
                                $retorno["codigo"] = 4;
                            else
                                if (!isset($_POST["funcionario"]))
                                    $retorno["codigo"] = 5;
                                else
                                    if (!isset($_POST["novo-setor"]))
                                        $retorno["codigo"] = 6;
                                    else
                                        if (!isset($_POST["nova-funcao"]))
                                            $retorno["codigo"] = 7;

                if ($retorno["codigo"] > 0){
                    $retorno["mensagem"] = "Alguns parametros não foram encontrados!";
                    echo json_encode($retorno);
                    die;
                }

                $codigo = $_POST["codigo"];
                $hora   = $_POST["hora"];
                $tipo   = $_POST["tipo"];
                $posto  = $_POST["posto"];
                $funcionario = $_POST["funcionario"];
                $novosetor   = $_POST["novo-setor"];
                $novafuncao  = $_POST["nova-funcao"];

                // valida se os campos contém valor
                if (trim($codigo) == ""){
                    $retorno["codigo"] = 8;
                    $retorno["mensagem"] = "Alguns parâmetros da agenda não foram encontrados!";
                }else
                    if (trim($hora) == ""){
                        $retorno["codigo"] = 9;
                        $retorno["mensagem"] = "Alguns parâmetros da agenda não foram encontrados!";
                    }else
                        if (trim($tipo) == ""){
                            $retorno["codigo"] = 10;
                            $retorno["mensagem"] = "Informe o tipo do exame!";
                        }else
                            if (trim($funcionario) == ""){
                                $retorno["codigo"] = 11;
                                $retorno["mensagem"] = "Selecione o funcionário!";
                            }else
                                if (trim($posto) == ""){
                                    $retorno["codigo"] = 12;
                                    $retorno["mensagem"] = "Selecione o posto de trabalho!";
                                }else
                                    if ((trim($novosetor) == "") && ($tipo == "M")){
                                        $retorno["codigo"] = 13;
                                        $retorno["mensagem"] = "Selecione o novo setor!";
                                    }else
                                        if ((trim($novafuncao) == "") && ($tipo == "M")){
                                            $retorno["codigo"] = 14;
                                            $retorno["mensagem"] = "Selecione a nova função!";
                                        }

                if ($retorno["codigo"] > 0){
                    echo json_encode($retorno);
                    die;
                }

                $ag = new agenda();
                if ($ag->buscarDataAgenda($_SESSION[config::getSessao()]["unidade"], $codigo)){
                    $data = $ag->getData();

                    if ($ag->buscarHorariosLivres(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                        "A.CODIGO" => $codigo,
                                                        "A.HORA" => $hora))){
                        // valida se horário está livre
                        $intervaloAgendamento = 0;
                        $limiteAgendamento = 0;
                        $em = new empresa();
                        if ($em->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                              "A.CODIGO" => $_SESSION[config::getSessao()]["empresa_ativa"]))){
                            $limiteAgendamento = $em->getItemLista(0)->getLimiteAgendamentoWeb();
                            $intervaloAgendamento = $em->getItemLista(0)->getIntervaloAgendamento();
                            // busca o limite de agendamento diario e o intervalo pra agendamento
                        }

                        $totalAgendado = 0;
                        $ag = new agenda();
                        if ($ag->buscar(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                              "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                              "A.CODIGO" => $codigo))){
                            $totalAgendado = $ag->getTotalRegistros();
                            // busca o total agendado para o dia
                        }

                        if (($limiteAgendamento == 0) || ($totalAgendado < $limiteAgendamento)){
                            // valida se excedeu o limite diário de agendamento, caso tenha algum
                            $horaAgora = date("H:i:s");
                            $diferenca = (strtotime($hora) - strtotime($horaAgora)) / 60; // calcula em segundos e muda pra minutos

                            if ((($data == date("Y-m-d")) && ($diferenca >= $intervaloAgendamento)) || ($data > date("Y-m-d"))){
                                // valida o intervalo do agendamento
                                $ex = new exameweb();
                                if ($ex->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"]))){
                                    $tipoOk = false;

                                    if ($ex->getItemLista(0)->permiteAdmissional() && ($tipo == "A"))
                                        $tipoOk = true;
                                    if ($ex->getItemLista(0)->permitePeriodico() && ($tipo == "P"))
                                        $tipoOk = true;
                                    if ($ex->getItemLista(0)->permiteDemissional() && ($tipo == "D"))
                                        $tipoOk = true;
                                    if ($ex->getItemLista(0)->permiteMudancaFuncao() && ($tipo == "M"))
                                        $tipoOk = true;
                                    if ($ex->getItemLista(0)->permiteRetornoTrabalho() && ($tipo == "R"))
                                        $tipoOk = true;
                                    if ($ex->getItemLista(0)->permiteConsultaClinica() && ($tipo == "C"))
                                        $tipoOk = true;
                                    if ($ex->getItemLista(0)->permiteIndefinido() && ($tipo == "I"))
                                        $tipoOk = true;

                                    if ($tipoOk){
                                        // valida se o tipo é válido e está habilitado
                                        $fu = new funcionario();
                                        if ($fu->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                              "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                              "A.DATADEMISSAO" => array("NULL", "IS"),
                                                              "A.MATRICULA" => $funcionario), 1)){
                                            // valida se o funcionário pertence a empresa ativa

                                            $pt = new postotrabalho();
                                            if ($pt->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                  "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                  "A.CODIGO"  => $posto), 1)){
                                                // valida se o posto de trabalho pertence a empresa ativa

                                                $setorFuncaoOk = !($tipo == "M");
                                                $mensTemp = "Não foi possível validar o setor e/ou função!";

                                                if ($tipo == "M"){
                                                    $se = new setor();
                                                    if ($se->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                          "A.ATIVO" => "S",
                                                                          "A.POSTOTRABALHO" => $posto,
                                                                          "A.CODIGO" => $novosetor), 1)){
                                                        // valida se o novo setor pertence a empresa ativa e ativo

                                                        $sf = new setorfuncao();
                                                        if ($sf->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                              "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                              "A.EXCLUIDO" => "N",
                                                                              "A.SETOR" => $novosetor,
                                                                              "A.FUNCAO" => $novafuncao), 1)){
                                                            $setorFuncaoOk = true;
                                                        }else
                                                            $mensTemp = "A nova função não foi encontrada!";
                                                    }else
                                                        $mensTemp = "O novo setor não foi encontrado!";
                                                }

                                                if ($setorFuncaoOk){
                                                    $val = array("CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                 "NOMEEMPRESA"   => $em->getItemLista(0)->getNomeFantasia(),
                                                                 "CODIGOPESSOA"  => $fu->getItemLista(0)->getCodigo(),
                                                                 "NOMEPESSOA"    => $fu->getItemLista(0)->getNome(),
                                                                 "DDD"           => $fu->getItemLista(0)->getDDD(),
                                                                 "FONE"          => $fu->getItemLista(0)->getTelefoneSemDDD(),
                                                                 "TIPO"          => $tipo,
                                                                 "CODIGOSETOR"   => $fu->getItemLista(0)->getSetor()->getCodigo(),
                                                                 "NOMESETOR"     => $fu->getItemLista(0)->getSetor()->getNome(),
                                                                 "CODIGOFUNCAO"  => $fu->getItemLista(0)->getFuncao()->getCodigo(),
                                                                 "NOMEFUNCAO"    => $fu->getItemLista(0)->getFuncao()->getCodigo(),
                                                                 "USUARIOAGENDAMENTOWEB"  => $_SESSION[config::getSessao()]["codigo"],
                                                                 "NOMEUSUAGENDAMENTO"     => $_SESSION[config::getSessao()]["usuarioweb"],
                                                                 "DATAAGENDAMENTO"        => date("d.m.Y"),
                                                                 "HORAAGENDAMENTO"        => date("H:i:s"),
                                                                 "CONTROLE_USUARIO"       => "",
                                                                 "RESPONSAVELAGENDAMENTO" => $_SESSION[config::getSessao()]["usuarioweb"],
                                                                 "CODIGOPOSTOTRABALHO"    => $pt->getItemLista(0)->getCodigo(),
                                                                 "DESCRICAOPOSTOTRABALHO" => $pt->getItemLista(0)->getDescricao(),
                                                                 "WEB"                    => "S",
                                                                 "NOVO_SETOR"             => $novosetor,
                                                                 "NOVA_FUNCAO"            => $novafuncao);

                                                    $ag = new agenda();
                                                    if ($ag->agendarHorario($val, $_SESSION[config::getSessao()]["unidade"], $codigo, $hora)){
                                                        $retorno["mensagem"] = "OK";

                                                        $ee = new empresaemail();
                                                        if ($ee->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                              "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                              "A.TIPO_EMAIL" => utf8_decode("TÉCNICO")))){
                                                            $listaemails = array();
                                                            for ($i = 0; $i < $ee->getTotalLista(); $i++)
                                                                array_push($listaemails, $ee->getItemLista($i)->getEmail());

                                                            if (count($listaemails) > 0){
                                                                $email = funcoes::montarEmailAgendamento("A", $data, $hora, $fu->getItemLista(0)->getNome(), "", "");
                                                                funcoes::enviarEmail(config::getEmailContato(), $listaemails, utf8_decode("Confirmação de agendamento"), $email);
                                                            }
                                                        }
                                                    }else{
                                                        $retorno["codigo"] = 24;
                                                        $retorno["mensagem"] = "Não foi possível realizar o agendamento!";
                                                    }
                                                }else{
                                                    $retorno["codigo"] = 23;
                                                    $retorno["mensagem"] = $mensTemp;
                                                }
                                            }else{
                                                $retorno["codigo"] = 22;
                                                $retorno["mensagem"] = "Posto de trabalho não encontrado!";
                                            }

                                        }else{
                                            $retorno["codigo"] = 21;
                                            $retorno["mensagem"] = "Funcionário não encontrado!";
                                        }

                                    }else{
                                        $retorno["codigo"] = 20;
                                        $retorno["mensagem"] = "Tipo de exame não encontrado ou não habilitado!";
                                    }

                                }else{
                                    $retorno["codigo"] = 19;
                                    $retorno["mensagem"] = "Não foi possível encontrar os tipos de exames!";
                                }

                            }else{
                                $retorno["codigo"] = 18;
                                $retorno["mensagem"] = "O horário selecionado é menor que o intervalo para o agendamento!";
                            }

                        }else{
                            $retorno["codigo"] = 17;
                            $retorno["mensagem"] = "O limite de agendamento para esse dia foi excedido!";
                        }
                    }else{
                        $retorno["codigo"] = 16;
                        $retorno["mensagem"] = "O horário não está livre para agendamento!";
                    }
                }else{
                    $retorno["codigo"] = 15;
                    $retorno["mensagem"] = "Não foi encontrada a data do agendamento!";
                }
                echo json_encode($retorno);
                die;
            }

        die;
    }else{
        $e = new empresa();
        $e->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                         "A.CODIGO" => $_SESSION[config::getSessao()]["empresa_ativa"]));

         $p = new parametrosgerais();
         $p->buscar();
        
        $ti = new titreceb();
        $ti->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                          "A.DATAVENCIMENTO" => array(date('d.m.Y'), "<"),
                          "A.SITUACAO"       => "A"));
        
        $totalAberto  = $ti->getTotalLista();
        $iMaiorAtraso = 0;
        
         for ($i = 0; $i < $ti->getTotalLista(); $i++) {
            $vencimento = strtotime($ti->getItemLista($i)->getDataVencimento());
            
            $datahoje   = time();
            
            $intervalo  = ($datahoje - $vencimento) / 60 / 60 / 24;
            
            if ($intervalo > $iMaiorAtraso){
                $iMaiorAtraso = $intervalo;
            }
         }
        
        $bloquear = false;
        if ($e->getItemLista(0)->getLimiteAtraso() == 0){
            $bloquear = $totalAberto > $p->getItemLista(0)->getLimiteTituloAberto();
            //echo "titulo aberto - " . $totalAberto . " - " . $p->getItemLista(0)->getLimiteTituloAberto();
        }else{
            $bloquear = $iMaiorAtraso > $e->getItemLista(0)->getLimiteAtraso();
            //echo "limite atraso - " . $iMaiorAtraso . " - " . $e->getItemLista(0)->getLimiteAtraso();
        }
        
        
        if ($bloquear): ?>
            <div class="cl-mcont">
                <div class="block-flat">
                    <div class="form-group-sm" style="margin-left: 13px;">
                        <div class="col-md-12">
                            <h3>
                                <br/>
                                <br/>
                                <center>
                                Agendamento bloqueado! Para maiores informações solicitamos contato junto a central (41) 3393-4452 , opção 6, ou no e-mail financeiro@saudevital.med.br
                                </center>
                                <br/>
                                <br/>
                            </h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        <?php
            die;
        endif;
    }
?>
<div class="page-head">
    <h2>Novo agendamento</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li>Agenda</li>
        <li class="active">Novo agendamento</li>
    </ol>
</div>
<div class="cl-mcont">
    <div id="div-geral-dados" class="block-flat">
        <div class="form-group-sm">
            <div class="col-sm-6">
                <label>Tipo de exame:</label>
                <select id="form-select-tipo" class="form-control input-sm">
                    <?php
                        $ex = new exameweb();
                        if ($ex->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"]), 1)){
                            if ($ex->getItemLista(0)->permiteAdmissional()): ?>
                                <option value="A">ADMISSIONAL</option>
                            <?php
                            endif;
                            if ($ex->getItemLista(0)->permitePeriodico()): ?>
                                <option value="P">PERIÓDICO</option>
                            <?php
                            endif;
                            if ($ex->getItemLista(0)->permiteDemissional()): ?>
                                <option value="D">DEMISSIONAL</option>
                            <?php
                            endif;
                            if ($ex->getItemLista(0)->permiteMudancaFuncao()): ?>
                                <option value="M">MUDANÇA DE FUNÇÃO</option>
                            <?php
                            endif;
                            if ($ex->getItemLista(0)->permiteRetornoTrabalho()): ?>
                                <option value="R">RETORNO AO TRABALHO</option>
                            <?php
                            endif;
                            if ($ex->getItemLista(0)->permiteConsultaClinica()): ?>
                                <option value="C">CONSULTA CLÍNICA</option>
                            <?php
                            endif;
                            if ($ex->getItemLista(0)->permiteIndefinido()): ?>
                                <option value="I">INDEFINIDO</option>
                            <?php
                            endif;
                        }
                    ?>
                </select>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group-sm">
            <div class="col-sm-12" style="height: 58px;">
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
            <div class="col-sm-6" style="height: 58px;">
                <label>Setor:</label>
                <input type="text" id="form-input-setor" readonly class="form-control input-sm" style="background-color: #fff;">
            </div>
            <div class="col-sm-6" style="height: 58px;">
                <label>Função:</label>
                <input type="text" id="form-input-funcao" readonly class="form-control input-sm" style="background-color: #fff;">
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group-sm">
            <div class="col-sm-12" style="height: 58px;">
                <label>Posto:</label>
                <div class="input-group">
                    <input type="hidden" id="form-input-posto">
                    <input type="text" id="form-input-descricao-posto" readonly class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-xs" id="form-button-pesquisa-posto" style="height: 30px; padding-top: 4px">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group-sm" id="div-form-mudanca-funcao" style="display: none">
            <div class="col-sm-6" style="height: 58px;">
                <label>Novo setor:</label>
                <div class="input-group">
                    <input type="hidden" id="form-input-novo-setor">
                    <input type="text" id="form-input-nome-novo-setor" readonly class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-xs" id="form-button-pesquisa-novo-setor" style="height: 30px; padding-top: 4px">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="col-sm-6" style="height: 58px;">
                <label>Nova função:</label>
                <div class="input-group">
                    <input type="hidden" id="form-input-nova-funcao">
                    <input type="text" id="form-input-nome-nova-funcao" readonly class="form-control input-sm" style="background-color: #fff; border-radius: 3px;">
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-xs" id="form-button-pesquisa-nova-funcao" style="height: 30px; padding-top: 4px">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group-sm" style="margin-top: 10px">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" id="form-button-continuar">Próximo</button>
                <button type="button" class="btn btn-danger" id="form-button-cancelar-continuar">Cancelar</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div id="div-geral-calendario" class="form-group-sm" style="margin-left: 13px; display:none;">
        <div class="col-md-12">
            <div class="widget-block white-box calendar-box-sm">
                <div class="col-md-12" id="div-menos" style="padding: 15px;"></div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="widget-block white-box calendar-box-sm">
                <div class="col-md-12" style="padding: 10px 20px 10px 20px;">
                    <label>Selecione o prestador:</label>
                    <select class="form-control" id="form-select-prestador">
                        <?php
                            $em = new empresamedico();
                            if ($em->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]))){
                                for ($i = 0; $i < $em->getTotalLista(); $i++){
                                ?>
                                <option value="<?php echo $em->getItemLista($i)->getMedico()->getCodigo(); ?>"><?php echo utf8_encode($em->getItemLista($i)->getMedico()->getNome()); ?></option>
                                <?php
                                }
                            }
                        ?>
                    </select>
                </div>
                <br>
                <div class="col-md-8 blue-box calendar no-padding" style="min-width: 302px;">
                    <div class="padding ui-datepicker" id="div-calendario"></div>
                </div>
                <div class="col-md-4" style="padding-top: 10px;">
                    <input type="hidden" id="form-input-data">
                    <label>HORÁRIOS DISPONÍVEIS</label>
                    <div id="div-hora" style="height: 255px; overflow-y: scroll">
                        SELECIONE A DATA
                    </div>
                    <div style="padding-top: 10px; text-align: right">
                        <button class="btn btn-primary btn" id="form-button-gravar">Gravar</button>
                        <button type="button" class="btn btn-default btn" id="form-button-cancelar">Voltar</button>
                    </div>
                </div>
                <div class="col-md-12 padding" style="display: none"></div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
    </div>
</div>
<script>
    $('document').ready(function(){
        $('#div-calendario').datepicker({ startDate: '<?php echo date('d/m/Y'); ?>' }).on('changeDate', function(){ pesquisarHorario(); });
        $('#div-calendario').datepicker('update', '01/03/2016');

        $('#div-calendario .datepicker').css('margin', '0 auto');
        $('#div-calendario .datepicker').css('width', 'auto');
    });

    $('#form-select-prestador').on('change', function(){
        pesquisarHorario();
    });

    function pesquisarHorario(){
        var data = $("#div-calendario").data("datepicker").getDate();
        data = $.strPad(data.getDate(), 2, '0') + "/" + $.strPad((data.getMonth() + 1), 2, '0') + "/" + data.getFullYear();
        console.log(data);
        $('#form-input-data').val(data);
        $('#div-hora').html('CARREGANDO HORÁRIOS...');
        $.ajax({
            type: 'post',
            url: 'agenda-novo-2.php',
            data: 'a=hora&data=' + data +
                    '&funcionario=' + $('#form-input-funcionario').val() +
                    '&prestador=' + $('#form-select-prestador').find('option:selected').val() +
                    '&tipo=' + $('#form-select-tipo').find('option:selected').val() +
                    '&novo-setor=' + $('#form-input-novo-setor').val() +
                    '&nova-funcao=' + $('#form-input-nova-funcao').val(),
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
                $('#div-hora').html('SELECIONE A DATA');
            }else{
                var html = '';
                for (var i = 0; i < data.retorno.length; i++){
                    html += '<div class="radio"><label>';
                    html += '<input type="radio" class="icheck" name="form-radio-hora" value="' + data.retorno[i].hora + '" codigo="' + data.retorno[i].codigo + '"> ';
                    html += data.retorno[i].hora;
                    html += '</label></div>';
                }
                $('#div-hora').html(html);
                $('.icheck').iCheck({
                    radioClass: 'iradio_square-blue'
                });
            }
        });
    }

    $('#form-button-pesquisa-funcionario').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa funcionário');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('funcionario-pesquisa.php', { 'habilitaCadastro': '1' });
    });

    $('#form-button-pesquisa-posto').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa posto de trabalho');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('posto-trabalho-pesquisa.php');
    });

    $('#form-button-pesquisa-novo-setor').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa setor');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('setor-pesquisa.php');
    });

    $('#form-button-pesquisa-nova-funcao').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa função');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('funcao-pesquisa.php');
    });

    $('#form-select-tipo').on('change', function(){
        if ($('#form-select-tipo').find('option:selected').val() == 'M')
            $('#div-form-mudanca-funcao').show(0);
        else{
            $('#div-form-mudanca-funcao').hide(0);
            $('#form-input-novo-setor').val('');
            $('#form-input-nome-novo-setor').val('');
            $('#form-input-nova-funcao').val('');
            $('#form-input-descricao-nova-funcao').val('');
        }
    });

    $('#form-button-continuar').on('click', function(){
        if ($('#form-input-funcionario').val() == ''){
            $.gritter.add({
                title: 'Ops!',
                text: 'Selecione o funcionário! #1',
                class_name: 'danger'
            });
        }else
            if ($('#form-input-posto').val() == ''){
                $.gritter.add({
                    title: 'Ops!',
                    text: 'Selecione o posto de trabalho! #2',
                    class_name: 'danger'
                });
            }else
                if (($('#form-select-tipo').find('option:selected').val() == 'M') && ($('#form-input-novo-setor').val() == '')){
                    $.gritter.add({
                        title: 'Ops!',
                        text: 'Selecione o novo setor! #3',
                        class_name: 'danger'
                    });
                }else
                    if (($('#form-select-tipo').find('option:selected').val() == 'M') && ($('#form-input-nova-funcao').val() == '')){
                        $.gritter.add({
                            title: 'Ops!',
                            text: 'Selecione a nova função! #4',
                            class_name: 'danger'
                        });
                    }else{
                        $('#div-hora').html('');
                        var html = '<div class="form-group-sm">'+
                                   '<div><div class="col-sm-2" style="font-weight: bold">Tipo: </div><div class="col-sm-10">' + $('#form-select-tipo').find('option:selected').text() + '</div></div>'+
                                   '<div><div class="col-sm-2" style="font-weight: bold">Funcionário: </div><div class="col-sm-10">' + $('#form-input-nome-funcionario').val() + '</div></div>'+
                                   '<div><div class="col-sm-2" style="font-weight: bold">Setor: </div><div class="col-sm-10">' + $('#form-input-setor').val() + '</div></div>'+
                                   '<div><div class="col-sm-2" style="font-weight: bold">Função: </div><div class="col-sm-10">' + $('#form-input-funcao').val() + '</div></div>'+
                                   '<div><div class="col-sm-2" style="font-weight: bold">Posto: </div><div class="col-sm-10">' + $('#form-input-descricao-posto').val() + '</div></div>';

                        if ($('#form-select-tipo').find('option:selected').val() == 'M'){
                            html += '<div><div class="col-sm-2" style="font-weight: bold">Novo setor: </div><div class="col-sm-10">' + $('#form-input-nome-novo-setor').val() + '</div></div>'+
                                    '<div><div class="col-sm-2" style="font-weight: bold">Nova função: </div><div class="col-sm-10">' + $('#form-input-nome-nova-funcao').val() + '</div></div>';
                        }

                        html += '</div>';

                        $('#div-geral-dados').hide(0);
                        $('#div-geral-calendario').fadeIn(100);
                        $('#div-menos').html(html);

                        pesquisarHorario();
                    }
    });

    $('#form-button-gravar').on('click', function(){
        $('#form-button-gravar').addClass('disabled');
        $('#form-button-gravar').text('Aguarde...');

        $.ajax({
            type: 'post',
            url: 'agenda-novo-2.php',
            data: 'a=gravar'+
                    '&codigo=' + $('#div-hora div .checked input').attr('codigo') +
                    '&hora=' + $('#div-hora div .checked input').val() +
                    '&tipo=' + $('#form-select-tipo').find('option:selected').val() +
                    '&funcionario=' + $('#form-input-funcionario').val() +
                    '&posto=' + $('#form-input-posto').val() +
                    '&novo-setor=' + $('#form-input-novo-setor').val() +
                    '&nova-funcao=' + $('#form-input-nova-funcao').val(),
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
            }else{
                $.gritter.add({
                    title: 'Oba!',
                    text: 'O agendamento foi realizado com sucesso!',
                    class_name: 'success'
                });
                <?php if (!funcoes::acessoCelular()): ?>
                    $('#div-modal-grande').modal();
                    $('#div-modal-grande .modal-header h3').text('Requisição de exames');
                <?php endif; ?>
                $('#div-modal-grande .modal-body').html('<iframe style=" border: 0px; width: 100%; min-height: 400px;" src="relatorio.php?r=requisicao&codigo=' + $('#div-hora div .checked input').attr('codigo') + '&hora=' + $('#div-hora div .checked input').val() + '"></iframe>');
                $('#div-modal-grande iframe').css('height', $(window).height() - 170);
                carregarPagina('agenda', false, 'data=' + $('#form-input-data').val());
            }

            $('#form-button-gravar').removeClass('disabled');
            $('#form-button-gravar').text('Gravar');
        });
    });

    $('#form-button-cancelar-continuar').on('click', function(){
        carregarPagina('agenda', false);
    });

    $('#form-button-cancelar').on('click', function(){
        $('#div-menos').html('');
        $('#div-geral-dados').fadeIn(100);
        $('#div-geral-calendario').hide(0);
    });
</script>

