<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class setor implements iface {
    private $codigo = "";
    private $nome   = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getCodigo(){ return $this->codigo; }
    function getNome(){ return $this->nome; }
    
    function setCodigo($valor){ $this->codigo = $valor; }
    function setNome($valor){ $this->nome = $valor; }
    
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
            return new setor();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.CODIGO", "UPPER(A.NOME) AS NOME");
        
        $fb   = new FB("T_SETOR A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, null, $select);
        for ($i = 0; $i < count($ret); $i++){
            $s = new setor();
            $s->setCodigo($ret[$i]["CODIGO"]);
            $s->setNome($ret[$i]["NOME"]);
            
            array_push($this->lista, $s);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function buscarSetorLevantamento($empresa, $unidade, $posto, $data, $nomeSetor){
        $fb   = new FB("T_SETOR A");


        if (isset($nomeSetor)&&$nomeSetor != ""){
            $ret = $fb->executeQuery( "SELECT DISTINCT CODIGOSETOR, NOMESETOR"
                                     ."  FROM SP_SETOR_FUNCAO_AGENDA('".$unidade."','". $empresa."','". $posto."','".$data."')"
                                     ." WHERE NOMESETOR CONTAINING '". $nomeSetor."'" );
            
        } else {
            $ret = $fb->executeQuery( "SELECT DISTINCT CODIGOSETOR, NOMESETOR"
                                     ."  FROM SP_SETOR_FUNCAO_AGENDA('".$unidade."','". $empresa."','". $posto."','".$data."')");
            
        }
            
        $this->totalRegistros = count($ret);
       
        for ($i = 0; $i < count($ret); $i++){
            $s = new setor();
            $s->setCodigo($ret[$i]["CODIGOSETOR"]);
            $s->setNome($ret[$i]["NOMESETOR"]);
            
            array_push($this->lista, $s);
        }       
        
        $this->totalLista = count($this->lista);
        $this->totalRegistros = count($ret);
        return $this->totalLista > 0;
    }

    function remover(){
        
    }
    function alterar(){
        
    }
    function inserir(){
        
    }
}
