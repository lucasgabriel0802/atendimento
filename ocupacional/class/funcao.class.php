<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");

class funcao implements iface {
    private $codigo = "";
    private $nome   = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getCodigo(){ return $this->codigo; }
    function getNome(){ return $this->nome; }
    
    function setCodigo($valor){ $this->codigo = $valor; }
    function setNome($valor){ $this->nome = $valor; }
    
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
            return new funcao();
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
