<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("empresa.class.php");
require_once("funcionario.class.php");
require_once("funcexamesacomp.class.php");
require_once("funcexames.class.php");

class funcaso implements iface {
    private $empresa             = "";
    private $funcionario         = "";
    private $datapedido          = "";
    private $numero              = "";
    private $caracteristica      = "";
    private $pdfDigitalizacaoASO = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getEmpresa(){ return $this->empresa; }
    function getFuncionario(){ return $this->funcionario; }
    function getDataPedido(){ return $this->datapedido; }
    function getNumero(){ return $this->numero; }
    function getCaracteristica(){ return $this->caracteristica; }
    function getPDFDigitalizacaoASO(){ return $this->pdfDigitalizacaoASO; }
    
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setFuncionario($valor){ $this->funcionario = $valor; }
    function setDataPedido($valor){ $this->datapedido = $valor; }
    function setNumero($valor){ $this->numero = $valor; }
    function setCaracteristica($valor){ $this->caracteristica = $valor; }
    function setPDFDigitalizacaoASO($valor){ $this->pdfDigitalizacaoASO = $valor; }
    
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
            return new funcaso();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("DISTINCT A.EMPRESA", "C.RAZAOSOCIAL", "A.MATRICULA", 
                        "B.NOME", "A.DATAPEDIDO", "A.NUMERO", "A.CARACTERISTICA",
                        "A.PDFDIGITALIZACAOASO", "C.PERMITEDIGITALIZACAOASO");
        $join   = "LEFT JOIN T_FUNCIONARIO B ON B.MATRICULA = A.MATRICULA "
                . "                         AND B.EMPRESA   = A.EMPRESA   "
                . "                         AND B.UNIDADE   = A.UNIDADE   "
                . "LEFT JOIN T_EMPRESA     C ON C.CODIGO    = A.EMPRESA   "
                . "                         AND C.UNIDADE   = A.UNIDADE   ";
        
        $fb = new FB("T_FUNCASO A");
        $ret = $fb->select($filtro, null, null, $join, $select, null, true);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $f = new funcaso();
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $em->setRazaoSocial($ret[$i]["RAZAOSOCIAL"]);
            $em->setPermiteDigitalizacaoASO($ret[$i]["PERMITEDIGITALIZACAOASO"] == "S");
            $f->setEmpresa($em);
            
            $fu = new funcionario();
            $fu->setCodigo($ret[$i]["MATRICULA"]);
            $fu->setNome($ret[$i]["NOME"]);
            $f->setFuncionario($fu);
            
            $f->setDataPedido($ret[$i]["DATAPEDIDO"]);
            $f->setNumero($ret[$i]["NUMERO"]);
            $f->setCaracteristica($ret[$i]["CARACTERISTICA"]);

            if (!empty($ret[$i]["PDFDIGITALIZACAOASO"])) {
                $arquivo = $ret[$i]["PDFDIGITALIZACAOASO"];

                $blob_data = ibase_blob_info($fb->getConexao(), $arquivo);
                $blob_hndl = ibase_blob_open($fb->getConexao(), $arquivo);
                $f->setPDFDigitalizacaoASO(ibase_blob_get($blob_hndl, $blob_data[0]));
                ibase_blob_close($blob_hndl);
            }
            
            array_push($this->lista, $f);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function buscarDocumentos($unidade, $empresa, $matricula){
        $select = array("DISTINCT A.EMPRESA", "C.RAZAOSOCIAL", "A.MATRICULA", 
                        "B.NOME", "A.DATAPEDIDO", "A.NUMERO", "A.CARACTERISTICA",
                        "A.PDFDIGITALIZACAOASO", "C.PERMITEDIGITALIZACAOASO");
        $join   = "LEFT JOIN T_FUNCIONARIO B ON B.MATRICULA = A.MATRICULA "
                . "                         AND B.EMPRESA   = A.EMPRESA   "
                . "                         AND B.UNIDADE   = A.UNIDADE   "
                . "LEFT JOIN T_EMPRESA     C ON C.CODIGO    = A.EMPRESA   "
                . "                         AND C.UNIDADE   = A.UNIDADE   ";
        
        $fb = new FB("T_FUNCASO A");
        $ret = $fb->executeQuery(" SELECT DISTINCT A.EMPRESA, C.RAZAOSOCIAL, A.MATRICULA, B.NOME, A.DATAPEDIDO,
                                            A.NUMERO, A.CARACTERISTICA, A.PDFDIGITALIZACAOASO, C.PERMITEDIGITALIZACAOASO
                                     FROM T_FUNCASO A
                                     LEFT JOIN T_FUNCIONARIO B ON B.MATRICULA = A.MATRICULA
                                                              AND B.EMPRESA = A.EMPRESA
                                                              AND B.UNIDADE = A.UNIDADE
                                     LEFT JOIN T_EMPRESA C ON C.CODIGO = A.EMPRESA
                                                          AND C.UNIDADE = A.UNIDADE
                                    WHERE A.UNIDADE = '" .$unidade. "'".
                                    "  AND A.EMPRESA = '".$empresa."'".
                                    "  AND A.MATRICULA = '".$matricula."'".
                                    "  AND (CURRENT_DATE - A.DATAENVIO)<= '60'");        

//        $ret = $fb->select($filtro, null, null, $join, $select, null, true);        
        $this->totalRegistros = count($ret);
        
        
//        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $f = new funcaso();
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $em->setRazaoSocial($ret[$i]["RAZAOSOCIAL"]);
            $em->setPermiteDigitalizacaoASO($ret[$i]["PERMITEDIGITALIZACAOASO"] == "S");
            $f->setEmpresa($em);
            
            $fu = new funcionario();
            $fu->setCodigo($ret[$i]["MATRICULA"]);
            $fu->setNome($ret[$i]["NOME"]);
            $f->setFuncionario($fu);
            
            $f->setDataPedido($ret[$i]["DATAPEDIDO"]);
            $f->setNumero($ret[$i]["NUMERO"]);
            $f->setCaracteristica($ret[$i]["CARACTERISTICA"]);

            if (!empty($ret[$i]["PDFDIGITALIZACAOASO"])) {
                $arquivo = $ret[$i]["PDFDIGITALIZACAOASO"];

                $blob_data = ibase_blob_info($fb->getConexao(), $arquivo);
                $blob_hndl = ibase_blob_open($fb->getConexao(), $arquivo);
                $f->setPDFDigitalizacaoASO(ibase_blob_get($blob_hndl, $blob_data[0]));
                ibase_blob_close($blob_hndl);
            }
            
            array_push($this->lista, $f);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }

