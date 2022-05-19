<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");

class titreceb implements iface {
    private $unidade               = "";
    private $empresa                = "";
    private $titulo                 = "";
    private $parcela                = "";
    private $dataemissao            = "";
    private $tipotitulo             = "";
    private $datavencimento         = "";
    private $valortitulo            = "";
    private $situacao               = "";
    private $nossonumero            = "";
    private $banco                  = "";
    private $contaboleto            = "";
    private $percmultafixa          = "";
    private $percmoradia            = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getTitulo(){ return $this->titulo; }
    function getParcela(){ return $this->parcela; }
    function getDataEmissao(){ return $this->dataemissao; }
    function getTipoTitulo(){ return $this->tipotitulo; }
    function getDataVencimento(){ return $this->datavencimento; }
    function getValorTitulo(){ return $this->valortitulo; }
    function getSituacao(){ return $this->situacao; }
    function getNossoNumero(){ return $this->nossonumero; }
    function getBanco(){ return $this->banco; }
    function getContaBoleto(){ return $this->contaboleto; }
    function getPercMultaFixa(){ return $this->percmultafixa; }
    function getPercMoraDia(){ return $this->percmoradia; }

    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setTitulo($valor){ $this->titulo = $valor; }
    function setParcela($valor){ $this->parcela = $valor; }
    function setDataEmissao($valor){ $this->dataemissao = $valor; }
    function setTipoTitulo($valor){ $this->tipotitulo = $valor; }
    function setDataVencimento($valor){ $this->datavencimento = $valor; }
    function setValorTitulo($valor){ $this->valortitulo = $valor; }
    function setSituacao($valor){ $this->situacao = $valor; }
    function setNossoNumero($valor){ $this->nossonumero = $valor; }
    function setBanco($valor){ $this->banco = $valor; }
    function setContaBoleto($valor){ $this->contaboleto = $valor; }
    function setPercMultaFixa($valor){ $this->percmultafixa = $valor; }
    function setPercMoraDia($valor){ $this->percmoradia = $valor; }
    
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
            return new titreceb();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.TITULO", "A.PARCELA", 
                        "A.DATAEMISSAO", "A.TIPOTITULO", "A.DATAVENCIMENTO", 
                        "A.VALORTITULO", "A.SITUACAO", "A.NOSSONUMERO",
                        "A.BANCO", "A.CONTABOLETO", "A.PERCMULTAFIXA",
                        "A.PERCMORADIA");
        
        $fb   = new FB("T_TITRECEB A");
        $ret = $fb->select($filtro);        
        if($ret){
            $this->totalRegistros = count($ret);
        }
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < $this->totalRegistros; $i++){
            $ti = new titreceb();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $ti->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $ti->setEmpresa($em);
            
            $ti->setTitulo($ret[$i]["TITULO"]);
            $ti->setParcela($ret[$i]["PARCELA"]);
            $ti->setDataEmissao($ret[$i]["DATAEMISSAO"]);
            $ti->setTipoTitulo($ret[$i]["TIPOTITULO"]);
            $ti->setDataVencimento($ret[$i]["DATAVENCIMENTO"]);
            $ti->setValorTitulo($ret[$i]["VALORTITULO"]);
            $ti->setSituacao($ret[$i]["SITUACAO"]);
            $ti->setNossoNumero($ret[$i]["NOSSONUMERO"]);
            $ti->setBanco($ret[$i]["BANCO"]);
            $ti->setContaBoleto($ret[$i]["CONTABOLETO"]);
            $ti->setPercMultaFixa($ret[$i]["PERCMULTAFIXA"]);
            $ti->setPercMoraDia($ret[$i]["PERCMORADIA"]);
            
            array_push($this->lista, $ti);
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
