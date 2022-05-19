<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("cep.class.php");

class medico implements iface {
    private $codigo     = "";
    private $nome       = "";
    private $observacao = "";
    private $endereco   = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getCodigo(){ return $this->codigo; }
    function getNome(){ return $this->nome; }
    function getEndereco(){ return $this->endereco; }
    function getObservacao(){ return $this->observacao; }
    
    function setCodigo($valor){ $this->codigo = $valor; }
    function setNome($valor){ $this->nome = $valor; }
    function setEndereco($valor){ $this->endereco = $valor; }
    function setObservacao($valor){ $this->observacao = $valor; }
    
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
            return new medico();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.CODIGO", "UPPER(A.NOME) AS NOME", "A.ENDERECO",
                        "A.NUMERO", "A.COMPLEMENTO", "A.BAIRRO", "A.CIDADE", 
                        "A.ESTADO", "A.CEP", "A.OBSERVACAO");
        
        $fb  = new FB("T_MEDICO A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $m = new medico();
            $m->setCodigo($ret[$i]["CODIGO"]);
            $m->setNome($ret[$i]["NOME"]);
            
            $blob_info = ibase_blob_info($ret[$i]["OBSERVACAO"]);
            if ($blob_info["length"] > 0){
                $blob_hand = ibase_blob_open($ret[$i]["OBSERVACAO"]);
                $observacao = ibase_blob_get($blob_hand, $blob_info[0]);
                ibase_blob_close($blob_hand);
            }else
                $observacao = "";
            
            $m->setObservacao($observacao);
            
            $c = new cep();
            $c->setEndereco($ret[$i]["ENDERECO"]);
            $c->setNumero($ret[$i]["NUMERO"]);
            $c->setComplemento($ret[$i]["COMPLEMENTO"]);
            $c->setBairro($ret[$i]["BAIRRO"]);
            $c->setCidade($ret[$i]["CIDADE"]);
            $c->setUF($ret[$i]["ESTADO"]);
            
            $m->setEndereco($c);
            
            array_push($this->lista, $m);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function buscarMedicoNaoFono(){
        $select = array("A.CODIGO", "UPPER(A.NOME) AS NOME");
        $join   = "LEFT JOIN T_MEDICOFONO B ON B.MEDICO = A.CODIGO"; // AND B.MEDICO IS NULL
        $where  = array("COALESCE(A.STATUS, 'N')" => "N", "B.MEDICO" => ["NULL", "IS"]);
        
        $fb  = new FB("T_MEDICO A");
        $ret = $fb->select($where, null, null, $join, $select);
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($where, null, null, $join, $select);
        
        for ($i = 0; $i < count($ret); $i++){
            $m = new medico();
            $m->setCodigo($ret[$i]["CODIGO"]);
            $m->setNome($ret[$i]["NOME"]);
            
            array_push($this->lista, $m);
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
}