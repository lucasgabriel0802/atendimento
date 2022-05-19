<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class cep implements iface{
    private $endereco    = "";
    private $numero      = "";
    private $complemento = "";
    private $bairro      = "";
    private $cidade      = "";
    private $uf          = "";
    private $cep         = "";

    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getEndereco(){ return $this->endereco; }
    function getNumero(){ return $this->numero; }
    function getComplemento(){ return $this->complemento; }
    function getBairro(){ return $this->bairro; }
    function getCidade(){ return $this->cidade; }
    function getUF(){ return $this->uf; }
    function getCEP(){ return $this->cep; }
    
    function setEndereco($valor){ $this->endereco = $valor; }
    function setNumero($valor){ $this->numero = $valor; }
    function setComplemento($valor){ $this->complemento = $valor; }
    function setBairro($valor){ $this->bairro = $valor; }
    function setCidade($valor){ $this->cidade = $valor; }
    function setUF($valor){ $this->uf = $valor; }
    function setCEP($valor){ $this->cep = $valor; }  
    
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
            return new cep();
    }   
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.CODIGO", "A.ENDERECO", "A.BAIRRO", "A.CIDADE", "A.ESTADO");
        $fb   = new FB("T_CEP A");
        
        $ret = $fb->select($filtro, null, null, null, null, null, false);        
        $this->totalRegistros = count($ret);
        
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);

        for ($i = 0; $i < count($ret); $i++){
            $item = new cep();
            $item->setCEP($ret[$i]["CODIGO"]);
            $item->setEndereco($ret[$i]["ENDERECO"]);
            $item->setBairro($ret[$i]["BAIRRO"]);
            $item->setCidade($ret[$i]["CIDADE"]);
            $item->setUF($ret[$i]["ESTADO"]);
            
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
