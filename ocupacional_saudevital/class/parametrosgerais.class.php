<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class parametrosgerais implements iface {
    private $limiteTituloAberto = 0;
    private $habilitaGeracaoeSocial = false;

    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getLimiteTituloAberto(){ return $this->limiteTituloAberto; }
    function getHabilitaGeracaoeSocial(){ return $this->habilitaGeracaoeSocial; }

    function setLimiteTituloAberto($valor){ $this->limiteTituloAberto = $valor; }
    function setHabilitaGeracaoeSocial($valor){ $this->habilitaGeracaoeSocial = $valor; }
    
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
            return new parametrosgerais();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.CODIGO", "A.QTDETITULOBLOQUEAR", "A.HABILITARGERACAOESOCIAL");

        $fb = new FB("T_PARAMETROSGERAIS A");
        $ret = $fb->select(array("A.CODIGO" => "1"), null, null, null, $select);
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $p = new parametrosgerais();
            $p->setLimiteTituloAberto($ret[$i]["QTDETITULOBLOQUEAR"]);
            $p->setHabilitaGeracaoeSocial($ret[$i]["HABILITARGERACAOESOCIAL"] == "S");
            
            array_push($this->lista, $p);
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
