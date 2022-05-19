<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class exame implements iface {
    private $codigo = "";
    private $descricao = "";
    private $orientacoes = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getCodigo(){ return $this->codigo; }
    function getDescricao(){ return $this->descricao; }
    function getOrientacoes(){ return $this->orientacoes; }
    
    function setCodigo($valor){ $this->codigo = $valor; }
    function setDescricao($valor){ $this->descricao = $valor; }
    function setOrientacoes($valor){ $this->orientacoes = $valor; }    
    
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
            return new exame();
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
