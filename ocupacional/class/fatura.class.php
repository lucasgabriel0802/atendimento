<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");

class fatura implements iface {
    private $unidade           = "";
    private $empresa           = "";
    private $dataemissao       = "";
    private $numero            = "";
    private $anomesfaturamento = "";
    private $datavencimento    = "";
    private $valor             = "";
    private $bancoboleto       = "";
    private $contaboleto       = "";
    private $numeroboleto      = "";
    private $percmultafixa     = "";
    private $percmoradia       = "";
    private $vlrmultafixa      = "";
    private $vlrmoradia        = "";
    private $nossonumero_cbx   = "";
    private $codigo_verificacao_nfse = "";
    private $retencaoinss      = 0;
    private $valor_ir          = 0;
    private $valor_csll        = 0;
    private $valor_cofins      = 0;
    private $valor_pis         = 0;
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getDataEmissao(){ return $this->dataemissao; }
    function getNumero(){ return $this->numero; }
    function getAnoMesFaturamento(){ return $this->anomesfaturamento; }
    function getDataVencimento(){ return $this->datavencimento; }
    function getValor(){ return $this->valor; }
    function getBancoBoleto(){ return $this->bancoboleto; }
    function getContaBoleto(){ return $this->contaboleto; }
    function getNumeroBoleto(){ return $this->numeroboleto; }
    function getPercMultaFixa(){ return $this->percmultafixa; }
    function getPercMoraDia(){ return $this->percmoradia; }
    function getVlrMultaFixa(){ return $this->vlrmultafixa; }
    function getVlrMoraDia(){ return $this->vlrmoradia; }
    function getNossoNumeroCBX(){ return $this->nossonumero_cbx; }
    function getCodigoVerificacaoNFSE(){ return $this->codigo_verificacao_nfse; }
    function getRetencaoINSS(){ return $this->retencaoinss; }
    function getValorIR(){ return $this->valor_ir; }
    function getValorCSLL(){ return $this->valor_csll; }
    function getValorCOFINS(){ return $this->valor_cofins; }
    function getValorPIS(){ return $this->valor_pis; }
    
    function getImpostos(){
        return $this->getRetencaoINSS() + 
                $this->getValorIR() +
                $this->getValorCSLL() +
                $this->getValorCOFINS() +
                $this->getValorPIS();
    }

    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setDataEmissao($valor){ $this->dataemissao = $valor; }
    function setNumero($valor){ $this->numero = $valor; }
    function setAnoMesFaturamento($valor){ $this->anomesfaturamento = $valor; }
    function setDataVencimento($valor){ $this->datavencimento = $valor; }
    function setValor($valor){ $this->valor = $valor; }
    function setBancoBoleto($valor){ $this->bancoboleto = $valor; }
    function setContaBoleto($valor){ $this->contaboleto = $valor; }
    function setNumeroBoleto($valor){ $this->numeroboleto = $valor; }
    function setPercMultaFixa($valor){ $this->percmultafixa = $valor; }
    function setPercMoraDia($valor){ $this->percmoradia = $valor; }
    function setVlrMultaFixa($valor){ $this->vlrmultafixa = $valor; }
    function setVlrMoraDia($valor){ $this->vlrmoradia = $valor; }
    function setNossoNumeroCBX($valor){ $this->nossonumero_cbx = $valor; }
    function setCodigoVerificacaoNFSE($valor){ $this->codigo_verificacao_nfse = $valor; }
    function setRetencaoINSS($valor){ $this->retencaoinss = $valor; }
    function setValorIR($valor){ $this->valor_ir = $valor; }
    function setValorCSLL($valor){ $this->valor_csll = $valor; }
    function setValorCOFINS($valor){ $this->valor_cofins = $valor; }
    function setValorPIS($valor){ $this->valor_pis = $valor; }
    
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
            return new fatura();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.DATAEMISSAO", 
                        "A.NUMERO", "A.ANOMESFATURAMENTO", "A.DATAVENCIMENTO", 
                        "A.VALOR", "A.BANCOBOLETO", "A.CONTABOLETO", 
                        "A.NUMEROBOLETO", "A.PERCMULTAFIXA", "A.PERCMORADIA",
                        "A.VLRMULTAFIXA", "A.VLRMORADIA", "A.NOSSONUMERO_CBX", 
                        "A.CODIGO_VERIFICACAO_NFSE", "A.RETENCAOINSS", 
                        "A.VALOR_IR", "A.VALOR_CSLL", "A.VALOR_COFINS", 
                        "A.VALOR_PIS");
        
        $fb   = new FB("T_FATURA A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $f = new fatura();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $f->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $f->setEmpresa($em);
                        
            $f->setDataEmissao($ret[$i]["DATAEMISSAO"]);
            $f->setNumero($ret[$i]["NUMERO"]);
            $f->setAnoMesFaturamento($ret[$i]["ANOMESFATURAMENTO"]);
            $f->setDataVencimento($ret[$i]["DATAVENCIMENTO"]);
            $f->setValor($ret[$i]["VALOR"]);
            $f->setBancoBoleto($ret[$i]["BANCOBOLETO"]);
            $f->setContaBoleto($ret[$i]["CONTABOLETO"]);
            $f->setNumeroBoleto($ret[$i]["NUMEROBOLETO"]);
            $f->setPercMultaFixa($ret[$i]["PERCMULTAFIXA"]);
            $f->setPercMoraDia($ret[$i]["PERCMORADIA"]);
            $f->setVlrMultaFixa($ret[$i]["VLRMULTAFIXA"]);
            $f->setVlrMoraDia($ret[$i]["VLRMORADIA"]);
            $f->setNossoNumeroCBX($ret[$i]["NOSSONUMERO_CBX"]);
            $f->setCodigoVerificacaoNFSE($ret[$i]["CODIGO_VERIFICACAO_NFSE"]);
            $f->setRetencaoINSS($ret[$i]["RETENCAOINSS"]);
            $f->setValorIR($ret[$i]["VALOR_IR"]);
            $f->setValorCSLL($ret[$i]["VALOR_CSLL"]);
            $f->setValorCOFINS($ret[$i]["VALOR_COFINS"]);
            $f->setValorPIS($ret[$i]["VALOR_PIS"]);
            
            array_push($this->lista, $f);
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
