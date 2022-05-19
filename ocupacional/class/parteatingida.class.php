<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class partesatingida implements iface {
    private $unidade    = "";
    private $empresa = "";
    private $numero = "";
    private $codigoparteatingida = "";
    private $codigolateralidade = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    
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
            return new partesatingida();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
    }
    
    function remover(){
        
    }
    function alterar(){
        
    }
    function inserir($val = null){
          
        
    }
}
