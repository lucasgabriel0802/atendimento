<?php
    require_once("class/config.inc.php");
    require_once("class/agenda.class.php");
    require_once("class/funcoes.class.php");
    require_once("class/funcexamesacomp.class.php");
    require_once("class/tabela.class.php");
    
    config::verificaLogin();
    
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
                    ?>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#agenda-visualiza-geral" data-toggle="tab">Geral</a></li>
                        <li><a href="#agenda-visualiza-exames" data-toggle="tab">Exames</a></li>
                    </ul>
                    <div class="tab-content" style="margin-bottom: 5px;">
                        <div id="agenda-visualiza-geral" class="tab-pane active cont">
                            <div class="form-group-sm">
                                <div class="col-md-2">
                                    <b>Empresa:</b>
                                </div>
                                <div class="col-md-8">
                                    <label><?php echo utf8_encode($ag->getItemLista(0)->getEmpresa()->getNomeFantasia());  ?></label>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group-sm">
                                <div class="col-sm-2">
                                    <b>Funcionário:</b>
                                </div>
                                <div class="col-sm-8">
                                    <label><?php echo utf8_encode($ag->getItemLista(0)->getFuncionario()->getNome());  ?></label>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group-sm">
                                <div class="col-sm-2">
                                    <b>Telefone:</b>
                                </div>
                                <div class="col-sm-8">
                                    <label><?php echo utf8_encode($ag->getItemLista(0)->getFuncionario()->getTelefone());  ?></label>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group-sm">
                                <div class="col-sm-2">
                                    <b>Setor:</b>
                                </div>
                                <div class="col-sm-8">
                                    <label><?php echo utf8_encode($ag->getItemLista(0)->getSetor()->getNome()); ?></label>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group-sm">
                                <div class="col-sm-2">
                                    <b>Função:</b>
                                </div>
                                <div class="col-sm-8">
                                    <label><?php echo utf8_encode($ag->getItemLista(0)->getFuncao()->getNome()); ?></label>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group-sm">
                                <div class="col-sm-2">
                                    <b>Data/hora:</b>
                                </div>
                                <div class="col-sm-8">
                                    <label>
                                    <?php 
                                        echo utf8_encode(date("d/m/Y", strtotime($ag->getItemLista(0)->getData()))." - {$ag->getItemLista(0)->getHora()}"); 
                                    ?>
                                    </label>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group-sm">
                                <div class="col-sm-2">
                                    <b>Exame:</b>
                                </div>
                                <div class="col-sm-8">
                                    <label>
                            <?php 
                                if ($ag->getItemLista(0)->getTipo() == "A")
                                    echo "ADMISSIONAL";
                                else
                                    if ($ag->getItemLista(0)->getTipo() == "D")
                                        echo "DEMISSIONAL";
                                    else
                                        if ($ag->getItemLista(0)->getTipo() == "P")
                                            echo "PERIÓDICO";
                                        else
                                            if ($ag->getItemLista(0)->getTipo() == "M")
                                                echo "MUDANÇA DE FUNÇÃO";
                                            else
                                                if ($ag->getItemLista(0)->getTipo() == "R")
                                                    echo "RETORNO AO TRABALHO";
                                                else
                                                    if ($ag->getItemLista(0)->getTipo() == "C")
                                                        echo "CONSULTA";
                                                    else
                                                        if ($ag->getItemLista(0)->getTipo() == "I")
                                                            echo "INDEFINIDO";
                            ?>
                                    </label>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group-sm">
                                <div class="col-sm-2">
                                    <b>Posto:</b>
                                </div>
                                <div class="col-sm-8">
                                    <label><?php echo utf8_encode($ag->getItemLista(0)->getPostoTrabalho()->getDescricao()); ?></label>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <?php 
                                if ($ag->getItemLista(0)->getHoraChegada() != "")
                                    echo "<div class=\"form-group-sm\">
                                            <div class=\"col-sm-2\">
                                                <b>Chegada:</b>
                                            </div>
                                            <div class=\"col-sm-8\">
                                                <label>".utf8_encode($ag->getItemLista(0)->getHoraChegada())."</label>
                                            </div>
                                            <div class=\"clearfix\"></div>
                                        </div>";
                            ?>

                            <?php 
                                if ($ag->getItemLista(0)->getHoraChamada() != "")
                                    echo "<div class=\"form-group-sm\">
                                            <div class=\"col-sm-2\">
                                                <b>Chamada:</b>
                                            </div>
                                            <div class=\"col-sm-8\">
                                                <label>".utf8_encode($ag->getItemLista(0)->getHoraChamada())."</label>
                                            </div>
                                            <div class=\"clearfix\"></div>
                                        </div>";
                            ?>                            
                            
                                                       
                        </div>
                        <div id="agenda-visualiza-exames" class="tab-pane cont">
                            <?php
                                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                                $cab = new cabecalho(array("class" => "no-border"));
                                $cab->addItem("Descrição", array("style" => "font-weight: bold"));
                                $tab->addCabecalho($cab);
                                
                                $fe = new funcexamesacomp();
                                $fe->buscarExames($ag->getItemLista(0)->getEmpresa()->getCodigo(), 
                                                  $ag->getItemLista(0)->getUnidade()->getCodigo(), 
                                                  $ag->getItemLista(0)->getPostoTrabalho()->getCodigo(), 
                                                  $ag->getItemLista(0)->getFuncionario()->getCodigo(), 
                                                  $ag->getItemLista(0)->getSetor()->getCodigo(), 
                                                  $ag->getItemLista(0)->getFuncao()->getCodigo(), 
                                                  $ag->getItemLista(0)->getTipo(), $ag->getItemLista(0)->getData());
                                if ($fe->buscar(array("A.EXAME" => array("NULL", "<>"), 
                                                      "A.UNIDADE" => $ag->getItemLista(0)->getUnidade()->getCodigo(),
                                                      "A.MATRICULA" => $ag->getItemLista(0)->getFuncionario()->getCodigo(),
                                                      "A.EMPRESA" => $ag->getItemLista(0)->getEmpresa()->getCodigo()))){
                                    for ($i = 0; $i < $fe->getTotalLista(); $i++){
                                        $reg = new registro();
                                        $reg->addItem(utf8_encode($fe->getItemLista($i)->getExame()->getDescricao()));                                        
                                        $tab->addRegistro($reg);
                                    }
                                    echo $tab->gerar();
                                }else   
                                    echo "<h4>Nenhum exame encontrado!</h4>";
                            ?>
                        </div>
                    </div>
                    <?php
                }else
                    echo "<h4>O agendamento não foi encontrado! #1</h4>";
            }
    }else
        echo "<h4>Alguns parâmetros não foram encontrados! #1</h4>";
?>

