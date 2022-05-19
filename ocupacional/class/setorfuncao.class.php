<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("setor.class.php");
require_once("funcao.class.php");

class setorfuncao implements iface {
    private $setor  = "";
    private $funcao = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getSetor(){ return $this->setor; }
    function getFuncao(){ return $this->funcao; }
    
    function setSetor($valor){ $this->setor = $valor; }
    function setFuncao($valor){ $this->funcao = $valor; }
    
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
        $select = array("DISTINCT A.SETOR", "UPPER(B.NOME) AS NOMESETOR", 
                        "A.FUNCAO", "UPPER(C.NOME) AS NOMEFUNCAO");
        $join   = "JOIN T_SETOR  B ON B.UNIDADE = A.UNIDADE "
                 ."               AND B.EMPRESA = A.EMPRESA "
                 ."               AND B.CODIGO  = A.SETOR   "
                 ."JOIN T_FUNCAO C ON C.UNIDADE = A.UNIDADE "
                 ."               AND C.EMPRESA = A.EMPRESA "
                 ."               AND C.CODIGO  = A.FUNCAO  ";
        
        $fb   = new FB("T_SETORFUNCAO A");
        $ret = $fb->select($filtro, null, null, $join, $select);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $sf = new setorfuncao();
            
            $se = new setor();
            $se->setCodigo($ret[$i]["SETOR"]);
            $se->setNome($ret[$i]["NOMESETOR"]);
            $sf->setSetor($se);
            
            $fu = new funcao();
            $fu->setCodigo($ret[$i]["FUNCAO"]);
            $fu->setNome($ret[$i]["NOMEFUNCAO"]);
            $sf->setFuncao($fu).
            
            array_push($this->lista, $sf);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function buscarFuncaoLevantamento($empresa, $unidade, $posto, $data, $setor, $nomeFuncao){

        $fb   = new FB("T_SETORFUNCAO");
        $ret = $fb->executeQuery( "SELECT DISTINCT CODIGOSETOR, NOMESETOR, CODIGOFUNCAO, NOMEFUNCAO"
                                 ."  FROM SP_SETOR_FUNCAO_AGENDA('".$unidade."','". $empresa."','". $posto."','".$data."')"
                                 ." WHERE CODIGOSETOR = '".$setor."'" 
                                 ."   AND NOMEFUNCAO CONTAINING '".$nomeFuncao."'");

        $this->totalRegistros = count($ret);
        
        for ($i = 0; $i < count($ret); $i++){
            $sf = new setorfuncao();
            
            $se = new setor();
            $se->setCodigo($ret[$i]["CODIGOSETOR"]);
            $se->setNome($ret[$i]["NOMESETOR"]);
            $sf->setSetor($se);
            
            $fu = new funcao();
            $fu->setCodigo($ret[$i]["CODIGOFUNCAO"]);
            $fu->setNome($ret[$i]["NOMEFUNCAO"]);
            $sf->setFuncao($fu).
            
            array_push($this->lista, $sf);
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
