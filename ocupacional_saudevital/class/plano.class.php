<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("tipoplano.class.php");

class plano implements iface {
    private $codigo    = "";
    private $descricao = "";
    private $tipo      = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getCodigo(){ return $this->codigo; }
    function getDescricao(){ return $this->descricao; }
    function getTipo(){ return $this->tipo; }
    
    function setCodigo($valor){ $this->codigo = $valor; }
    function setDescricao($valor){ $this->descricao = $valor; }
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
            return new plano();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        
    }
    
    function remover(){
        
    }
    function alterar(){
        
    }
    function inserir(){
        
    }
}
