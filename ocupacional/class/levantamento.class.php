<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class levantamento implements iface {
    private $data          = "";
    private $identificacao = "";
    private $status        = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getData(){ return $this->data; }
    function getIdentificacao(){ return $this->identificacao; }
    function getStatus(){ return $this->status; }
    
    function setData($valor){ $this->data = $valor; }
    function setIdentificacao($valor){ $this->identificacao = $valor; }
    function setStatus($valor){ $this->status = $valor; }
    
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
            return new levantamento();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.DATA", "A.IDENTIFICACAO", "A.STATUS");
        
        $fb   = new FB("T_LEVANTAMENTO A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $l = new levantamento();
            $l->setData($ret[$i]["DATA"]);
            $l->setIdentificacao($ret[$i]["IDENTIFICACAO"]);
            $l->setStatus($ret[$i]["STATUS"]);
            
            array_push($this->lista, $l);
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
