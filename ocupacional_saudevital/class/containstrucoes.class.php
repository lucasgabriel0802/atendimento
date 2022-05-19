<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");

class containstrucoes implements iface {
    private $unidade   = "";
    private $banco     = "";
    private $conta     = "";
    private $item      = "";
    private $instrucao = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getUnidade(){ return $this->unidade; }
    function getBanco(){ return $this->banco; }
    function getConta(){ return $this->conta; }
    function getItem(){ return $this->item; }
    function getInstrucao(){ return $this->instrucao; }

    function setUnidade($valor){ $this->unidade = $valor; }
    function setBanco($valor){ $this->banco = $valor; }
    function setConta($valor){ $this->conta = $valor; }
    function setItem($valor){ $this->item = $valor; }
    function setInstrucao($valor){ $this->instrucao = $valor; }

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
            return new containstrucoes();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.BANCO", "A.CONTA", "A.ITEM", 
                        "A.INSTRUCAO");
        
        $fb   = new FB("T_CONTAINSTRUC A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $ci = new containstrucoes();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $ci->setUnidade($un);
            
            $ci->setBanco($ret[$i]["BANCO"]);
            $ci->setConta($ret[$i]["CONTA"]);
            $ci->setItem($ret[$i]["ITEM"]);
            $ci->setInstrucao($ret[$i]["INSTRUCAO"]);
            
            array_push($this->lista, $ci);
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
