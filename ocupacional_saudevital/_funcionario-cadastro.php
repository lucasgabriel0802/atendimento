<?php
    require_once("class/config.inc.php");
    require_once("class/funcionario.class.php");
    require_once("class/postotrabalho.class.php");
    require_once("class/setor.class.php");
    require_once("class/setorfuncao.class.php");    
    require_once("class/funcoes.class.php");

    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "pesquisarg"){
            $retorno = array("codigo" => 0, "mensagem" => "");
            if (isset($_POST["rg"])){
                
                if (trim($_POST["rg"]) != ""){
                    
                    $fu = new funcionario();
                    if ($fu->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                          "A.NUMERO_RG" => $_POST["rg"],
                                          "A.DATADEMISSAO" => array("NULL", "IS")))){
                        $retorno["codigo"] = 3;
                        
                        $temp = array();
                        for ($i = 0; $i < $fu->getTotalLista(); $i++){
                            array_push($temp, $fu->getItemLista($i)->getNome());
                        }
                        $retorno["mensagem"] = "O RG informado já está vinculado ao(s) funcionário(s): <br>- ".implode("<br> - ", $temp)."<br>";
                    }
                }else{
                    $retorno["codigo"] = 2;
                    $retorno["mensagem"] = "Informe o RG!";
                }
                
            }else{
                $retorno["codigo"] = 1;
                $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
            }
            
            echo json_encode($retorno);
        }else
            if ($acao == "gravar"){
                $retorno = array("codigo" => 0, "mensagem" => "");
                
                if (!isset($_POST["rg"]))
                    $retorno["codigo"] = 1;
                else
                    if (!isset($_POST["nome"]))
                        $retorno["codigo"] = 2;
                    else
                        if (!isset($_POST["data-nascimento"]))
                            $retorno["codigo"] = 3;
                        else
                            if (!isset($_POST["cpf"]))
                                $retorno["codigo"] = 4;
                            else
                                if (!isset($_POST["sexo"]))
                                    $retorno["codigo"] = 5;
                                else
                                    if (!isset($_POST["estado-civil"]))
                                        $retorno["codigo"] = 6;
                                    else
                                        if (!isset($_POST["deficiencia"]))
                                            $retorno["codigo"] = 7;
                                        else
                                            if (!isset($_POST["tipo-contrato"]))
                                                $retorno["codigo"] = 8;
                                            else           
                                                if (!isset($_POST["pis"]))
                                                    $retorno["codigo"] = 9;
                                                else
                                                    if (!isset($_POST["data-admissao"]))
                                                        $retorno["codigo"] = 10;
                                                    else
                                                        if (!isset($_POST["posto"]))
                                                            $retorno["codigo"] = 11;
                                                        else
                                                            if (!isset($_POST["setor"]))
                                                                $retorno["codigo"] = 12;
                                                            else
                                                                if (!isset($_POST["funcao"]))
                                                                    $retorno["codigo"] = 13;
                                                                else
                                                                    if (!isset($_POST["cargo"]))
                                                                        $retorno["codigo"] = 14;
                                                                    else
                                                                        if (!isset($_POST["revezamento"]))
                                                                            $retorno["codigo"] = 15;
                
                if ($retorno["codigo"] == 0){
                    if ($_POST["deficiencia"] == "BR"){
                        if (!isset($_POST["descricao-deficiencia"]))
                            $retorno["codigo"] = 16;
                    }else
                        if ($_POST["deficiencia"] == "PDH"){
                            if (!isset($_POST["tipo-deficiencia"]))
                                $retorno["codigo"] = 17;
                            else
                                if (!isset($_POST["cid10"]))
                                    $retorno["codigo"] = 18;
                                else
                                    if (!isset($_POST["perda-deficiencia"]))
                                        $retorno["codigo"] = 19;
                                    else
                                        if (!isset($_POST["descricao-deficiencia"]))
                                            $retorno["codigo"] = 20;
                                    
                        }
                }
                                                                
                if ($retorno["codigo"] > 0){
                    $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
                    echo json_encode($retorno);
                    die;
                }
                
                if (trim($_POST["rg"]) != ""){
                    
                    if (trim($_POST["nome"]) != ""){
                        
                        if (funcoes::validarCPF(trim($_POST["cpf"]))){
                        
                            if (funcoes::validarData($_POST["data-nascimento"])){

                                $deficienciaOk = false;
                                if ($_POST["deficiencia"] == "NA")
                                    $deficienciaOk = true;
                                else{
                                    if ($_POST["deficiencia"] == "BR"){
                                        if (trim($_POST["descricao-deficiencia"]) != "")
                                            $deficienciaOk = true;
                                        else{
                                            $retorno["codigo"] = 21;
                                            $retorno["mensagem"] = "Informe a descrição da deficiência reabilitada!";
                                        }
                                    }else
                                        if ($_POST["deficiencia"] == "PDH"){

                                            if (in_array($_POST["tipo-deficiencia"], array("1", "2", "3", "4", "5"))){

                                                if (is_numeric($_POST["cid10"])){

                                                    if (in_array($_POST["perda-deficiencia"], array("S", "N"))){
                                                        $deficienciaOk = true;                                                    
                                                    }else{
                                                        $retorno["codigo"] = 24;
                                                        $retorno["mensagem"] = "Selecione a perda da deficiência!";
                                                    }

                                                }else{
                                                    $retorno["codigo"] = 23;
                                                    $retorno["mensagem"] = "Selecione o CID 10!";
                                                }

                                            }else{
                                                $retorno["codigo"] = 22;
                                                $retorno["mensagem"] = "Selecione um tipo de deficiência válido!";
                                            }

                                        }
                                }

                                if ($deficienciaOk){

                                    if (in_array($_POST["tipo-contrato"], array("1", "2", "3", "4"))){

                                        if (funcoes::validarPIS($_POST["pis"])){

                                            if (funcoes::validarData($_POST["data-admissao"])){

                                                if (trim($_POST["setor"]) != ""){

                                                    if (trim($_POST["funcao"]) != ""){

                                                        //if (trim($_POST["cargo"]) != ""){

                                                            if (trim($_POST["matricularh"]) != ""){

                                                                $pt = new postotrabalho();
                                                                if ($pt->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                                      "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                                      "A.CODIGO"  => $_POST["posto"]), 1)){

                                                                    $se = new setor();
                                                                    if ($se->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                                          "A.ATIVO" => "S",
                                                                                          "A.POSTOTRABALHO" => $_POST["posto"],
                                                                                          "A.CODIGO" => $_POST["setor"]), 1)){

                                                                        $sf = new setorfuncao();
                                                                        if ($sf->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                                                              "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                                              "A.EXCLUIDO" => "N",
                                                                                              "A.SETOR" => $_POST["setor"],
                                                                                              "A.FUNCAO" => $_POST["funcao"]), 1)){

                                                                            $val = array("UNIDADE"        => $_SESSION[config::getSessao()]["unidade"],
                                                                                         "EMPRESA"        => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                                                         "NOME"           => utf8_decode($_POST["nome"]),
                                                                                         "DATANASCIMENTO" => funcoes::converterData($_POST["data-nascimento"]),
                                                                                         "SEXO"           => $_POST["sexo"],
                                                                                         "ESTADOCIVIL"    => $_POST["estado-civil"],
                                                                                         "NUMERO_RG"      => $_POST["rg"],
                                                                                         "CPF"            => $_POST["cpf"],
                                                                                         "PISPASEPCEI"    => $_POST["pis"],
                                                                                         "BR_PDH_NA"      => $_POST["deficiencia"],
                                                                                         "DATAADMISSAO"   => funcoes::converterData($_POST["data-admissao"]),
                                                                                         "CTPS_NUMERO"    => $_POST["numero-ctps"],
                                                                                         "CTPS_SERIE"     => $_POST["serie-ctps"],
                                                                                         "CTPS_UF"        => $_POST["uf-ctps"],
                                                                                         "DATACADASTRO"   => date("Y-m-d"),
                                                                                         //"REGIMEREVEZAMENTO" => $_POST["revezamento"],
                                                                                         "MATRICULAESOCIAL" => $_POST["matricularh"],
                                                                                         "SETOR"             => $_POST["setor"],
                                                                                         "FUNCAO"            => $_POST["funcao"],
                                                                                         //"CARGO"             => $_POST["cargo"],
                                                                                         "TIPOCONTRATO"      => $_POST["tipo-contrato"],
                                                                                         "CID10"             => $_POST["cid10"]);
                                                                                         //"POSTOTRABALHO"     => $_POST["posto"]);

                                                                            if (config::getExibirCargoCadFuncionario())
                                                                                $val["CARGO"] = $_POST["cargo"];
                                                                            else
                                                                                $val["CARGO"] = "0000";

                                                                            if (config::getExibirRevezamentoCadFuncionario())
                                                                                $val["REGIMEREVEZAMENTO"] = $_POST["revezamento"];
                                                                            else
                                                                                $val["REGIMEREVEZAMENTO"] = "0000";

                                                                            $fu = new funcionario();
                                                                            if ($fu->inserir($val)){
                                                                                $retorno["matricula"] = $fu->getCodigo();
                                                                            }else{
                                                                                $retorno["codigo"] = 38;
                                                                                $retorno["mensagem"] = "Não foi possível cadastrar o funcionário!";
                                                                            }

                                                                        }else{
                                                                            $retorno["codigo"] = 37;
                                                                            $retorno["mensagem"] = "Função não encontrada!";
                                                                        }

                                                                    }else{
                                                                        $retorno["codigo"] = 36;
                                                                        $retorno["mensagem"] = "Setor não encontrado!";
                                                                    }

                                                                }else{
                                                                    $retorno["codigo"] = 35;
                                                                    $retorno["mensagem"] = "Posto de trabalho não encontrado!";
                                                                }

                                                            }else{
                                                                $retorno["codigo"] = 39;
                                                                $retorno["mensagem"] = "Informe a matricula do RH!";
                                                            }

    //                                                    }else{;
    //                                                        $retorno["codigo"] = 33;
    //                                                        $retorno["mensagem"] = "Selecione o cargo!";
    //                                                    }

                                                    }else{
                                                        $retorno["codigo"] = 32;
                                                        $retorno["mensagem"] = "Selecione a função!";
                                                    }

                                                }else{
                                                    $retorno["codigo"] = 31;
                                                    $retorno["mensagem"] = "Selecione o setor!";
                                                }

                                            }else{
                                                $retorno["codigo"] = 30;
                                                $retorno["mensagem"] = "Informe a data de admissão!";
                                            }

                                        }else{
                                            $retorno["codigo"] = 29;
                                            $retorno["mensagem"] = "Informe um PIS válido!";
                                        }

                                    }else{
                                        $retorno["codigo"] = 28;
                                        $retorno["mensagem"] = "Selecione um tipo de contrato válido!";
                                    }

                                }else{
                                    $retorno["codigo"] = 27;
                                    $retorno["mensagem"] = "Ocorreu um erro durante a validação!";
                                }
                            }else{
                                $retorno["codigo"] = 27;
                                $retorno["mensagem"] = "Informe a data de nascimento!";
                            }
                            
                      /*  }else{
                            $retorno["codigo"] = 26;
                            $retorno["mensagem"] = "Informe um CPF válido!";
                        } */
                    }else{
                        $retorno["codigo"] = 25;
                        $retorno["mensagem"] = "Informe o nome!";
                    }                        
                    
                }else{
                    $retorno["codigo"] = 24;
                    $retorno["mensagem"] = "Informe o RG!";
                }
                
                echo json_encode($retorno);
            }
        
        die;
    }
