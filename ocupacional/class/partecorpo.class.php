<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class partecorpo implements iface {
    private $codigo    = "";
    private $descricao = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getCodigo(){ return $this->codigo; }
    function getDescricao(){ return $this->descricao; }
    
    function setCodigo($valor){ $this->codigo = $valor; }
    function setDescricao($valor){ $this->descricao = $valor; }
    
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
            return new partecorpo();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.CODIGO", "UPPER(A.DESCRICAO) AS DESCRICAO");
        $fb   = new FB("T_PARTECORPOATINGIDA A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);

        for ($i = 0; $i < count($ret); $i++){
            $item = new partecorpo();
            $item->setCodigo($ret[$i]["CODIGO"]);
            $item->setDescricao($ret[$i]["DESCRICAO"]);
            
            array_push($this->lista, $item);
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
