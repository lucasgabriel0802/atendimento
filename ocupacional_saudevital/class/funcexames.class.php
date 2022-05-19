<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");
require_once("funcionario.class.php");
require_once("exame.class.php");

class funcexames implements iface {
    private $unidade        = "";
    private $empresa        = "";
    private $funcionario    = "";
    private $exame          = "";
    private $codigoTuss     = "";
    private $dataRealizacao = "";
    private $tipoExame      = "";
    private $resultado      = "";
    private $alterado       = "";
    private $agravamento    = "";
    private $natureza       = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getFuncionario(){ return $this->funcionario; }
    function getExame(){ return $this->exame; }
    function getCodigoTuss(){ return $this->codigoTuss; }
    function getDataRealizacao(){ return $this->dataRealizacao; }
    function getTipoExame(){ return $this->tipoExame; }
    function getResultado(){ return $this->resultado; }
    function getAlterado(){ return $this->alterado; }
    function getAgravamento(){ return $this->agravamento; }
    function getNatureza(){ return $this->natureza; }
    
    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }    
    function setFuncionario($valor){ $this->funcionario = $valor; }    
    function setExame($valor){ $this->exame = $valor; }
    function setCodigoTuss($valor){ $this->codigoTuss = $valor; }
    function setDataRealizacao($valor){ $this->dataRealizacao = $valor; }
    function setTipoExame($valor){ $this->tipoExame = $valor; }
    function setResultado($valor){ $this->resultado = $valor; }
    function setAlterado($valor){ $this->alterado = $valor; }
    function setAgravamento($valor){ $this->agravamento = $valor; }
    function setNatureza($valor){ $this->natureza = $valor; }
    
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
            return new funcexames();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.MATRICULA", "A.EXAME", "B.DESCRICAO",
                        "B.CODIGO_TUSS", "A.DATAREALIZACAO", "A.TIPOEXAME", "A.RESULTADO",
                        "A.ALTERADO", "A.AGRAVAMENTO", "A.NATUREZA");
        
        $join   = "JOIN T_EXAME B ON A.EXAME = B.CODIGO";

        if (isset($filtro["UPPER(B.DESCRICAO)"])){
            $join .= " AND UPPER(B.DESCRICAO)";
            if (is_array($filtro["UPPER(B.DESCRICAO)"]))
                $join .= " ".$filtro["UPPER(B.DESCRICAO)"][1]." '".addslashes($filtro["UPPER(B.DESCRICAO)"][0])."'";
            else
                $join .= " = '".addslashes($filtro["UPPER(B.DESCRICAO)"])."'";
            unset($filtro["UPPER(B.DESCRICAO)"]);
        }
        
        $fb = new FB("T_FUNCEXAME A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $fe = new funcexames();
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $fe->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $fe->setEmpresa($em);
            
            $fu = new funcionario();
            $fu->setCodigo($ret[$i]["MATRICULA"]);
            $fe->setFuncionario($fu);
            
            $e = new exame();
            $e->setCodigo($ret[$i]["EXAME"]);
            $e->setDescricao($ret[$i]["DESCRICAO"]);
            $fe->setExame($e);
            
            $fe->setCodigoTuss($ret[$i]["CODIGO_TUSS"]);
            $fe->setDataRealizacao($ret[$i]["DATAREALIZACAO"]);
            $fe->setTipoExame($ret[$i]["TIPOEXAME"]);
            $fe->setResultado($ret[$i]["RESULTADO"]);
            $fe->setAlterado($ret[$i]["ALTERADO"]);
            $fe->setAgravamento($ret[$i]["AGRAVAMENTO"]);
            $fe->setNatureza($ret[$i]["NATUREZA"]);

            array_push($this->lista, $fe);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
        
    function remover($val = null){
        if ($val != null){
            if (is_array($val)){
                $fb = new FB("T_FUNCEXAME");               
                return $fb->delete($val);
            }else
                return false;
        }else
            return false;          
    }
    function alterar(){
        
    }
    function inserir($val = null){
        if ($val != null){
            if (is_array($val)){
                $fb = new FB("T_FUNCEXAME");               
                return $fb->insert($val);
            }else
                return false;
        }else
            return false;         
    }
}
