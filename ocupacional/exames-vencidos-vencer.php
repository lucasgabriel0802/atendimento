<?php
require_once("class/config.inc.php");
require_once("class/agenda.class.php");
require_once("class/exameweb.class.php");
require_once("class/funcexamesacomp.class.php");
require_once("class/funcionario.class.php");
require_once("class/funcoes.class.php");
require_once("class/tabela.class.php");
require_once("class/funcaso.class.php");
require_once("class/convocacao.class.php");

config::verificaLogin();

if (isset($_POST["a"])) {
    $acao = $_POST["a"];

    if ($acao == "pesquisar") {
        $retorno = array("codigo" => 0, "mensagem" => "");
        if (isset($_POST["data"])) {
            if (funcoes::validarData($_POST["data"])) {

                $dataRef  = funcoes::converterData($_POST["data"]);
                $convoc = new convocacao();
                if ($convoc->convocar($dataRef)) {
                    $tab = new tabela(array("class" => "table table-bordered\" id=\"datatable\" " )); 
                    $cab = new cabecalho(array("class" => "no-border"));
                    $cab->addItem("Funcionário", array(
                        "style" => "font-weight: bold;",
                        "class" => "sorting_asc\"  tabindex=\"0\" aria-controls=\"datatable\" aria-sort=\"ascending\" aria-label=\"Rendering engine: activate to sort column descending\" "
                    ));

                    $cab->addItem("Exame", array("style" => "font-weight: bold"));
                    $cab->addItem("Data Última Realização", array("style" => "font-weight: bold"));
                    $cab->addItem("Data Próxima Realização", array("style" => "font-weight: bold"));
                    $cab->addItem("Validade (meses)", array("style" => "font-weight: bold"));
                    $cab->addItem("Tipo", array("style" => "font-weight: bold"));
                    $tab->addCabecalho($cab);
                    $nomeAnterior = '';

                    for ($i = 0; $i < $convoc->getTotalLista(); $i++) {
                        $reg = new registro();

                        $date = new DateTime($convoc->getItemLista($i)->getDataProximaRealizacao());
                        $hoje = new DateTime('Now');
                        $font_red = " " ;
                        if ($date < $hoje){
                            $font_red = " ; color:red; ";
                        }

                        if ($convoc->getItemLista($i)->getFuncionario()->getNome() != $nomeAnterior)
                            $reg->addItem(utf8_encode($convoc->getItemLista($i)->getFuncionario()->getNome()), array("style" => "font-weight: bold ".$font_red));
                        else
                            $reg->addItem('');
                        $reg->addItem(utf8_encode($convoc->getItemLista($i)->getExame()->getDescricao()),array("style" => $font_red));

                        $date = new DateTime($convoc->getItemLista($i)->getDataUltimaRealizacao());
                        $reg->addItem($date->format('d/m/Y'), array("style" => "text-align: center". $font_red));

                        $date = new DateTime($convoc->getItemLista($i)->getDataProximaRealizacao());
                        $reg->addItem($date->format('d/m/Y'), array("style" => "text-align: center". $font_red));

                        $reg->addItem(($convoc->getItemLista($i)->getValidade()), array("style" => "text-align: center".$font_red));
                        $reg->addItem(($convoc->getItemLista($i)->getCaracteristica()), array("style" => "text-align: center".$font_red));
                        $tab->addRegistro($reg);

                        $nomeAnterior = $convoc->getItemLista($i)->getFuncionario()->getNome();
                    }
                    echo $tab->gerar();
                } else
                    echo "<h4>Nenhum resultado foi encontrado!</h4>";
            } else {
                $retorno["codigo"] = 5;
                $retorno["mensagem"] = "Selecione uma data válida!";
            }
        } else {
            if (!isset($_POST["data"]))
                $retorno["codigo"] = 1;
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
        <li class="active">Exames vencidos ou à vencer</li>
    </ol>
</div>
<div class="cl-mcont">
    <div class="block-flat">
        <div class="form-group-sm">
            <div class="col-sm-2" style="width: 135px; height: 58px;">
                <label>Data Referência:</label>
                <input type="text" id="form-input-data" readonly class="form-control input-sm" style="background-color: #FFF; " value="<?php $data = date("d/m/Y");
                                                                                                                                        echo date("d/m/Y", strtotime("+30 days")); ?>">
            </div>
            <div class="col-sm-4" style="height: 58px; padding-top: 25px;">
                <button type="button" class="btn btn-primary" id="form-button-pesquisar-exames">
                    Pesquisar
                </button>
            </div>
            <div class="clearfix"></div>
        </div>
        <br>
        <div>
            <div class="content">
                <div class="table-responsive">
                        
                        <div class="col-md-12" id="div-exames"></div>
                        <div class="clearfix"></div>
                        
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('#form-input-data').datepicker();
                $('#form-input-data').mask('99/99/9999');
            });

            $('#form-button-pesquisar-exames').on('click', function() {
                $('#div-exames').html('<center><div style="top: 30px; position: inherit;"><img src="img/loader2.gif"></div></center>');
                $('#div-exames').load('exames-vencidos-vencer.php', {
                    'a': 'pesquisar',
                    'data': $('#form-input-data').val()
                } /*,function() {
                    $('#datatable').DataTable({
                        'language': {
                            'url': '//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese.json',
                        },
                    });
                }*/);
            })
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                App.init();
            });
        </script>