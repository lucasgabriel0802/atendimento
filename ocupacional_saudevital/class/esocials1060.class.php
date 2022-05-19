<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("postotrabalho.class.php");
require_once("setor.class.php");
require_once("empresa.class.php");
require_once("criptografia.class.php");
require_once("funcoes.class.php");
require_once("firebird.class.php");

class esocials1060 implements iface {
    private $postoTrabalho = "";
    private $setor         = "";
    private $sequencial    = "";
    private $datahora      = "";
    private $xml           = "";
    private $situacao      = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getPostoTrabalho(){ return $this->postoTrabalho; }
    function getSetor(){ return $this->setor; }
    function getSequencial(){ return $this->sequencial; }
    function getDataHora(){ return $this->datahora; }
    function getXML(){ return $this->xml; }
    function getSituacao(){ return $this->situacao; }
    
    function setPostoTrabalho($valor){ $this->postoTrabalho = $valor; }
    function setSetor($valor){ $this->setor = $valor; }
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
            return new esocials1060();
    }  

    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.POSTOTRABALHO", "B.DESCRICAO AS DESCPOSTO",
                        "A.SETOR", "C.NOME AS NOMESETOR", "A.SEQUENCIAL", "A.DATAHORA", "A.XML",
                        "A.SITUACAO");
        
        $join = " JOIN T_EMPPOSTOTRABALHO B ON B.UNIDADE       = A.UNIDADE       "
              . "                          AND B.EMPRESA       = A.EMPRESA       "
              . "                          AND B.CODIGO        = A.POSTOTRABALHO "
              . " JOIN T_SETOR            C ON C.UNIDADE       = A.UNIDADE       "
              . "                          AND C.EMPRESA       = A.EMPRESA       "
              . "                          AND C.CODIGO        = A.SETOR         "
              . "                          AND C.POSTOTRABALHO = A.POSTOTRABALHO ";

        $fb = new FB("T_ESOCIAL_S1060 A");
        $ret = $fb->select($filtro, null, $ordem, $join, $select);        
        $this->totalRegistros = count($ret);

        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);

        for ($i = 0; $i < count($ret); $i++){
            $e = new esocials1060();
            
            $p = new postotrabalho();
            $p->setCodigo($ret[$i]["POSTOTRABALHO"]);
            $p->setDescricao($ret[$i]["DESCPOSTO"]);
            $e->setPostoTrabalho($p);
            
            $s = new setor();
            $s->setCodigo($ret[$i]["SETOR"]);
            $s->setNome($ret[$i]["NOMESETOR"]);
            $e->setSetor($s);

            $e->setSequencial($ret[$i]["SEQUENCIAL"]);
            $e->setDataHora($ret[$i]["DATAHORA"]);
//            $e->setXML($ret[$i]["XML"]);
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
    
    function gerarXML($unidade, $empresa, $posto, $setor, $sequencial, $usuario, $xml) {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $evtTabAmbiente = $dom->getElementsByTagName("evtTabAmbiente");
        $id = $evtTabAmbiente->item(0)->attributes->item(0)->nodeValue;

        if (!empty($id)) {
            $fb = new FB("T_ESOCIAL_S1060");
            $fb->update(["SITUACAO" => "B", "DATAHORADOWNLOAD" => date("Y-m-d H:i:s", time()), "USUARIODOWNLOAD" => $usuario],
                        ["UNIDADE" => $unidade, "EMPRESA" => $empresa, "POSTOTRABALHO" => $posto, "SETOR" => $setor, "SEQUENCIAL" => $sequencial]);
        }

        $emp = new empresa();
        $emp->buscar(array("A.UNIDADE" => $unidade, "A.CODIGO" => $empresa));
        $empresa = $emp->getItemLista(0);

        header('Content-disposition: attachment; filename="' . $id . '-S1060.xml"');
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
