<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("plano.class.php");
require_once("tipoplano.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");
require_once("postotrabalho.class.php");

class empresaplano implements iface {
    private $unidade       = "";
    private $empresa       = "";
    private $postotrabalho = "";
    private $plano         = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getPostoTrabalho(){ return $this->postotrabalho; }
    function getPlano(){ return $this->plano; }
    
    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setPostoTrabalho($valor){ $this->postotrabalho = $valor; }
    function setPlano($valor){ $this->plano = $valor; }
    
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
        $select = array("A.UNIDADE", "A.EMPRESA", "A.POSTOTRABALHO", "A.PLANO", 
                        "UPPER(B.DESCRICAO) AS DESCPLANO", "B.TIPOPLANO", 
                        "UPPER(C.DESCRICAO) AS DESCTIPO", "C.COBERTURA",
                        "UPPER(D.DESCRICAO) AS DESCPOSTO");
        $join   = "JOIN T_PLANO            B ON B.CODIGO  = A.PLANO     "
                . "JOIN T_TIPOPLANO        C ON C.CODIGO  = B.TIPOPLANO "
                . "JOIN T_EMPPOSTOTRABALHO D ON D.UNIDADE = A.UNIDADE   "
                . "                         AND D.EMPRESA = A.EMPRESA   "
                . "                         AND D.CODIGO  = A.POSTOTRABALHO";
        
        $fb  = new FB("T_EMPPLANO A");
        $ret = $fb->select($filtro, null, null, $join);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $ep = new empresaplano();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $ep->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $ep->setEmpresa($em);
            
            $pt = new postotrabalho();
            $pt->setCodigo($ret[$i]["POSTOTRABALHO"]);
            $pt->setDescricao($ret[$i]["DESCPOSTO"]);
            $ep->setPostoTrabalho($pt);
            
            $pl = new plano();
            $pl->setCodigo($ret[$i]["PLANO"]);
            $pl->setDescricao($ret[$i]["DESCPLANO"]);
            
            $tp = new tipoplano();
            $tp->setCodigo($ret[$i]["TIPOPLANO"]);
            $tp->setDescricao($ret[$i]["DESCTIPO"]);
            $tp->setCobertura($ret[$i]["COBERTURA"]);
            $pl->setTipo($tp);
            
            $ep->setPlano($pl);
            
            array_push($this->lista, $ep);
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
