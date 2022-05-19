<?php
require_once("class/config.inc.php");
require_once("class/funcionario.class.php");
require_once("class/tabela.class.php");
require_once("class/funcoes.class.php");

config::verificaLogin();

if (isset($_POST["a"])) {
    $acao = $_POST["a"];

    if ($acao == "pesquisa") {

        $pagina = 1;
        $nome   = "";

        if (isset($_POST["nome"]))
            $nome = $_POST["nome"];

        if (isset($_POST["pagina"]))
            if (is_numeric($_POST["pagina"]))
                $pagina = $_POST["pagina"];

        $pagina--; // inicia com 0
        $limite = array(config::getLimitePagina(), ($pagina * config::getLimitePagina()));
        $totalRegistros = 0;

        $filtro = array(
            "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
            "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
            //"A.SITUACAO" => "ATIVO",
            "A.DATADEMISSAO" => array("NULL", "IS")
        );

        if ($nome != "")
            $filtro["A.NOME"] = array($nome, "CONTAINING");

        $fu = new funcionario();
        if ($fu->buscar($filtro, $limite, array("A.NOME" => "ASC"))) {
            $totalRegistros = $fu->getTotalRegistros();

            $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
            $cab = new cabecalho(array("class" => "no-border"));
            $cab->addItem("Código", array("style" => "width: 65px; font-weight: bold"));
            $cab->addItem("Nome", array("style" => "font-weight: bold"));
            $cab->addItem("", array("style" => "width: 100px"));
            $tab->addCabecalho($cab);

            for ($i = 0; $i < $fu->getTotalLista(); $i++) {
                $reg = new registro(array("class" => "no-border-y"));
                $reg->addItem($fu->getItemLista($i)->getCodigo());
                $reg->addItem(utf8_encode($fu->getItemLista($i)->getNome()));
                $admissao = date("d/m/Y", strtotime($fu->getItemLista($i)->getDataAdmissao()));

                $reg->addItem("<div class=\"btn-group\">" .
                    //"<button type=\"button\" class=\"btn btn-default\">Ações</button>".
                    "<button type=\"button\" data-toggle=\"dropdown\" class=\"btn btn-primary dropdown-toggle\" aria-expanded=\"false\"><span class=\"caret\"></span><span class=\"sr-only\">Toggle Dropdown</span>Ações</button>" .
                    "<ul role=\"menu\" class=\"dropdown-menu\">" .
                    "<li style:padding:10px; ><a style=\"color:White\" class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" acao=\"demitir\" codigo=\"{$fu->getItemLista($i)->getCodigo()}\"><i class=\"fa fa-ban\"></i>Demitir</a></li>" .
                    "<li padding=5px><a style=\"color:White\" class=\"btn btn-danger btn-xs\" href=\"javascript: void(0)\" acao=\"excluir\" codigo=\"{$fu->getItemLista($i)->getCodigo()}\"><i class=\"fa fa-times\"></i>Excluir</a></li>" .
                    //   "<li padding=5px><a style=\"color:White\" class=\"btn btn-info btn-xs\" href=\"javascript: void(0)\" acao=\"editar\" codigo=\"{$fu->getItemLista($i)->getCodigo()}\" nome=\"{$fu->getItemLista($i)->getNome()} \"><i class=\"fa fa-pencil\"></i>Editar</a></li>".
                    "<li padding=5px><a style=\"color:White\" class=\"btn btn-info btn-xs\" href=\"javascript: void(0)\" acao=\"editar\" " .
                    " codigo=\"{$fu->getItemLista($i)->getCodigo()}\" " .
                    " nome=\"{$fu->getItemLista($i)->getNome()} \"  " .
                    " cpf=\"{$fu->getItemLista($i)->getCPF()} \"  " .
                    " matricularh=\"{$fu->getItemLista($i)->getMatriculaESocial()} \"  " .
                    " data-admissao=\"{$admissao} \"  " .
                    "><i class=\"fa fa-pencil\"></i>Editar</a></li>" .

                    "</ul>" .
                    "</div>");

                // $reg->addItem("<a class=\"btn btn-primary btn-xs\" href=\"javascript: void(0)\" acao=\"demitir\" codigo=\"{$fu->getItemLista($i)->getCodigo()}\"><i class=\"fa fa-ban\"></i></a>".
                //               "<a class=\"btn btn-danger btn-xs\" href=\"javascript: void(0)\" acao=\"excluir\" codigo=\"{$fu->getItemLista($i)->getCodigo()}\"><i class=\"fa fa-times\"></i></a>".
                //               "<a class=\"btn btn-info btn-xs\" href=\"javascript: void(0)\" acao=\"editar\" codigo=\"{$fu->getItemLista($i)->getCodigo()}\"><i class=\"fa fa-times\"></i></a>"
                //             );
                $tab->addRegistro($reg);
            }
            echo $tab->gerar();
            funcoes::gerarPaginacao(($pagina + 1), $totalRegistros, "pesquisarFuncionario('<pagina>');");
        } else
            echo "<h5>Nenhum resultado encontrado!</h5>";
    } else
            if ($acao == 'demissao') {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_POST["codigo"])) {
            if (is_numeric($_POST["codigo"])) {
                $filtro = array(
                    "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                    "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                    "A.DATADEMISSAO" => array("NULL", "IS"),
                    //"A.SITUACAO" => "ATIVO",
                    "A.MATRICULA" => $_POST["codigo"]
                );

                $fu = new funcionario();
                if ($fu->buscar($filtro, 1)) {
                    if (!$fu->alterar(
                        array("DATADEMISSAO" => date("Y-m-d")),
                        array(
                            "UNIDADE"   => $_SESSION[config::getSessao()]["unidade"],
                            "EMPRESA"   => $_SESSION[config::getSessao()]["empresa_ativa"],
                            "MATRICULA" => $fu->getItemLista(0)->getCodigo()
                        )
                    )) {
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Não foi possível realizar o processo!";
                    }
                } else {
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Funcionário não encontrado!";
                }
            } else {
                $retorno["codigo"] = 2;
                $retorno["mensagem"] = "Selecione um funcionário!";
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        echo json_encode($retorno);
    } else
                if ($acao == "excluir") {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_POST["codigo"])) {
            if (is_numeric($_POST["codigo"])) {
                $filtro = array(
                    "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                    "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                    "A.DATADEMISSAO" => array("NULL", "IS"),
                    //"A.SITUACAO" => "ATIVO",
                    "A.MATRICULA" => $_POST["codigo"]
                );

                $fu = new funcionario();
                if ($fu->buscar($filtro, 1)) {
                    if (!$fu->remover($_SESSION[config::getSessao()]["unidade"], $_SESSION[config::getSessao()]["empresa_ativa"], $fu->getItemLista(0)->getCodigo())) {
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Não foi possível realizar o processo!";
                    }
                } else {
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Funcionário não encontrado!";
                }
            } else {
                $retorno["codigo"] = 2;
                $retorno["mensagem"] = "Selecione um funcionário!";
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        echo json_encode($retorno);
    } else
                if ($acao == "excluir") {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_POST["codigo"])) {
            if (is_numeric($_POST["codigo"])) {
                $filtro = array(
                    "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                    "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                    "A.DATADEMISSAO" => array("NULL", "IS"),
                    "A.SITUACAO" => "ATIVO",
                    "A.MATRICULA" => $_POST["codigo"]
                );

                $fu = new funcionario();
                if ($fu->buscar($filtro, 1)) {
                    if (!$fu->remover($_SESSION[config::getSessao()]["unidade"], $_SESSION[config::getSessao()]["empresa_ativa"], $fu->getItemLista(0)->getCodigo())) {
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Não foi possível realizar o processo!";
                    }
                } else {
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Funcionário não encontrado!";
                }
            } else {
                $retorno["codigo"] = 2;
                $retorno["mensagem"] = "Selecione um funcionário!";
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        echo json_encode($retorno);
    } else
                    if ($acao == 'editar') {
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_POST["codigo"])) {
            if (is_numeric($_POST["codigo"])) {
                $filtro = array(
                    "A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                    "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                    "A.MATRICULA" => $_POST["codigo"]
                );

                $fu = new funcionario();

                $editar = [];
                if (isset($_POST["cpf"]) and ($_POST["cpf"] != '')) {
                    $editar["CPF"] = $_POST["cpf"];
                }

                if (isset($_POST["matricularh"]) and ($_POST["matricularh"] != '')) {
                    $editar["MATRICULAESOCIAL"] = $_POST["matricularh"];
                }

                if (isset($_POST["dataadmissao"]) and ($_POST["dataadmissao"] != '')) {
                    $editar["DATAADMISSAO"] = funcoes::converterData($_POST["dataadmissao"]);
                }


                if ((count($editar) > 0) and ($fu->buscar($filtro, 1))) {
                    if (!$fu->alterar(
                        $editar,
                        array(
                            "UNIDADE"   => $_SESSION[config::getSessao()]["unidade"],
                            "EMPRESA"   => $_SESSION[config::getSessao()]["empresa_ativa"],
                            "MATRICULA" => $fu->getItemLista(0)->getCodigo()
                        )
                    )) {
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Não foi possível realizar o processo!";
                    }
                } else {
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Funcionário não encontrado!";
                }
            } else {
                $retorno["codigo"] = 2;
                $retorno["mensagem"] = "Selecione um funcionário!";
            }
        } else {
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }

        echo json_encode($retorno);
    }

    die;
}
?>
<div class="page-head">
    <h2>Funcionários</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li class="active">Funcionários</li>
    </ol>
