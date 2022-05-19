<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");

class faturaitem implements iface {
    private $unidade       = "";
    private $empresa       = "";
    private $dataemissao   = "";
    private $numero        = "";
    private $item          = "";
    private $descricao     = "";
    private $quantidade    = "";
    private $valorunitario = "";
    private $valortotal    = "";
    private $operacao      = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getDataEmissao(){ return $this->dataemissao; }
    function getNumero(){ return $this->numero; }
    function getItem(){ return $this->item; }
    function getDescricao(){ return $this->descricao; }
    function getQuantidade(){ return $this->quantidade; }
    function getValorUnitario(){ return $this->valorunitario; }
    function getValorTotal(){ return $this->valortotal; }
    function getOperacao(){ return $this->operacao; }

    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setDataEmissao($valor){ $this->dataemissao = $valor; }
    function setNumero($valor){ $this->numero = $valor; }
    function setItem($valor){ $this->item = $valor; }
    function setDescricao($valor){ $this->descricao = $valor; }
    function setQuantidade($valor){ $this->quantidade = $valor; }
    function setValorUnitario($valor){ $this->valorunitario = $valor; }
    function setValorTotal($valor){ $this->valortotal = $valor; }
    function setOperacao($valor){ $this->operacao = $valor; }
    
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
            return new faturaitem();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.DATAEMISSAO", "A.NUMERO",
                        "A.ITEM", "A.DESCRICAO", "A.QUANTIDADE", "A.VALORUNITARIO", 
                        "A.VALORTOTAL", "A.OPERACAO");
        
        $fb   = new FB("T_FATURAITEM A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $fi = new faturaitem();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $fi->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $fi->setEmpresa($em);
            
            $fi->setDataEmissao($ret[$i]["DATAEMISSAO"]);
            $fi->setNumero($ret[$i]["NUMERO"]); 
            $fi->setItem($ret[$i]["ITEM"]);
            $fi->setDescricao($ret[$i]["DESCRICAO"]);
            $fi->setQuantidade($ret[$i]["QUANTIDADE"]);
            $fi->setValorUnitario($ret[$i]["VALORUNITARIO"]);
            $fi->setValorTotal($ret[$i]["VALORTOTAL"]);
            $fi->setOperacao($ret[$i]["OPERACAO"]);
            
            array_push($this->lista, $fi);
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
