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

class esocials2245 implements iface {
    private $funcionario = "";
    private $sequencial  = "";
    private $datahora    = "";
    private $xml         = "";
    private $situacao    = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getFuncionario(){ return $this->funcionario; }
    function getSequencial(){ return $this->sequencial; }
    function getDataHora(){ return $this->datahora; }
    function getXML(){ return $this->xml; }
    function getSituacao(){ return $this->situacao; }
    
    function setFuncionario($valor){ $this->funcionario = $valor; }
    function setSequencial($valor){ $this->sequencial = $valor; }
    function setDataHora($valor){ $this->datahora = $valor; }
    function setXML($valor){ $this->xml = $valor; }
    function setSituacao($valor){ $this->situacao = $valor; }
    
    function getLista(){
        return $this->lista;
    }
    function getTotalLista(){
        return $this->totalLista;
    }
    function getTotalRegistros(){
        return $this->totalRegistros;
    }
    function getItemLista($index){
        if (isset($this->lista[$index]))
            return $this->lista[$index];
        else
            return new esocials2245();
    }
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.MATRICULA", "B.NOME AS NOMEFUNCIONARIO",
                        "A.SEQUENCIAL", "A.DATAHORA", "A.XML", "A.SITUACAO");
        $join   = "JOIN T_FUNCIONARIO B ON B.MATRICULA = A.MATRICULA "
                . "                    AND B.EMPRESA   = A.EMPRESA   "
                . "                    AND B.UNIDADE   = A.UNIDADE   ";
        
        $fb   = new FB("T_ESOCIAL_S2245 A");
        $ret = $fb->select($filtro, null, null, $join, $select);        
        $this->totalRegistros = count($ret);

        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);

        for ($i = 0; $i < count($ret); $i++){
            $e = new esocials2245();
            
            $f = new funcionario();
            $f->setCodigo($ret[$i]["MATRICULA"]);
            $f->setNome($ret[$i]["NOMEFUNCIONARIO"]);
            $e->setFuncionario($f);
            
            $e->setSequencial($ret[$i]["SEQUENCIAL"]);
            $e->setDataHora($ret[$i]["DATAHORA"]);
            //$e->setXML($ret[$i]["XML"]);
            $e->setSituacao($ret[$i]["SITUACAO"]);
  
            $blob_data = ibase_blob_info($ret[$i]["XML"]);
            $blob_hndl = ibase_blob_open($ret[$i]["XML"]);
            $e->setXML(ibase_blob_get($blob_hndl, $blob_data[0]));
                                
            array_push($this->lista, $e);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function remover(){
        
    }
    function alterar(){
        
    }
    function inserir(){
        
    }
    
    function gerarXML($unidade, $empresa, $matricula, $sequencial, $usuario, $xml) {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $evtTreiCap = $dom->getElementsByTagName("evtTreiCap");
        $id = $evtTreiCap->item(0)->attributes->item(0)->nodeValue;
        
        if (!empty($id)) {
            $fb = new FB("T_ESOCIAL_S2245");
            $fb->update(["SITUACAO" => "B", "DATAHORADOWNLOAD" => date("Y-m-d H:i:s", time()), "USUARIODOWNLOAD" => $usuario],
                        ["UNIDADE" => $unidade, "EMPRESA" => $empresa, "MATRICULA" => $matricula, "SEQUENCIAL" => $sequencial]);
        }
        
        $emp = new empresa();
        $emp->buscar(array("A.UNIDADE" => $unidade, "A.CODIGO" => $empresa));
        $empresa = $emp->getItemLista(0);

        header('Content-disposition: attachment; filename="' . $id . '-S2245.xml"');
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
