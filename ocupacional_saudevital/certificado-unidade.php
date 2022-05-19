<?php
require_once("class/config.inc.php");
require_once("class/unidade.class.php");
require_once("class/parametrosgerais.class.php");
require_once("class/criptografia.class.php");
require_once("class/funcoes.class.php");
require_once("class/tabela.class.php");

config::verificaLogin();

if ($_SESSION[config::getSessao()]["esocial"] != "S") {
    header("Location: 403.php");
    die;
}

$unidade = new unidade();
$unidade->buscar(array(    
    "A.CODIGO" => $_SESSION[config::getSessao()]["unidade"]
));
$unidade = $unidade->getItemLista(0);

$parametro = new parametrosgerais();
$parametro->buscar();
$parametro = $parametro->getItemLista(0);

if (isset($_POST["a"])) {
    $acao = $_POST["a"];
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

                            if (!$unidade->salvarCertificadoDigital($_SESSION[config::getSessao()]["unidade"], $_SESSION[config::getSessao()]["empresa_ativa"], $certificado, $senha, $validade)) {
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
    }
}

?>

<div class="page-head">
    <h2>e-Social</h2>
    <ol class="breadcrumb">
        <li>Início</li>
        <li class="active">Certificado unidade e-Social</li>
    </ol>
</div>

<div class="block-flat">
    Certificado digital valido até <strong><?php echo funcoes::converterData($unidade->getValidadeCertificadoDigital()) ?></strong>
    <hr />
    Selecione o arquivo: <input type="file" name="arquivo" id="arquivoCertificadoDigital" style="display: initial;">&nbsp;&nbsp;&nbsp;&nbsp;
    Informe a senha: <input type="password" name="senha" id="senhaCertificadoDigital">
    <button type="button" class="btn btn-primary" onclick="atualizarCertificadoDigital()">Atualizar</button>
    <hr />

</div>
<script>
    $(document).ready(function() {
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
                url: 'certificado-unidade.php',
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


</script>