<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");
require_once("medico.class.php");

class empresamedico implements iface {
    private $unidade  = "";
    private $empresa  = "";
    private $medico   = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getMedico(){ return $this->medico; }
    
    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setMedico($valor){ $this->medico = $valor; }
    
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
            return new empresamedico();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = "A.UNIDADE, A.EMPRESA, A.MEDICO, B.NOME";
        $join = "JOIN T_MEDICO B ON B.CODIGO = A.MEDICO";
        $fb = new FB("T_EMP_MEDICO_WEB A");
        $ret = $fb->select($filtro, null, null, $join, $select);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $e = new empresamedico();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $e->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $e->setEmpresa($em);
            
            $me = new medico();
            $me->setCodigo($ret[$i]["MEDICO"]);
            $me->setNome($ret[$i]["NOME"]);
            $e->setMedico($me);
            
            array_push($this->lista, $e);
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
