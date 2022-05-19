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
require_once("postotrabalho.class.php");

class convocacao implements iface {
    
    private $unidade        = "";
    private $empresa        = "";
    private $postoTrabalho  = "";
    private $funcionario    = "";
    private $exame          = "";
    private $caracteristica = "";
    private $dataUltimaRealizacao = "";
    private $dataProximaRealizacao = "";
    private $validade      = 0;

    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getPostoTrabalho(){ return $this->postoTrabalho; }
    function getFuncionario(){ return $this->funcionario; }
    function getExame(){ return $this->exame; }
    function getCaracteristica(){ return $this->caracteristica; }
    function getDataUltimaRealizacao(){ return $this->dataUltimaRealizacao; }
    function getDataProximaRealizacao(){ return $this->dataProximaRealizacao; }
    function getValidade(){ return $this->validade; }
    
    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }    
    function setPostoTrabalho($valor){ $this->postoTrabalho = $valor; }    
    function setFuncionario($valor){ $this->funcionario = $valor; }    
    function setExame($valor){ $this->exame = $valor; }
    function setCaracteristica($valor){ $this->caracteristica = $valor; }
    function setDataUltimaRealizacao($valor){ $this->dataUltimaRealizacao = $valor; }
    function setDataProximaRealizacao($valor){ $this->dataProximaRealizacao = $valor; }
    function setValidade($valor){ $this->validade = $valor; }

    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    
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
            return new convocacao();
    }  
    

    function convocar($data = null){
        
        $fb = new FB("T_CONVOCACAO");
                                            
        $unidade = $_SESSION[config::getSessao()]["unidade"];                                 
        $empresa = $_SESSION[config::getSessao()]["empresa_ativa"];                                 
        $postoInferior = "000001";
        $postoSuperior = "999999";
        $admissionalPendente = "S";
        $dataAdmissao = "S";
        $somenteClinico = "N";

        $ret = $fb->executeProcedure("SP_CONVOCACAO", array($unidade, $empresa, $postoInferior, $postoSuperior, $data, $admissionalPendente, $dataAdmissao, $somenteClinico));
        if ($ret > 0){
            $select = array("A.UNIDADE", "A.EMPRESA", "A.MATRICULA", "A.EXAME", "A.POSTOTRABALHO",
                            "A.DATACONVOCACAO", "A.CARACTERISTICA","A.ULTIMAREALIZACAO","A.VALIDADE",
                            "A.PROXIMAREALIZACAO, A.EXAMEALTERADO",
                            "EX.DESCRICAO AS NOMEEXAME",
                            "F.NOME AS NOMEFUNCIONARIO",
                            "EPT.DESCRICAO AS NOMEPOSTOTRABALHO"

                            );
            
            $join   = " LEFT OUTER JOIN T_EXAME EX ON EX.CODIGO = A.EXAME ".
                    " LEFT OUTER JOIN T_EMPRESA EM ON EM.CODIGO = A.EMPRESA AND EM.UNIDADE = A.UNIDADE " .
                    " LEFT OUTER JOIN T_FUNCIONARIO F ON F.EMPRESA = A.EMPRESA AND F.UNIDADE = A.UNIDADE AND F.MATRICULA = A.MATRICULA " .
                    " LEFT OUTER JOIN T_EMPPOSTOTRABALHO EPT ON EPT.UNIDADE = A.UNIDADE AND EPT.EMPRESA = A.EMPRESA AND EPT.CODIGO = F.POSTOTRABALHO ";

            $filtro = array("COALESCE(A.UNIDADE, '')" => $unidade,
                            "COALESCE(A.EMPRESA, '')" => $empresa,
                            "A.PROXIMAREALIZACAO" => array($data, "<="));

            $fb = new FB("T_CONVOCACAO A");

            $ordem = ["F.NOME" => "ASC"];
            $ret = $fb->select($filtro, null, $ordem, $join, $select, null, False);

            for ($i = 0; $i < count($ret); $i++){
               
                $convc = new convocacao();
                
                $un = new unidade();
                $un->setCodigo($ret[$i]["UNIDADE"]);
                $convc->setUnidade($un);
                
                $em = new empresa();
                $em->setCodigo($ret[$i]["EMPRESA"]);
                $convc->setEmpresa($em);
                
                $fu = new funcionario();
                $fu->setCodigo($ret[$i]["MATRICULA"]);
                $fu->setNome($ret[$i]["NOMEFUNCIONARIO"]);
                $convc->setFuncionario($fu);
                
                $e = new exame();
                $e->setCodigo($ret[$i]["EXAME"]);
                $e->setDescricao($ret[$i]["NOMEEXAME"]);
                $convc->setExame($e);
                
                $p = new postotrabalho();
                $p->setCodigo($ret[$i]["POSTOTRABALHO"]);
                $p->setDescricao($ret[$i]["NOMEPOSTOTRABALHO"]);
                $convc->setPostoTrabalho($p);

                $convc->setCaracteristica($ret[$i]["CARACTERISTICA"]);
                $convc->setValidade($ret[$i]["VALIDADE"]);
                $convc->setDataUltimaRealizacao($ret[$i]["ULTIMAREALIZACAO"]);
                $convc->setDataProximaRealizacao($ret[$i]["PROXIMAREALIZACAO"]);
                
                array_push($this->lista, $convc);
            }       
        }
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function buscar($filtro = null, $limite = null, $ordem = null)
    {
        
    }
    function remover($val = null){}
    function alterar(){}
    function inserir($val = null){}
}