    function remover($val = null){
        if ($val != null){
            if (is_array($val)){
                $fb = new FB("T_FUNCASO");               
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
                $fb = new FB("T_FUNCASO");               
                return $fb->insert($val);
            }else
                return false;
        }else
            return false; 
    }
    
    function getUltimoASO($unidade, $empresa, $matricula){
        if ($this->buscar(array("A.UNIDADE"   => $unidade,
                                "A.EMPRESA"   => $empresa,
                                "A.MATRICULA" => $matricula,
                                "A.SITUACAO"  => "A"))){
            return $this->getItemLista(0)->getNumero();
        }else
            return 0;
    }
    
    function gerarNovoASO($empresa, $unidade, $posto, $matricula, $setor, $funcao, $tipoExame, $dataExame, &$retorno){
        $retorno = array("codigo" => 0, "mensagem" => "");
        
        $fea = new funcexamesacomp();
        $fea->buscarExames($empresa, $unidade, $posto, $matricula, $setor, $funcao, $tipoExame, $dataExame);
        
        if ($fea->buscar(array("A.UNIDADE" => $unidade,
                               "A.EMPRESA" => $empresa,
                               "A.MATRICULA" => $matricula))){
            
            $fu = new funcionario();
            if ($fu->buscar(array("A.UNIDADE"   => $unidade,
                                  "A.EMPRESA"   => $empresa,
                                  "A.MATRICULA" => $matricula), 1)){

                $numeroaso = $this->getUltimoASO($unidade, $empresa, $matricula);
                $numeroaso++;
                
                $valASO = array("UNIDADE"    => $unidade,
                                "EMPRESA"    => $empresa,
                                "MATRICULA"  => $matricula,
                                "NUMERO"     => $numeroaso,
                                "DATAPEDIDO" => date("Y-m-d"),
                                "CARACTERISTICA" => $tipoExame,
                                "SITUACAO"       => "A",
                                "SETOR"          => $setor,
                                "FUNCAO"         => $funcao);
                
                if ($this->inserir($valASO)){

                    $bInserido = true;
                    for ($i = 0; $i < $fea->getTotalLista(); $i++){

                        $fe = new funcexames();
                        $valExame = array("UNIDADE"   => $unidade,
                                          "EMPRESA"   => $empresa,
                                          "MATRICULA" => $matricula,
                                          "NUMEROASO" => $numeroaso,
                                          "EXAME"     => str_pad($fea->getItemLista($i)->getExame()->getCodigo(), 3, "0", STR_PAD_LEFT),
                                          "ITEM"      => ($i + 1),
                                          "DATAPEDIDO"          => date("Y-m-d"),
                                          "CARACTERISTICA"      => $tipoExame,
                                          "FATURADO"            => "N",
                                          "FECHADOCTAPRESTADOR" => "N",
                                          "GUIAORIGINAL"        => "S",
                                          "SELECAO"             => "N",
                                          "USUARIO_PEDIDO"      => "001");
                        
                        if (!$fe->inserir($valExame)){
                            $bInserido = false;
                            $i = $fea->getTotalLista();
                        }
                        
                    }
                    
                    if (!$bInserido){
                        $fe = new funcexames();
                        $fe->remover(array("UNIDADE"   => $unidade,
                                           "EMPRESA"   => $empresa,
                                           "MATRICULA" => $matricula,
                                           "NUMEROASO" => $numeroaso));
                        
                        $this->remover(array("UNIDADE"   => $unidade,
                                             "EMPRESA"   => $empresa,
                                             "MATRICULA" => $matricula,
                                             "NUMERO"    => $numeroaso));
                        
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Não foi possível gerar o ASO!";
                    } 
                }else{
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Não foi possível inserir o ASO!";
                }
                
            }else{
                $retorno["codigo"]   = 2;
                $retorno["mensagem"] = "Funcionário não encontrado!";
            }
        }else{
            $retorno["codigo"]   = 1;
            $retorno["mensagem"] = "Funcionário não tem exames para realizar!";
        }
    }
}