</div>
<div class="cl-mcont">
    <div class="block-flat">
        <div class="form-group-sm">
            <div class="col-md-12">
                <label>Nome:</label>
                <div class="input-group">
                    <input type="input" id="modal-input-nome-funcionario" class="form-control input-sm">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" style="height: 30px; padding-top: 4px" id="modal-button-pesquisar">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div id="div-pesquisa-funcionario"></div>
    </div>
</div>
<input type="hidden" id="modal-input-pagina" value="1">
<div id="modal-confirma" tabindex="-1" role="dialog" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close">×</button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="i-circle primary"><i class="fa fa-question"></i></div>
                    <h4>Confirmação</h4>
                    <p></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
                <button type="button" data-dismiss="modal" class="btn btn-success">Proceder</button>
            </div>
        </div>
    </div>
</div>
<div id="modal-editar" tabindex="-1" role="dialog" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close">×</button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="i-circle primary"><i class="fa fa-exclamation"></i></div>
                    <h4>Edição</h4>
                    <p></p>
                </div>

                <div class="form-group-sm">
                    <div class="col-sm-4">
                        <label id="labelcpf">CPF: </label>
                        <input type="text" class="form-control input-sm" id="cadastro-input-cpf">
                    </div>
                    <div class="col-sm-4">
                        <label>Matricula RH: </label>
                        <input type="text" class="form-control input-sm" id="cadastro-input-matricularh">
                    </div>
                    <div class="col-sm-4">
                        <label>Data Admissão: </label>
                        <input type="text" class="form-control input-sm" id="cadastro-input-data-admissao">
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default">Cancelar</button>
                <button type="button" data-dismiss="modal" class="btn btn-success">Proceder</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#modal-button-pesquisar').on('click', function() {
        pesquisarFuncionario('1');
    });

    $('#modal-button-adicionar').on('click', function() {
        $('#div-modal').modal('toggle');
        $('#div-modal-cadastro').modal();
        $('#div-modal-cadastro .modal-header h3').text('Cadastro de funcionário');
        $('#div-modal-cadastro .modal-body').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-modal-cadastro .modal-body').load('funcionario-cadastro.php');
    });
    $('#modal-input-nome-funcionario').keypress(function(event) {
        if (event.which == 13)
            $('#modal-button-pesquisar').click();
    });

    $('document').ready(function() {
        $('#modal-button-pesquisar').click();
        $('#cadastro-input-cpf').mask('999.999.999-99');
        $('#cadastro-input-data-admissao').datepicker({
            autoclose: true
        });
    });

    function demitirFuncionario(codigo) {
        $.ajax({
            url: 'funcionarios.php',
            type: 'post',
            dataType: 'json',
            data: {
                a: 'demissao',
                codigo: codigo
            },
            cache: false,
            async: true,
            success: function(data) {
                if (data.codigo > 0) {
                    $.gritter.add({
                        title: 'Ops!',
                        text: data.mensagem + ' #' + data.codigo,
                        class_name: 'danger'
                    });
                } else {
                    pesquisarFuncionario('1');
                }
            },
            error: function() {
                $.gritter.add({
                    title: 'Ops!',
                    text: 'Não foi possível realizar a operação!',
                    class_name: 'danger'
                });
            }
        });
    }

    function excluirFuncionario(codigo) {
        $.ajax({
            url: 'funcionarios.php',
            type: 'post',
            dataType: 'json',
            data: {
                a: 'excluir',
                codigo: codigo
            },
            cache: false,
            async: true,
            success: function(data) {
                if (data.codigo > 0) {
                    $.gritter.add({
                        title: 'Ops!',
                        text: data.mensagem + ' #' + data.codigo,
                        class_name: 'danger'
                    });
                } else {
                    pesquisarFuncionario('1');
                }
            },
            error: function() {
                $.gritter.add({
                    title: 'Ops!',
                    text: 'Não foi possível realizar a operação!',
                    class_name: 'danger'
                });
            }
        });
    }

    function editarFuncionario(codigo) {
        $.ajax({
            url: 'funcionarios.php',
            type: 'post',
            dataType: 'json',
            data: {
                a: 'editar',
                codigo: codigo,
                cpf: $('#cadastro-input-cpf').val(),
                matricularh: $('#cadastro-input-matricularh').val(),
                dataadmissao : $('#cadastro-input-data-admissao').val()
            },
            cache: false,
            async: true,
            success: function(data) {
                if (data.codigo > 0) {
                    $.gritter.add({
                        title: 'Ops!',
                        text: data.mensagem + ' #' + data.codigo,
                        class_name: 'danger'
                    });
                } else {
                    $.gritter.add({
                        title: 'Oba!',
                        text: 'Funcionário atualizado.',
                        class_name: 'success'
                    });
                    $('#cadastro-input-cpf').val('');
                    $('#cadastro-input-matricularh').val('');
                    $('#cadastro-input-data-admissao').val('');
                    pesquisarFuncionario('1');
                }
            },
            error: function() {
                $.gritter.add({
                    title: 'Ops!',
                    text: 'Não foi possível realizar a operação!',
                    class_name: 'danger'
                });
            }
        });
    }

    function pesquisarFuncionario(pagina) {
        $('#div-pesquisa-funcionario').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
        $('#div-pesquisa-funcionario').load('funcionarios.php', {
            'a': 'pesquisa',
            'nome': $('#modal-input-nome-funcionario').val(),
            'pagina': pagina
        }, function() {
            $('#div-pesquisa-funcionario a').each(function() {
                $(this).on('click', function() {
                    if ($(this).attr('acao') == 'demitir') {
                        $('#modal-confirma').modal('show');
                        $('#modal-confirma').find('p').first().text('Tem certeza que deseja demitir esse funcionário?');
                        $('#modal-confirma').find('button[class="btn btn-success"]').first().attr('onclick', 'demitirFuncionario(\'' + $(this).attr('codigo') + '\')');
                    } else
                    if ($(this).attr('acao') == 'excluir') {
                        $('#modal-confirma').modal('show');
                        $('#modal-confirma').find('p').first().text('Tem certeza que deseja excluir esse funcionário?');
                        $('#modal-confirma').find('button[class="btn btn-success"]').first().attr('onclick', 'excluirFuncionario(\'' + $(this).attr('codigo') + '\')');
                    } else
                    if ($(this).attr('acao') == 'editar') {
                        $('#modal-editar').modal('show');
                        $('#modal-editar').find('p').first().text($(this).attr('nome'));
                        $('#modal-editar').find('#cadastro-input-cpf').first().val($(this).attr('cpf'));
                        $('#modal-editar').find('#cadastro-input-matricularh').first().val($(this).attr('matricularh'));
                        $('#modal-editar').find('#cadastro-input-data-admissao').first().val($(this).attr('data-admissao'));
                        $('#modal-editar').find('button[class="btn btn-success"]').first().attr('onclick', 'editarFuncionario(\'' + $(this).attr('codigo') + '\' )');
                    }
                });
            });
        });
    }
</script>