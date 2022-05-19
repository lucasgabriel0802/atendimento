<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");

class empresaemail implements iface {
    private $unidade = "";
    private $empresa = "";
    private $item    = "";
    private $email   = "";
    private $tipo    = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getItem(){ return $this->item; }
    function getEmail(){ return $this->email; }
    function getTipo(){ return $this->tipo; }
    
    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }    
    function setItem($valor){ $this->item = $valor; }    
    function setEmail($valor){ $this->email = $valor; }
    function setTipo($valor){ $this->tipo = $valor; }
    
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
            return new empresaemail();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.ITEM", "A.EMAIL", "A.TIPO_EMAIL");
        
        $fb = new FB("T_EMP_EMAIL A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $ee = new empresaemail();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $ee->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $ee->setEmpresa($em);
            
            $ee->setItem($ret[$i]["ITEM"]);
            $ee->setEmail($ret[$i]["EMAIL"]);
            $ee->setTipo($ret[$i]["TIPO_EMAIL"]);

            array_push($this->lista, $ee);
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
