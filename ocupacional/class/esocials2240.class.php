<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("funcionario.class.php");
require_once("empresa.class.php");
require_once("criptografia.class.php");
require_once("funcoes.class.php");
require_once("firebird.class.php");

class esocials2240 implements iface
{
    private $funcionario = "";
    private $sequencial  = "";
    private $datahora    = "";
    private $xml         = "";
    private $situacao    = "";
    private $situacao_transmissao    = "";
    private $transmite_esocial    = "";
    private $chave    = "";

    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    private $numerorecibo    = "";

    function getFuncionario()
    {
        return $this->funcionario;
    }
    function getSequencial()
    {
        return $this->sequencial;
    }
    function getDataHora()
    {
        return $this->datahora;
    }
    function getXML()
    {
        return $this->xml;
    }
    function getSituacao()
    {
        return $this->situacao;
    }
    function getSituacaoTransmissao()
    {
        return $this->situacao_transmissao;
    }
    function getTransmiteESocial()
    {
        return $this->transmite_esocial;
    }
    function getChave()
    {
        return $this->chave;
    }
    function getNumeroRecibo()
    {
        return $this->numerorecibo;
    }

    function setFuncionario($valor)
    {
        $this->funcionario = $valor;
    }
    function setSequencial($valor)
    {
        $this->sequencial = $valor;
    }
    function setDataHora($valor)
    {
        $this->datahora = $valor;
    }
    function setXML($valor)
    {
        $this->xml = $valor;
    }
    function setSituacao($valor)
    {
        $this->situacao = $valor;
    }
    function setSituacaoTransmissao($valor)
    {
        $this->situacao_transmissao = $valor;
    }
    function setTransmiteESocial($valor)
    {
        $this->transmite_esocial = $valor;
    }
    function setChave($valor)
    {
        $this->chave = $valor;
    }
    function setNumeroRecibo($valor)
    {
        $this->numerorecibo = $valor;
    }

    function getLista()
    {
        return $this->lista;
    }
    function getTotalLista()
    {
        return $this->totalLista;
    }
    function getTotalRegistros()
    {
        return $this->totalRegistros;
    }
    function getItemLista($index)
    {
        if (isset($this->lista[$index]))
            return $this->lista[$index];
        else
            return new esocials2240();
    }

    function buscar($filtro = null, $limite = null, $ordem = null)
    {
        $select = array(
            "A.UNIDADE", "A.EMPRESA", "A.MATRICULA", "B.NOME AS NOMEFUNCIONARIO",
            "A.SEQUENCIAL", "A.DATAHORA", "A.XML", "A.SITUACAO", "C.SITUACAO AS SIT_TRANSMISSAO",
            "E.TRANSMISSAOESOCIAL", "A.CHAVE", "C.RETORNORECIBO"
        );
        $join   = "JOIN T_FUNCIONARIO B ON B.MATRICULA = A.MATRICULA "
            . "                    AND B.EMPRESA   = A.EMPRESA   "
            . "                    AND B.UNIDADE   = A.UNIDADE   "
            . "LEFT OUTER JOIN T_ESOCIAL C ON C.EVENTO     = 'S2240' "
            . "                 AND C.CHAVE     = A.CHAVE   "
            . "                 AND C.AMBIENTE  = A.AMBIENTE   "
            . "                 AND C.EMPRESA   = A.EMPRESA   "
            . "                 AND C.UNIDADE   = A.UNIDADE   "
            . "JOIN T_EMPRESA E ON E.CODIGO    = A.EMPRESA   "
            . "                AND E.UNIDADE   = A.UNIDADE   ";


        $fb   = new FB("T_ESOCIAL_S2240 A");
        $ret = $fb->select($filtro, null, null, $join, $select);
        $this->totalRegistros = count($ret);

        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);

        for ($i = 0; $i < count($ret); $i++) {
            $e = new esocials2240();

            $f = new funcionario();
            $f->setCodigo($ret[$i]["MATRICULA"]);
            $f->setNome($ret[$i]["NOMEFUNCIONARIO"]);
            $e->setFuncionario($f);

            $e->setSequencial($ret[$i]["SEQUENCIAL"]);
            $e->setDataHora($ret[$i]["DATAHORA"]);
            //$e->setXML($ret[$i]["XML"]);
            $e->setSituacao($ret[$i]["SITUACAO"]);
            $e->setSituacaoTransmissao($ret[$i]["SIT_TRANSMISSAO"]);
            $e->setTransmiteESocial($ret[$i]["TRANSMISSAOESOCIAL"]);
            $e->setChave($ret[$i]["CHAVE"]);
            $e->setNumeroRecibo($ret[$i]["RETORNORECIBO"]);

            $blob_data = ibase_blob_info($ret[$i]["XML"]);
            $blob_hndl = ibase_blob_open($ret[$i]["XML"]);
            $e->setXML(ibase_blob_get($blob_hndl, $blob_data[0]));

            array_push($this->lista, $e);
        }

        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }

    function remover()
    {
    }
    function alterar()
    {
    }
    function inserir()
    {
    }

    function gerarXML($unidade, $empresa, $matricula, $sequencial, $usuario, $xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $evtExpRisco = $dom->getElementsByTagName("evtExpRisco");
        $id = $evtExpRisco->item(0)->attributes->item(0)->nodeValue;

        if (!empty($id)) {
            $fb = new FB("T_ESOCIAL_S2240");
            $fb->update(
                ["SITUACAO" => "B", "DATAHORADOWNLOAD" => date("Y-m-d H:i:s", time()), "USUARIODOWNLOAD" => $usuario],
                ["UNIDADE" => $unidade, "EMPRESA" => $empresa, "MATRICULA" => $matricula, "SEQUENCIAL" => $sequencial]
            );
        }

        $emp = new empresa();
        $emp->buscar(array("A.UNIDADE" => $unidade, "A.CODIGO" => $empresa));
        $empresa = $emp->getItemLista(0);

        $nome = $id . '-S2240.xml';
        if ($empresa->getModeloArquivoDownload() == 1) {
            // 9901_S-2210_9901000001S221020191001.xml
            $filial = $empresa->getFilialProtheus();
            $fb = new FB("T_ESOCIAL_S2240");
            $sequencia = str_pad($fb->proximoCodigoGeral($unidade, "DWNESO"), 6, "0", STR_PAD_LEFT);
            $nome = $filial . '_S-2240_' . $filial . $sequencia . 'S2240' . date("Ymd", time()) . '.xml';
        }

        header('Content-disposition: attachment; filename="' . $nome . '"');

        header('Content-type: "text/xml"; charset="utf8"');

        $xmlRet = funcoes::assinar(
            $empresa->getArquivoCertificadoDigital(),
            criptografia::deCriptografia($empresa->getSenhaCertificadoDigital()),
            $xml,
            'eSocial',
            '',
            OPENSSL_ALGO_SHA256,
            [true, false, null, null]
        );

        echo $xmlRet;
    }
}
