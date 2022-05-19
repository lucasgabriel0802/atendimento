<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");

class exameweb implements iface {
    private $unidade       = "";
    private $admissional   = "N";
    private $periodico     = "N";
    private $demissional   = "N";
    private $mudancafuncao = "N";
    private $retornotrabalho  = "N";
    private $consultaclinica  = "N";
    private $indefinido       = "N";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getUnidade(){ return $this->unidade; }
    function getAdmissional(){ return $this->admissional; }
    function getPeriodico(){ return $this->periodico; }
    function getDemissional(){ return $this->demissional; }
    function getMudancaFuncao(){ return $this->mudancafuncao; }
    function getRetornoTrabalho(){ return $this->retornotrabalho; }
    function getConsultaClinica(){ return $this->consultaclinica; }
    function getIndefinido(){ return $this->indefinido; }

    function permiteAdmissional(){ return $this->admissional == "S"; }
    function permitePeriodico(){ return $this->periodico == "S"; }
    function permiteDemissional(){ return $this->demissional == "S"; }
    function permiteMudancaFuncao(){ return $this->mudancafuncao == "S"; }
    function permiteRetornoTrabalho(){ return $this->retornotrabalho == "S"; }
    function permiteConsultaClinica(){ return $this->consultaclinica == "S"; }
    function permiteIndefinido(){ return $this->indefinido == "S"; }    
    
    function setUnidade($valor){ $this->unidade = $valor; }
    function setAdmissional($valor){ $this->admissional = $valor; }
    function setPeriodico($valor){ $this->periodico = $valor; }
    function setDemissional($valor){ $this->demissional = $valor; }
    function setMudancaFuncao($valor){ $this->mudancafuncao = $valor; }
    function setRetornoTrabalho($valor){ $this->retornotrabalho = $valor; }
    function setConsultaClinica($valor){ $this->consultaclinica = $valor; }
    function setIndefinido($valor){ $this->indefinido = $valor; }
    
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
            return new exameweb();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $fb = new FB("T_EXAMEWEB A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem);
        for ($i = 0; $i < count($ret); $i++){
            $e = new exameweb();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $e->setUnidade($un);
            
            $e->setAdmissional($ret[$i]["ADMISSIONAL"]);
            $e->setPeriodico($ret[$i]["PERIODICO"]);
            $e->setDemissional($ret[$i]["DEMISSIONAL"]);
            $e->setMudancaFuncao($ret[$i]["MUDANCAFUNCAO"]);
            $e->setRetornoTrabalho($ret[$i]["RETORNOTRABALHO"]);
            $e->setConsultaClinica($ret[$i]["CONSULTACLINICA"]);
            $e->setIndefinido($ret[$i]["INDEFINIDO"]);

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
