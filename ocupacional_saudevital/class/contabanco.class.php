<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");

class contabanco implements iface {
    private $unidade        = "";
    private $banco          = "";
    private $conta          = "";
    private $agencia        = "";
    private $nossonumero    = "";
    private $pracapagamento = "";
    private $codigocedente  = "";
    private $multafixa      = "";
    private $moradia        = "";
    private $localarquivo   = "";
    private $configuracao1  = "";
    private $configuracao2  = "";
    private $diasprotesto   = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getUnidade(){ return $this->unidade; }
    function getBanco(){ return $this->banco; }
    function getConta(){ return $this->conta; }
    function getAgencia(){ return $this->agencia; }
    function getNossoNumero(){ return $this->nossonumero; }
    function getPracaPagamento(){ return $this->pracapagamento; }
    function getCodigoCedente(){ return $this->codigocedente; }
    function getMultaFixa(){ return $this->multafixa; }
    function getMoraDia(){ return $this->moradia; }
    function getLocalArquivo(){ return $this->localarquivo; }
    function getConfiguracao1(){ return $this->configuracao1; }
    function getConfiguracao2(){ return $this->configuracao2; }
    function getDiasProtesto(){ return $this->diasprotesto; }

    function setUnidade($valor){ $this->unidade = $valor; }
    function setBanco($valor){ $this->banco = $valor; }
    function setConta($valor){ $this->conta = $valor; }
    function setAgencia($valor){ $this->agencia = $valor; }
    function setNossoNumero($valor){ $this->nossonumero = $valor; }
    function setPracaPagamento($valor){ $this->pracapagamento = $valor; }
    function setCodigoCedente($valor){ $this->codigocedente = $valor; }
    function setMultaFixa($valor){ $this->multafixa = $valor; }
    function setMoraDia($valor){ $this->moradia = $valor; }
    function setLocalArquivo($valor){ $this->localarquivo = $valor; }
    function setConfiguracao1($valor){ $this->configuracao1 = $valor; }
    function setConfiguracao2($valor){ $this->configuracao2 = $valor; }
    function setDiasProtesto($valor){ $this->diasprotesto = $valor; }
    
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
            return new contabanco();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.BANCO", "A.CONTA", "A.AGENCIA", 
                        "A.NOSSONUMERO", "A.PRACAPAGAMENTO", "A.CODIGOCEDENTE", 
                        "A.MULTAFIXA", "A.MORADIA", "A.LOCALIZACAO_ARQ_CONF", 
                        "A.CONFIGURACAO_1", "A.CONFIGURACAO_2", "A.DIASPROTESTO");
        
        $fb   = new FB("T_CONTABANCO A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $cb = new contabanco();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $cb->setUnidade($un);
            
            $cb->setBanco($ret[$i]["BANCO"]);
            $cb->setConta($ret[$i]["CONTA"]);
            $cb->setAgencia($ret[$i]["AGENCIA"]);
            $cb->setNossoNumero($ret[$i]["NOSSONUMERO"]);
            $cb->setPracaPagamento($ret[$i]["PRACAPAGAMENTO"]);
            $cb->setCodigoCedente($ret[$i]["CODIGOCEDENTE"]);
            $cb->setMultaFixa($ret[$i]["MULTAFIXA"]);
            $cb->setMoraDia($ret[$i]["MORADIA"]);
            $cb->setLocalArquivo($ret[$i]["LOCALIZACAO_ARQ_CONF"]);
            $cb->setConfiguracao1($ret[$i]["CONFIGURACAO_1"]);
            $cb->setConfiguracao2($ret[$i]["CONFIGURACAO_2"]);
            $cb->setDiasProtesto($ret[$i]["DIASPROTESTO"]);
            
            array_push($this->lista, $cb);
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
