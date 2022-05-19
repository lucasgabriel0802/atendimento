<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");
require_once("usuarios.class.php");

class usuwebemp implements iface {
    private $unidade, $empresa, $usuarios;
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getUnidade(){ 
        if (is_a($this->unidade, "unidade"))
            return $this->unidade;
        else
            return new unidade();
    }
    function getEmpresa(){ 
        if (is_a($this->empresa, "empresa"))
            return $this->empresa;
        else
            return new empresa();
    }
    function getUsuario(){ 
        if (is_a($this->usuarios, "usuarios"))
            return $this->usuarios;
        else
            return new usuarios();
    }
    
    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setUsuario($valor){ $this->usuario = $valor; }
    
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
            return new usuwebemp();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.USUARIO", "A.EMPRESA",
                        "B.RAZAOSOCIAL AS RAZAOSOCIAL_U", "B.NOMEFANTASIA AS NOMEFANTASIA_U",
                        "C.RAZAOSOCIAL AS RAZAOSOCIAL_E", "C.NOMEFANTASIA AS NOMEFANTASIA_E");
        
        $join   = "JOIN T_UNIDADE B ON B.CODIGO = A.UNIDADE ".
                  "JOIN T_EMPRESA C ON C.CODIGO = A.EMPRESA AND C.UNIDADE = A.UNIDADE";
        $fb     = new FB("T_USUWEB_EMP A");
        $ret    = $fb->select($filtro, null, null, $join, $select);
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $u = new usuwebemp();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $un->setRazaoSocial($ret[$i]["RAZAOSOCIAL_U"]);
            $un->setNomeFantasia($ret[$i]["NOMEFANTASIA_U"]);
            $u->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $em->setRazaoSocial($ret[$i]["RAZAOSOCIAL_E"]);
            $em->setNomeFantasia($ret[$i]["NOMEFANTASIA_E"]);
            $u->setEmpresa($em);
            
            $us = new usuarios();
            $us->setCodigo($ret[$i]["USUARIO"]);
            $u->setUsuario($us);
            
            array_push($this->lista, $u);
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
