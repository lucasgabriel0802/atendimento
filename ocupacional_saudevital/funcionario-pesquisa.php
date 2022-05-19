<?php
    require_once("class/config.inc.php");
    require_once("class/funcionario.class.php");
    require_once("class/tabela.class.php");
    require_once("class/funcoes.class.php");
    
    config::verificaLogin();

        
    if (isset($_POST["a"])){
        $acao = $_POST["a"];

        if ($acao == "pesquisa"){

            $pagina = 1;
            $nome   = "";
            
            $status = "ATIVOS";
            if (isset($_POST["situacao"]))
                $status = $_POST["situacao"];
            
            if (isset($_POST["nome"]))
                $nome = $_POST["nome"];
            
            if (isset($_POST["pagina"]))
                if (is_numeric($_POST["pagina"]))
                    $pagina = $_POST["pagina"];
            
            $pagina--; // inicia com 0
            $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
            $totalRegistros = 0;
            
            if ($status == "ATIVOS"){
                $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                "A.DATADEMISSAO" => array("NULL", "IS"),
                                "A.SITUACAO" => "ATIVO");
            }else{
                $filtro = array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]);                
            }
                
            if ($nome != "")
                $filtro["A.NOME"] = array($nome, "CONTAINING");
            
            $fu = new funcionario();
            if ($fu->buscar($filtro, $limite, array("A.NOME" => "ASC"))){
                $totalRegistros = $fu->getTotalRegistros();
                
                $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                $cab = new cabecalho(array("class" => "no-border"));
                $cab->addItem("Código", array("style" => "width: 65px; font-weight: bold"));
                $cab->addItem("Nome", array("style" => "font-weight: bold"));
                $cab->addItem($status, array("style" => "width: 20px"));
                $tab->addCabecalho($cab);
                
                for ($i = 0; $i < $fu->getTotalLista(); $i++){
                    $reg = new registro(array("class" => "no-border-y"));
                    $reg->addItem($fu->getItemLista($i)->getCodigo());
                    $reg->addItem(utf8_encode($fu->getItemLista($i)->getNome()));
                    $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" "
                                  . "codigo=\"{$fu->getItemLista($i)->getCodigo()}\""
                                  . "nome=\"".utf8_encode($fu->getItemLista($i)->getNome())."\""
                                  . "setor=\"".utf8_encode($fu->getItemLista($i)->getSetor()->getNome())."\""
                                  . "funcao=\"".utf8_encode($fu->getItemLista($i)->getFuncao()->getNome())."\""
                                  . "nomemae=\"{$fu->getItemLista($i)->getNomeMae()}\""
                                  . "sexocat=\"{$fu->getItemLista($i)->getSexoCAT()}\""
                                  . "estadocivil=\"{$fu->getItemLista($i)->getEstadoCivilCAT()}\""
                                  . "datanascimento=\"{$fu->getItemLista($i)->getDataNascimento()}\""
                                  . "numeroctps=\"{$fu->getItemLista($i)->getCTPSNumero()}\""
                                  . "ufctps=\"{$fu->getItemLista($i)->getCTPSUF()}\""
                                  . "seriectps=\"{$fu->getItemLista($i)->getCTPSSerie()}\""
                                  . "numerorg=\"{$fu->getItemLista($i)->getRG()}\""
                                  . "numeropis=\"{$fu->getItemLista($i)->getPIS()}\""
                                  . "postotrabalho=\"{$fu->getItemLista($i)->getPostoTrabalho()->getCodigo()}\""
                                  . "descpostotrabalho=\"".utf8_encode($fu->getItemLista($i)->getPostoTrabalho()->getDescricao())."\">"
                                          . "<i class=\"fa fa-check\"></i></a>");
                    $tab->addRegistro($reg);
                }
                echo $tab->gerar();
                funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarFuncionario('<pagina>');");
            }else
                echo "<h5>Nenhum resultado encontrado!</h5>";
        }
        
        die;
    }
?>
<div class="form-group-sm">
    <div class="col-md-12">
        <label>Funcionário:</label>
        <div class="input-group">
            <input type="input" id="modal-input-nome-funcionario" class="form-control input-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                    <i class="fa fa-search"></i>
                </button>
                <?php 
                    if (isset($_POST["habilitaCadastro"]))
                        echo "<button type=\"button\" class=\"btn btn-primary\" style=\"height: 30px; padding-top: 4px\" id=\"modal-button-adicionar\">
                                <i class=\"fa fa-plus\"></i>
                            </button>";
                ?>
            </span>
        </div>
    </div>
