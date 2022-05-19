<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("medico.class.php");
require_once("empresa.class.php");
require_once("funcionario.class.php");

class agendacancelamento implements iface {
    private $unidade     = "";
    private $data        = "";
    private $hora        = "";
    private $medico      = "";
    private $empresa     = "";
    private $funcionario = "";
    private $postotrabalho = "";
    private $tipoexame     = "";
    private $responsavelagendamento  = "";
    private $responsavelcancelamento = "";
    private $motivocancelamento      = "";
    private $datacancelamento        = "";
    private $horacancelamento        = "";
    private $usuariocancelamento     = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getUnidade(){ return $this->unidade; }
    function getData(){ return $this->data; }
    function getHora(){ return $this->hora; }
    function getMedico(){ return $this->medico; }
    function getEmpresa(){ return $this->empresa; }
    function getFuncionario(){ return $this->funcionario; }
    function getPostoTrabalho(){ return $this->postotrabalho; }
    function getTipoExame(){ return $this->tipoexame; }
    function getResponsavelAgendamento(){ return $this->responsavelagendamento; }
    function getResponsavelCancelamento(){ return $this->responsavelcancelamento; }
    function getMotivoCancelamento(){ return $this->motivocancelamento; }
    function getDataCancelamento(){ return $this->datacancelamento; }
    function getHoraCancelamento(){ return $this->horacancelamento; }
    function getUsuarioCancelamento(){ return $this->usuariocancelamento; }

    function setUnidade($valor){ $this->unidade = $valor; }
    function setData($valor){ $this->data = $valor; }
    function setHora($valor){ $this->hora = $valor; }
    function setMedico($valor){ $this->medico = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setFuncionario($valor){ $this->funcionario = $valor; }
    function setPostoTrabalho($valor){ $this->postotrabalho = $valor; }
    function setTipoExame($valor){ $this->tipoexame = $valor; }
    function setResponsavelAgendamento($valor){ $this->responsavelagendamento = $valor; }
    function setResponsavelCancelamento($valor){ $this->responsavelcancelamento = $valor; }
    function setMotivoCancelamento($valor){ $this->motivocancelamento = $valor; }
    function setDataCancelamento($valor){ $this->datacancelamento = $valor; }
    function setHoraCancelamento($valor){ $this->horacancelamento = $valor; }
    function setUsuarioCancelamento($valor){ $this->usuariocancelamento = $valor; }
    
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
            return new agendacancelamento();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){ }
    
    function remover(){ }
    function alterar(){ }
    function inserir(){
        $temp = array("CODIGOUNIDADE" => $this->getUnidade()->getCodigo(),
                      "DATA"          => $this->getData(),
                      "HORA"          => $this->getHora(),
                      "CODIGOMEDICO"  => $this->getMedico()->getCodigo(),
                      "NOMEMEDICO"    => $this->getMedico()->getNome(),
                      "CODIGOEMPRESA" => $this->getEmpresa()->getCodigo(),
                      "NOMEEMPRESA"   => $this->getEmpresa()->getNomeFantasia(),
                      "DDD"           => $this->getFuncionario()->getDDD(),
                      "FONE"          => $this->getFuncionario()->getTelefoneSemDDD(),
                      "NOMEPESSOA"    => $this->getFuncionario()->getNome(),
                      "TIPO"          => $this->getTipoExame(),
                      "RESPONSAVELAGENDAMENTO"  => $this->getResponsavelAgendamento(),
                      "RESPONSAVELCANCELAMENTO" => $this->getResponsavelCancelamento(),
                      "MOTIVOCANCELAMENTO"      => $this->getMotivoCancelamento(),
                      "DATACANCELAMENTO"        => $this->getDataCancelamento(),
                      "HORACANCELAMENTO"        => $this->getHoraCancelamento(),
                      "USUARIOCANCELAMENTO"     => $this->getUsuarioCancelamento(),
                      "CODIGOPOSTOTRABALHO"     => $this->getPostoTrabalho()->getCodigo(),
                      "DESCRICAOPOSTOTRABALHO"  => $this->getPostoTrabalho()->getDescricao(),
                      "FATURADO"                => "N");
        
        if (strlen($temp["DESCRICAOPOSTOTRABALHO"]) > 60)
            $temp["DESCRICAOPOSTOTRABALHO"] = substr($temp["DESCRICAOPOSTOTRABALHO"], 0, 60);
        
        $fb = new FB("T_AGENDAHISTORICOCANCELAMENTO");
        return $fb->insert($temp);
    }
}
