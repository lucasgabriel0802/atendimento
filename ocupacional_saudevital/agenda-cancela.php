<?php
    require_once("class/config.inc.php");
    require_once("class/agenda.class.php");
    require_once("class/agendacancelamento.class.php");
    require_once("class/funcionario.class.php");
    require_once("class/empresaemail.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    
    config::verificaLogin();
    
    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "cancelar"){
            $retorno = array("codigo" => 0, "mensagem" => "");
            
            if (isset($_POST["codigo"]) && is_numeric($_POST["codigo"])){
                if (isset($_POST["hora"]) && funcoes::validarHora($_POST["hora"])){
                    if ((isset($_POST["motivo"]) && ($_POST["motivo"] != ""))){
                        $ag = new agenda();
                        if ($ag->buscar(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                              "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                              "A.CODIGO"        => $_POST["codigo"],
                                              "A.HORA"          => $_POST["hora"]))){

                            $dataAgora = strtotime(date("Y-m-d H:i:s"));
                            $dataHoraAgenda = strtotime("{$ag->getItemLista(0)->getData()} {$ag->getItemLista(0)->getHora()}");

                            if ($dataAgora < $dataHoraAgenda){

                                if (($ag->getItemLista(0)->getHoraChegada() == "") && ($ag->getItemLista(0)->getStatus() != "F")){

                                    $intervaloAgoraEAgenda = $dataHoraAgenda - $dataAgora; // retorna em segundos
                                    $intervaloAgoraEAgenda = $intervaloAgoraEAgenda / 60; // converte pra minutos

                                    if (($ag->getItemLista(0)->getEmpresa()->getIntervaloCancelamento() == 0) || 
                                            ($intervaloAgoraEAgenda > $ag->getItemLista(0)->getEmpresa()->getIntervaloCancelamento())){

                                        $ag->buscarDataAgenda($_SESSION[config::getSessao()]["unidade"], $_POST["codigo"]);
                                        $ac = new agendacancelamento();
                                        $ac->setUnidade($ag->getItemLista(0)->getUnidade());
                                        $ac->setData($ag->getData());
                                        $ac->setHora($_POST["hora"]);
                                        $ac->setMedico($ag->getItemLista(0)->getMedico());
                                        $ac->setEmpresa($ag->getItemLista(0)->getEmpresa());
                                        $ac->setFuncionario($ag->getItemLista(0)->getFuncionario());
                                        $ac->setPostoTrabalho($ag->getItemLista(0)->getPostoTrabalho());
                                        $ac->setTipoExame($ag->getItemLista(0)->getTipo());
                                        $ac->setResponsavelAgendamento($ag->getItemLista(0)->getResponsavelAgendamento());
                                        $ac->setResponsavelCancelamento($_SESSION[config::getSessao()]["usuarioweb"]);
                                        $ac->setMotivoCancelamento($_POST["motivo"]);
                                        $ac->setDataCancelamento(date("d.m.Y"));
                                        $ac->setHoraCancelamento(date("H:i:s"));
                                        $ac->setUsuarioCancelamento($_SESSION[config::getSessao()]["usuarioweb"]);
                                        if ($ac->inserir()){

                                            if ($ag->liberarHorario($_SESSION[config::getSessao()]["unidade"], $_POST["codigo"], $_POST["hora"])){
                                                $retorno["mensagem"] = "OK";
                                                
                                                if (config::getModeloAgendamento() == 2){
                                                    $ee = new empresaemail();
                                                    if ($ee->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                          "A.TIPO_EMAIL" => utf8_decode("TÉCNICO")))){
                                                        $listaemails = array();
                                                        for ($i = 0; $i < $ee->getTotalLista(); $i++)
                                                            array_push($listaemails, $ee->getItemLista($i)->getEmail());

                                                        if (count($listaemails) > 0){
                                                            $email = funcoes::montarEmailAgendamento("C", $ag->getData(), $_POST["hora"], $ag->getItemLista(0)->getFuncionario()->getNome(), $_SESSION[config::getSessao()]["usuarioweb"], $_POST["motivo"]);
                                                            funcoes::enviarEmail(config::getEmailContato(), $listaemails, "Cancelamento de agendamento", $email);
                                                        }
                                                    }
                                                }
                                                
                                                if ($ag->getItemLista(0)->getTipo() == 'A'){
                                                    $ag->buscar([
                                                        "A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                        "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                        "A.CODIGOPESSOA"  => $ag->getItemLista(0)->getFuncionario()->getCodigo()
                                                    ]);
                                                    
                                                    if ($ag->getTotalRegistros() == 0){ // se tiver não tiver nenhum agendamento, remove o funcionario
                                                    
                                                        try{
                                                            $fu = new funcionario();
                                                            $fu->remover($_SESSION[config::getSessao()]["unidade"], $_SESSION[config::getSessao()]["empresa_ativa"], $ag->getItemLista(0)->getFuncionario()->getCodigo());
                                                        } catch (Exception $ex) { }
                                                        
                                                    }
                                                }
                                            }else{
                                                $retorno["codigo"] = 10;
                                                $retorno["mensagem"] = "Não foi possível efetivar o cancelamento!";
                                            }

                                        }else{
                                            $retorno["codigo"] = 9;
                                            $retorno["mensagem"] = "Não foi possível efetivar o cancelamento!";
                                        }
                                    }else{
                                        $retorno["codigo"] = 8;
                                        $retorno["mensagem"] = "Só é possível cancelar um agendamento com pelo menos "
                                                                . "{$ag->getItemLista(0)->getEmpresa()->getIntervaloCancelamento()} "
                                                                . "minuto(s) de antecedência!";
                                    }

                                }else{
                                    if ($ag->getItemLista(0)->getHoraChegada() != ""){
                                        $retorno["codigo"] = 6;
                                        $retorno["mensagem"] = "O funcionário já chegou até a unidade!";
                                    }else
                                        if ($ag->getItemLista(0)->getStatus() == "F"){
                                            $retorno["codigo"] = 7;
                                            $retorno["mensagem"] = "O funcionário faltou!";
                                        }
                                }

                            }else{
                                $retorno["codigo"] = 5;
                                $retorno["mensagem"] = "Não é possível cancelar um agendamento antigo!";
                            }
                        }else{
                            $retorno["codigo"] = 4;
                            $retorno["mensagem"] = "Agendamento não encontrado!";
                        }                    
                    }else{
                        $retorno["codigo"] = 3;
                        if (!isset($_POST["motivo"]))
                            $retorno["mensagem"] = "Alguns parâmetros estão errados!";
                        else
                            if ($_POST["motivo"] == "")
                                $retorno["mensagem"] = "Informe o motivo do cancelamento!";
                    }
                }else{
                    $retorno["codigo"] = 2;
                    $retorno["mensagem"] = "Alguns parâmetros estão errados!";
                }
            }else{
                $retorno["codigo"] = 1;
                $retorno["mensagem"] = "Alguns parâmetros estão errados!";
            }
            echo json_encode($retorno);
        }
        
        die;
    }
    
    if (isset($_POST["codigo"]) && (isset($_POST["hora"]))){
        if (!is_numeric($_POST["codigo"]))
            echo "<h4>Alguns parâmetros estão errados! #1</h4>";
        else
            if (!funcoes::validarHora($_POST["hora"])){
                echo "<h4>Alguns parâmetros estão errados! #2</h4>";
            }else{
                $ag = new agenda();
                if ($ag->buscar(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                      "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                      "A.CODIGO" => $_POST["codigo"],
                                      "A.HORA" => $_POST["hora"]))){
                    
                    $dataAgora = strtotime(date("Y-m-d H:i:s"));
                    $dataHoraAgenda = strtotime("{$ag->getItemLista(0)->getData()} {$ag->getItemLista(0)->getHora()}");
                    
                    if ($dataAgora > $dataHoraAgenda){
                        echo "<h4>Não é possível cancelar um agendamento antigo! #3</h4>";
                        die;
                    }

                    if ($ag->getItemLista(0)->getHoraChegada() != ""){
                        echo "<h4>O funcionário já chegou até a unidade! #4</h4>";
                        die;
                    }
                    
                    if ($ag->getItemLista(0)->getStatus() == "F"){
                        echo "<h4>O funcionário faltou! #4</h4>";
                        die; 
                    }
                    
                    $intervaloAgoraEAgenda = $dataHoraAgenda - $dataAgora; // retorna em segundos
                    $intervaloAgoraEAgenda = $intervaloAgoraEAgenda / 60; // converte pra minutos

                    if ($ag->getItemLista(0)->getEmpresa()->getIntervaloCancelamento() > 0)
                        if ($intervaloAgoraEAgenda < $ag->getItemLista(0)->getEmpresa()->getIntervaloCancelamento()){
                            echo "<h4>Só é possível cancelar um agendamento com pelo menos {$ag->getItemLista(0)->getEmpresa()->getIntervaloCancelamento()} minuto(s) de antecedência! #5</h4>";
                            die;
                        }
                    
                    ?>
                        <label style="width: 105px; text-align: right;">Empresa:</label>
                        <?php echo utf8_encode("{$ag->getItemLista(0)->getEmpresa()->getCodigo()} - {$ag->getItemLista(0)->getEmpresa()->getNomeFantasia()}");  ?><br>
                        <label style="width: 105px; text-align: right;">Funcionário:</label>
                        <?php echo utf8_encode("{$ag->getItemLista(0)->getFuncionario()->getCodigo()} - {$ag->getItemLista(0)->getFuncionario()->getNome()}");  ?><br>
                        <label style="width: 105px; text-align: right;">Data/hora:</label>
                        <?php echo utf8_encode(date("d/m/Y", strtotime($ag->getItemLista(0)->getData()))." - {$ag->getItemLista(0)->getHora()}"); ?><br>
                        <label style="text-align: right;">Informe o motivo:</label>
                        <div>
                            <textarea id="modal-textarea-motivo-cancelamento" class="form-control input-sm" style="height: 80px;"></textarea>
                        </div><br><br>
                        
                        <button type="button" class="btn btn-primary" id="modal-button-efetivar-cancelamento">Efetivar</button>
                        <button type="button" class="btn btn-danger" id="modal-button-abortar-cancelamento">Abortar</button>
                    <?php
                }else
                    echo "<h4>O agendamento não foi encontrado! #1</h4>";
            }
    }else
        echo "<h4>Alguns parâmetros não foram encontrados! #1</h4>";
?>
<script>
    $('#modal-button-efetivar-cancelamento').on('click', function(){
        $('#modal-button-efetivar-cancelamento').addClass('disabled');
        $('#modal-button-efetivar-cancelamento').text('Aguarde...');
        
        $.ajax({
            type: 'post',
            url: 'agenda-cancela.php',
            data: 'a=cancelar&codigo=<?php echo $_POST["codigo"]; ?>&hora=<?php echo $_POST["hora"] ?>&motivo=' + $('#modal-textarea-motivo-cancelamento').val(),
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
                    title: 'Tudo certo',
                    text: 'O agendamento foi cancelado com sucesso!',
                    class_name: 'success'
                });
                $('#div-modal').modal('toggle');
                carregarPagina('agenda', false, 'data=' + $('#agenda-data').val());
            }
            
            $('#modal-button-efetivar-cancelamento').removeClass('disabled');
            $('#modal-button-efetivar-cancelamento').text('Efetivar');
        });
    });
    
    $('#modal-button-abortar-cancelamento').on('click', function(){
        $('#div-modal .modal-dialog .modal-content .modal-header button').click();
    });    
</script>

