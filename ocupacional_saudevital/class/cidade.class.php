<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class cidade implements iface {
    private $id    = "";
    private $idUF = "";
    private $nome = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getId(){ return $this->id; }
    function getIdUF(){ return $this->idUF; }
    function getNome(){ return $this->nome; }
    
    function setId($valor){ $this->id = $valor; }
    function setIdUF($valor){ $this->idUF = $valor; }
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
            return new tipoacidente();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.ID", "A.IDUF", "UPPER(A.NOME) AS NOME");
        $fb   = new FB("T_MUNICIPIO A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);

        for ($i = 0; $i < count($ret); $i++){
            $item = new cidade();
            $item->setId($ret[$i]["ID"]);
            $item->setIdUF($ret[$i]["IDUF"]);
            $item->setNome($ret[$i]["NOME"]);
            
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
