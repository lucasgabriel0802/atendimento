<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("exame.class.php");

class funcexamesacomp implements iface {
    private $item = "";
    private $datarealizacao = "";
    private $exame = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getItem(){ return $this->item; }
    function getDataRealizacao(){ return $this->datarealizacao; }
    function getExame(){ return $this->exame; }
    
    function setItem($valor){ $this->item = $valor; }
    function setDataRealizacao($valor){ $this->datarealizacao = $valor; }    
    function setExame($valor){ $this->exame = $valor; }
    
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
            return new funcexamesacomp();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("B.EXAME", "B.DESCRICAO", "A.ITEM", "A.DATAREALIZACAO", 
                        "B.ORIENTACOES_REALIZACAO");
        
        $join   = "LEFT JOIN T_EXAME B ON A.EXAME = B.CODIGO ";
                  //"LEFT JOIN T_ORDEMIMPRESSAOEXAME C ON C.UNIDADE = A.UNIDADE ".
                  //"                                 AND C.CODIGO  = B.ORDEMIMPRESSAO";

        $fb = new FB("T_FUNCEXAME_ACOMP A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $fe = new funcexamesacomp();
            $fe->setItem($ret[$i]["ITEM"]);
            $fe->setDataRealizacao($ret[$i]["DATAREALIZACAO"]);
            
            $e = new exame();
            $e->setCodigo($ret[$i]["EXAME"]);
            $e->setDescricao($ret[$i]["DESCRICAO"]);
            $e->setOrientacoes($ret[$i]["ORIENTACOES_REALIZACAO"]);
            $fe->setExame($e);

            array_push($this->lista, $fe);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function buscarExames($empresa, $unidade, $posto, $matricula, $setor, $funcao, $tipoExame, $dataExame){
        $fb = new FB("T_FUNCEXAME_ACOMP A");
        $fb->executeProcedure("SP_AGENDA_RISCO_EXAME", array($empresa,   $unidade,
                                                             $posto,     $matricula, 
                                                             $setor,     $funcao,
                                                             $tipoExame, $dataExame));
    }
    
    function remover(){
        
    }
    function alterar(){
        
    }
    function inserir(){
        
    }
}