?>

<div class="form-group-sm">
    <div class="col-sm-5">
        <label>RG:</label>
        <div class="input-group" style="margin-bottom: 0px;">
            <input type="text" id="cadastro-input-rg" class="form-control input-sm" maxlength="18">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="form-button-proximo">
                    <i class="fa fa-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-danger" style="height: 30px; padding-top: 4px" id="form-button-cancelar-proximo">
                    <i class="fa fa-times"></i>
                </button>
            </span>
        </div>
    </div>
    <div class="clearfix"></div>
</div>  
<div class="form-group-sm" style="padding: 12px; display: none" id="div-cadastro-parte-2">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#cadastro-funcionario-pessoais" data-toggle="tab">Pessoais</a></li>
        <li><a href="#cadastro-funcionario-funcionais" data-toggle="tab">Funcionais</a></li>
    </ul>
    <div class="tab-content" style="margin-bottom: 10px;">
        <div id="cadastro-funcionario-pessoais" class="tab-pane active cont">
            <div class="form-group-sm">
                <div class="col-sm-8">
                    <label>Nome: *</label>
                    <input type="text" class="form-control input-sm" id="cadastro-input-nome">
                </div>
                <div class="col-sm-4">
                    <label>CPF: *</label>
                    <input type="text" class="form-control input-sm" id="cadastro-input-cpf">
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-4">
                    <label>Nascimento: *</label>
                    <input type="text" class="form-control input-sm" id="cadastro-input-data-nascimento" style="width: 90px;">
                </div>
                <div class="col-sm-4">
                    <label>Sexo: *</label>
                    <select class="form-control input-sm" id="cadastro-select-sexo">
                        <option value="M">MASCULINO</option>
                        <option value="F">FEMININO</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label>Estado civil: *</label>
                    <select class="form-control input-sm" id="cadastro-select-estado-civil">
                        <option value="S">SOLTEIRO</option>
                        <option value="C">CASADO</option>
                        <option value="D">DIVORCIADO</option>
                        <option value="V">VIÚVO</option>
                        <option value="O">OUTROS</option>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-6">
                    <label>Deficiência:</label>
                    <select class="form-control input-sm" id="cadastro-select-deficiencia">
                        <option value="NA">NÃO APLICÁVEL</option>
                        <option value="BR">BENEFICIÁRIO REABILITADO</option>
                        <option value="PDH">PORTADOR DE DEFICIÊNCIA HABILITADO</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label>Tipo:</label>
                    <select class="form-control input-sm" id="cadastro-select-tipo-deficiencia" disabled>
                        <option value=""></option>
                        <option value="1">FÍSICO</option>
                        <option value="2">AUDITIVO</option>
                        <option value="3">VISUAL</option>
                        <option value="4">MENTAL</option>
                        <option value="5">DEFICIÊNCIAS MULTIPLAS</option>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-6">
                    <label>CID10:</label>
                    <div class="input-group" style="margin-bottom: 0px;">
                        <input type="hidden" id="cadastro-input-cid10">
                        <input type="input" id="cadastro-input-descricao-cid10" class="form-control input-sm" readonly style="background-color: #fff; border-radius: 3px;" disabled>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary disabled" style="height: 30px; padding-top: 4px" id="cadastro-button-pesquisar-cid10">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label>Perda:</label>
                    <select class="form-control input-sm" id="cadastro-select-perda-deficiencia" disabled>
                        <option value="S">OCUPACIONAL</option>
                        <option value="N">NÃO OCUPACIONAL</option>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-12">
                    <label>Descrição:</label>
                    <input class="form-control input-sm" id="cadastro-input-descricao-deficiencia" disabled>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div id="cadastro-funcionario-funcionais" class="tab-pane cont">
            <div class="form-group-sm">
                <div class="col-sm-4">
                    <label>Tipo de contrato:</label>
                    <select class="form-control input-sm" id="cadastro-select-tipo-contrato">
                        <option value="1">REGISTRADO</option>
                        <option value="2">ESTAGIÁRIO</option>
                        <option value="3">CONTRATADO</option>
                        <option value="4">TERCEIRIZADO</option>                        
                    </select>
                </div>
                <div class="col-sm-4">
                    <label>PIS: *</label>
                    <input type="text" class="form-control input-sm" id="cadastro-input-pis">
                </div>
                <div class="col-sm-4">
                    <label>Admissão: *</label>
                    <input type="text" class="form-control input-sm" id="cadastro-input-data-admissao" style="width: 90px;">
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-4">
                    <label>Número CTPS:</label>
                    <input type="text" class="form-control input-sm" id="cadastro-input-numero-ctps" maxlength="7">
                </div>
                <div class="col-sm-4">
                    <label>Série CTPS:</label>
                    <input type="text" class="form-control input-sm" id="cadastro-input-serie-ctps" maxlength="5">
                </div>
                <div class="col-sm-4">
                    <label>UF CTPS:</label>
                    <select class="form-control input-sm" id="cadastro-select-uf-ctps">
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
            <div class="form-group-sm">
                <div class="col-sm-12">
                    <label>Posto de trabalho: *</label>
                    <div class="input-group" style="margin-bottom: 0px;">
                        <input type="hidden" id="cadastro-input-posto">
                        <input type="input" id="cadastro-input-descricao-posto" class="form-control input-sm" readonly style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="cadastro-button-pesquisar-posto">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-sm">
                <div class="col-sm-6">
                    <label>Setor: *</label>
                    <div class="input-group" style="margin-bottom: 0px;">
                        <input type="hidden" id="cadastro-input-setor">
                        <input type="input" id="cadastro-input-nome-setor" class="form-control input-sm" readonly style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="cadastro-button-pesquisar-setor">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label>Função: *</label>
                    <div class="input-group" style="margin-bottom: 0px;">
                        <input type="hidden" id="cadastro-input-funcao">
                        <input type="input" id="cadastro-input-nome-funcao" class="form-control input-sm" readonly style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="cadastro-button-pesquisar-funcao">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
                if (config::getExibirCargoCadFuncionario()) :
            ?>
            <div class="form-group-sm">
                <div class="col-sm-12">
                    <label>Cargo:</label>
                    <div class="input-group" style="margin-bottom: 0px;">
                        <input type="hidden" id="cadastro-input-cargo">
                        <input type="input" id="cadastro-input-descricao-cargo" class="form-control input-sm" readonly style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="cadastro-button-pesquisar-cargo">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
                endif;
                if (config::getExibirRevezamentoCadFuncionario()) :
            ?>
            <div class="form-group-sm">
                <div class="col-sm-12">
                    <label>Revezamento:</label>
                    <div class="input-group" style="margin-bottom: 0px;">
                        <input type="hidden" id="cadastro-input-revezamento">
                        <input type="input" id="cadastro-input-descricao-revezamento" class="form-control input-sm" readonly style="background-color: #fff; border-radius: 3px;">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="cadastro-button-pesquisar-revezamento">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
                endif;
            ?>
            <div class="form-group-sm">
                <div class="col-sm-4">
                    <label>Matricula RH: *</label>
                    <input type="text" class="form-control input-sm" id="cadastro-input-matricularh">
                </div>
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <label>* Campos obrigatórios</label>
    <div class="form-group-sm">
        <div class="col-sm-12" style="text-align: right">
            <button type="button" class="btn btn-primary" id="cadastro-button-gravar">Gravar</button>
            <button type="button" class="btn btn-danger" id="cadastro-button-cancelar">Cancelar</button>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#cadastro-input-data-nascimento').datepicker();
        $('#cadastro-input-data-nascimento').mask('99/99/9999');
        
        $('#cadastro-input-data-admissao').datepicker();
        $('#cadastro-input-data-admissao').mask('99/99/9999');
        
        $('#cadastro-input-pis').mask('999.99999.99-9');
        $('#cadastro-input-cpf').mask('999.999.999-99');
    });
    
    $('#form-button-cancelar-proximo').on('click', function(){
        $('#div-modal-cadastro').modal('toggle');
    });
    
    $('#form-button-proximo').on('click', function(){
        $.ajax({
            type: 'post',
            url: 'funcionario-cadastro.php',
            data: 'a=pesquisarg&rg=' + $('#cadastro-input-rg').val(),
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
                $('#cadastro-input-rg').parent().parent().find('label').text('RG: ' + $('#cadastro-input-rg').val());
                $('#cadastro-input-rg').parent().hide(0);
                $('#form-button-proximo').remove();
                $('#div-cadastro-parte-2').fadeIn(1000);
            }
        });
    });
    
    $('#cadastro-button-pesquisar-posto').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa posto de trabalho');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('posto-trabalho-pesquisa.php', { 'prefixo': 'cadastro' });
    });   
    
    $('#cadastro-button-pesquisar-setor').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa setor');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('setor-pesquisa.php', { 'prefixo': 'cadastro' });
    });  
    
    $('#cadastro-button-pesquisar-funcao').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa função');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('funcao-pesquisa.php', { 'prefixo': 'cadastro' });
    });    
    
    $('#cadastro-button-pesquisar-cargo').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa cargo');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('cargo-pesquisa.php', { 'prefixo': 'cadastro' });
    });     
    
    $('#cadastro-button-pesquisar-revezamento').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa revezamento');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('revezamento-pesquisa.php', { 'prefixo': 'cadastro' });
    }); 
    
    $('#cadastro-button-pesquisar-cid10').on('click', function(){
        $('#div-modal').modal();
        $('#div-modal .modal-header h3').text('Pesquisa CID 10');
        $('#div-modal .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal .modal-body').load('cid10-pesquisa.php', { 'prefixo': 'cadastro' });
    });   
    
    $('#cadastro-button-gravar').on('click', function(){
        $('#cadastro-button-gravar').addClass('disabled');
        $('#cadastro-button-gravar').text('Aguarde...');
        
        $.ajax({
            type: 'post',
            url: 'funcionario-cadastro.php',
            data: 'a=gravar&rg=' + $('#cadastro-input-rg').val() + 
                    '&nome=' + $('#cadastro-input-nome').val().toUpperCase() + 
                    '&cpf=' + $('#cadastro-input-cpf').val() +
                    '&data-nascimento=' + $('#cadastro-input-data-nascimento').val() + 
                    '&sexo=' + $('#cadastro-select-sexo').find('option:selected').val() + 
                    '&estado-civil=' + $('#cadastro-select-estado-civil').find('option:selected').val() + 
                    '&deficiencia=' + $('#cadastro-select-deficiencia').find('option:selected').val() + 
                    '&tipo-deficiencia=' + $('#cadastro-select-tipo-deficiencia').find('option:selected').val() + 
                    '&cid10=' + $('#cadastro-input-cid10').val() + 
                    '&perda-deficiencia=' + $('#cadastro-select-perda-deficiencia').find('option:selected').val() + 
                    '&descricao-deficiencia=' + $('#cadastro-input-descricao-deficiencia').val() + 
                    '&tipo-contrato=' + $('#cadastro-select-tipo-contrato').find('option:selected').val() + 
                    '&pis=' + $('#cadastro-input-pis').val() + 
                    '&data-admissao=' + $('#cadastro-input-data-admissao').val() + 
                    '&numero-ctps=' + $('#cadastro-input-numero-ctps').val() +
                    '&serie-ctps=' + $('#cadastro-input-serie-ctps').val() +
                    '&uf-ctps=' + $('#cadastro-select-uf-ctps').find('option:selected').val() +
                    '&posto=' + $('#cadastro-input-posto').val() + 
                    '&setor=' + $('#cadastro-input-setor').val() + 
                    '&funcao=' + $('#cadastro-input-funcao').val() + 
                    '&cargo=' + $('#cadastro-input-cargo').val() + 
                    '&revezamento=' + $('#cadastro-input-revezamento').val()+
                    '&matricularh=' + $('#cadastro-input-matricularh').val(),
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
                    text: 'Funcionário cadastrado com sucesso!',
                    class_name: 'success'
                });
                
                $('#form-input-funcionario').val(data.matricula);
                $('#form-input-nome-funcionario').val($('#cadastro-input-nome').val().toUpperCase());
                $('#form-input-setor').val($('#cadastro-input-nome-setor').val().toUpperCase());
                $('#form-input-funcao').val($('#cadastro-input-nome-funcao').val().toUpperCase());
                $('#form-input-posto').val($('#cadastro-input-posto').val().toUpperCase());
                $('#form-input-descricao-posto').val($('#cadastro-input-descricao-posto').val().toUpperCase());
                $('#div-modal-cadastro').modal('toggle');
            }
            
            $('#cadastro-button-gravar').removeClass('disabled');
            $('#cadastro-button-gravar').text('Gravar');
        });
    });
    
    $('#cadastro-button-cancelar').on('click', function(){
        $('#div-modal-cadastro').modal('toggle');
    });
    
    $('#cadastro-select-deficiencia').on('change', function(){
        if ($('#cadastro-select-deficiencia').find('option:selected').val() == "NA"){
            $('#cadastro-select-tipo-deficiencia').attr('disabled', 'disabled');
            $('#cadastro-input-descricao-cid10').attr('disabled', 'disabled');
            $('#cadastro-button-pesquisar-cid10').addClass('disabled');
            $('#cadastro-select-perda-deficiencia').attr('disabled', 'disabled');
            $('#cadastro-input-descricao-deficiencia').attr('disabled', 'disabled');
            
            $('#cadastro-input-cid10').val('');
            $('#cadastro-select-tipo-deficiencia').val('');
            $('#cadastro-input-descricao-cid10').val('');
            $('#cadastro-select-perda-deficiencia').val('');
            $('#cadastro-input-descricao-deficiencia').val('');
        }else
            if ($('#cadastro-select-deficiencia').find('option:selected').val() == "BR"){
                $('#cadastro-select-tipo-deficiencia').attr('disabled', 'disabled');
                $('#cadastro-input-descricao-cid10').attr('disabled', 'disabled');
                $('#cadastro-button-pesquisar-cid10').addClass('disabled');
                $('#cadastro-select-perda-deficiencia').attr('disabled', 'disabled');
                
                $('#cadastro-input-descricao-deficiencia').removeAttr('disabled');

                $('#cadastro-input-cid10').val('');
                $('#cadastro-select-tipo-deficiencia').val('');
                $('#cadastro-input-descricao-cid10').val('');
                $('#cadastro-select-perda-deficiencia').val('');
            }else
                if ($('#cadastro-select-deficiencia').find('option:selected').val() == "PDH"){
                    $('#cadastro-select-tipo-deficiencia').removeAttr('disabled', 'disabled');
                    $('#cadastro-input-descricao-cid10').removeAttr('disabled', 'disabled');
                    $('#cadastro-button-pesquisar-cid10').removeClass('disabled');
                    $('#cadastro-select-perda-deficiencia').removeAttr('disabled', 'disabled');
                    $('#cadastro-input-descricao-deficiencia').removeAttr('disabled');
                }
            
    });
    
</script>