</div>    
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-div-pesquisa-funcionario"></div>
<script>
    $('#modal-button-pesquisar').on('click', function(){
        pesquisarFuncionario('1');
    });
    
    $('#modal-button-adicionar').on('click', function(){
        $('#div-modal').modal('toggle');
        $('#div-modal-cadastro').modal();
        $('#div-modal-cadastro .modal-header h3').text('Cadastro de funcionário');
        $('#div-modal-cadastro .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal-cadastro .modal-body').load('funcionario-cadastro.php');            
    });
    $('#modal-input-nome-funcionario').keypress(function(event){
        if (event.which == 13)
           $('#modal-button-pesquisar').click();
    });
    
    $('document').ready(function(){
        $('#modal-button-pesquisar').click();
    });
    
    function pesquisarFuncionario(pagina){
        $('#modal-div-pesquisa-funcionario').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#modal-div-pesquisa-funcionario').load('funcionario-pesquisa.php', 
                                                    {'a': 'pesquisa', 
                                                     'nome': $('#modal-input-nome-funcionario').val(),
                                                     'situacao': $('#modal-input-situacao').val() ,
                                                     'pagina': pagina 
                                                    }, function(){
                                                        $('#modal-div-pesquisa-funcionario a').each(function(){
                                                            $(this).on('click', function(){
                                                                <?php
                                                                    $prefixo = "form";
                                                                    if (isset($_POST["prefixo"]))
                                                                        $prefixo = $_POST["prefixo"];
                                                                ?>                                                                
                                                                $('#<?= $prefixo ?>-input-funcionario').val($(this).attr('codigo'));
                                                                $('#<?= $prefixo ?>-input-nome-funcionario').val($(this).attr('nome'));
                                                                $('#<?= $prefixo ?>-input-setor').val($(this).attr('setor'));
                                                                $('#<?= $prefixo ?>-input-funcao').val($(this).attr('funcao'));
                                                                $('#<?= $prefixo ?>-input-posto').val($(this).attr('postotrabalho'));
                                                                $('#<?= $prefixo ?>-input-descricao-posto').val($(this).attr('descpostotrabalho'));
                                                                $('#<?= $prefixo ?>-input-nome-mae').val($(this).attr('nomemae'));
                                                                $('#<?= $prefixo ?>-input-data-nascimento').val($(this).attr('datanascimento'));
                                                                $('#<?= $prefixo ?>-input-numero-ctps').val($(this).attr('numeroctps'));
                                                                $('#<?= $prefixo ?>-input-serie-ctps').val($(this).attr('seriectps'));
                                                                $('#<?php echo $prefixo; ?>-input-select-uf-ctps').val($(this).attr('ufctps'));
                                                                $('#<?php echo $prefixo; ?>-input-select-sexo').val($(this).attr('sexocat'));
                                                                $('#<?php echo $prefixo; ?>-input-select-estado-civil').val($(this).attr('estadocivil'));
                                                                $('#<?= $prefixo ?>-input-numero-rg').val($(this).attr('numerorg'));
                                                                $('#<?= $prefixo ?>-input-numero-pis').val($(this).attr('numeropis'));

                                                                $('#<?= $prefixo ?>-codigo').val($(this).attr('codigo'));
                                                                $('#<?= $prefixo ?>-nome').val($(this).attr('nome'));
                                                                $('#<?= $prefixo ?>-nome-mae').val($(this).attr('nomemae'));
                                                                $('#<?= $prefixo ?>-data-nascimento').val($(this).attr('datanascimento'));
                                                                $('#<?= $prefixo ?>-numero-ctps').val($(this).attr('numeroctps'));
                                                                $('#<?= $prefixo ?>-serie-ctps').val($(this).attr('seriectps'));
                                                                $('#<?php echo $prefixo; ?>-select-uf-ctps').val($(this).attr('ufctps'));
                                                                $('#<?php echo $prefixo; ?>-select-sexo').val($(this).attr('sexocat'));
                                                                $('#<?php echo $prefixo; ?>-select-estado-civil').val($(this).attr('estadocivil'));
                                                                $('#<?= $prefixo ?>-numero-rg').val($(this).attr('numerorg'));
                                                                $('#<?= $prefixo ?>-numero-pis').val($(this).attr('numeropis'));

                                                                $('#div-modal').modal('toggle');
                                                            });
                                                        });
                                                    });
    }
</script>