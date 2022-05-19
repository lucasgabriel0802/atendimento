<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");
require_once("funcionario.class.php");
require_once("setor.class.php");
require_once("funcao.class.php");
require_once("postotrabalho.class.php");
require_once("medico.class.php");

class agenda implements iface {
    private $unidade = "";
    private $empresa = "";
    private $codigo  = "";
    private $data    = "";
    private $hora    = "";
    private $tipo    = "";
    private $funcionario  = "";
    private $setor        = "";
    private $funcao       = "";
    private $novosetor    = "";
    private $novafuncao   = "";
    private $postotrabalho = "";
    private $medico        = "";
    private $horachegada   = "";
    private $horachamada   = "";
    private $observacao    = "";
    private $responsavelagendamento = "";
    private $dataagendamento = "";
    private $horaagendamento = "";
    private $status          = "";
    private $quantidade      = 0;
   
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getUnidade(){ return $this->unidade; }
    function getEmpresa(){ return $this->empresa; }
    function getCodigo(){ return $this->codigo; }
    function getData(){ return $this->data; }
    function getHora(){ return $this->hora; }
    function getTipo(){ return $this->tipo; }
    function getFuncionario(){ return $this->funcionario; }
    function getSetor(){ return $this->setor; }
    function getFuncao(){ return $this->funcao; }
    function getNovoSetor(){ return $this->novosetor; }
    function getNovaFuncao(){ return $this->novafuncao; }
    function getPostoTrabalho(){ return $this->postotrabalho; }
    function getMedico(){ return $this->medico; }
    function getHoraChegada(){ return $this->horachegada; }
    function getHoraChamada(){ return $this->horachamada; }
    function getObservacao(){ return $this->observacao; }
    function getResponsavelAgendamento(){ return $this->responsavelagendamento; }
    function getDataAgendamento(){ return $this->dataagendamento; }
    function getHoraAgendamento(){ return $this->horaagendamento; }
    function getStatus(){ return $this->status; }
    function getQuantidade(){ return $this->quantidade; }

    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setCodigo($valor){ $this->codigo = $valor; }
    function setData($valor){ $this->data = $valor; }
    function setHora($valor){ $this->hora = $valor; }
    function setTipo($valor){ $this->tipo = $valor; }
    function setFuncionario($valor){ $this->funcionario = $valor; }
    function setSetor($valor){ $this->setor = $valor; }
    function setFuncao($valor){ $this->funcao = $valor; }
    function setNovoSetor($valor){ $this->novosetor = $valor; }
    function setNovaFuncao($valor){ $this->novafuncao = $valor; } 
    function setPostoTrabalho($valor){ $this->postotrabalho = $valor; }
    function setMedico($valor){ $this->medico = $valor; }
    function setHoraChegada($valor){ $this->horachegada = $valor; }
    function setHoraChamada($valor){ $this->horachamada = $valor; }
    function setObservacao($valor){ $this->observacao = $valor; }
    function setResponsavelAgendamento($valor){ $this->responsavelagendamento = $valor; }
    function setDataAgendamento($valor){ $this->dataagendamento = $valor; }
    function setHoraAgendamento($valor){ $this->horaagendamento = $valor; }
    function setStatus($valor){ $this->status = $valor; }
    function setQuantidade($valor){ $this->quantidade = $valor; }
    
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
            return new agenda();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        //$data = "";
        $select = array("A.CODIGOUNIDADE", "A.CODIGOEMPRESA", "UPPER(H.NOMEFANTASIA) AS NOMEFANTASIA",
                        "A.CODIGO", "A.DATA", "A.HORA", "C.DDDFONE1", "C.PREFONE1", "C.NUMFONE1",
                        "A.CODIGOPESSOA", "UPPER(C.NOME) AS NOMEPESSOA", "A.TIPO", "A.CODIGOSETOR", 
                        "UPPER(D.NOME) AS NOMESETOR", "A.CODIGOFUNCAO", "UPPER(E.NOME) AS NOMEFUNCAO", 
                        "A.NOVO_SETOR", "UPPER(F.NOME) AS NOMENOVOSETOR", "A.NOVA_FUNCAO", 
                        "UPPER(G.NOME) AS NOMENOVAFUNCAO", "B.CODIGOMEDICO", "A.CODIGOPOSTOTRABALHO", 
                        "UPPER(I.DESCRICAO) AS DESCPOSTOTRABALHO", "A.HORACHEGADA", "A.HORACHAMADA",
                        "A.OBSERVACAO", "UPPER(A.RESPONSAVELAGENDAMENTO) AS RESPONSAVELAGENDAMENTO", 
                        "A.DATAAGENDAMENTO", "A.HORAAGENDAMENTO", "H.INTERVALOAGENDAMENTO",
                        "H.INTERVALOCANCELAMENTO", "H.LIMITEAGENDAMENTOWEB", "H.MENSAGEMWEB", "A.STATUS",
                        "J.NOME AS NOMEMEDICO");
        
        $join = "JOIN T_AGENDA      B ON B.CODIGOUNIDADE = A.CODIGOUNIDADE ".
                "                    AND B.CODIGO        = A.CODIGO        ". // .$data.
                "JOIN T_FUNCIONARIO C ON C.UNIDADE       = A.CODIGOUNIDADE ".
                "                    AND C.EMPRESA       = A.CODIGOEMPRESA ".
                "                    AND C.MATRICULA     = A.CODIGOPESSOA  ".
                "JOIN T_SETOR       D ON D.UNIDADE       = A.CODIGOUNIDADE ".
                "                    AND D.EMPRESA       = A.CODIGOEMPRESA ".
                "                    AND D.CODIGO        = A.CODIGOSETOR   ".
                "                    AND D.POSTOTRABALHO = A.CODIGOPOSTOTRABALHO ".
                "JOIN T_FUNCAO      E ON E.UNIDADE       = A.CODIGOUNIDADE ".
                "                    AND E.EMPRESA       = A.CODIGOEMPRESA ".
                "                    AND E.CODIGO        = A.CODIGOFUNCAO  ".
                "LEFT JOIN T_SETOR  F ON F.UNIDADE       = A.CODIGOUNIDADE ".
                "                    AND F.EMPRESA       = A.CODIGOEMPRESA ".
                "                    AND F.CODIGO        = A.NOVO_SETOR    ".
                "LEFT JOIN T_FUNCAO G ON G.UNIDADE       = A.CODIGOUNIDADE ".
                "                    AND G.EMPRESA       = A.CODIGOEMPRESA ".
                "                    AND G.CODIGO        = A.NOVA_FUNCAO   ".
                "JOIN T_EMPRESA     H ON H.UNIDADE       = A.CODIGOUNIDADE ".
                "                    AND H.CODIGO        = A.CODIGOEMPRESA ".
                "JOIN T_EMPPOSTOTRABALHO I ON I.UNIDADE  = A.CODIGOUNIDADE ".
                "                         AND I.EMPRESA  = A.CODIGOEMPRESA ".
                "                         AND I.CODIGO   = A.CODIGOPOSTOTRABALHO ".
                "JOIN T_MEDICO           J ON J.CODIGO   = B.CODIGOMEDICO        ";
        
        $fb = new FB("T_AGENDAITEM A");
        $ret = $fb->select($filtro, null, $ordem, $join, $select);        
        if($ret){
            $this->totalRegistros = count($ret);
        }
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < $this->totalRegistros; $i++){
            $ag = new agenda();
            $ag->setCodigo($ret[$i]["CODIGO"]);
            $ag->setData($ret[$i]["DATA"]);
            $ag->setHora($ret[$i]["HORA"]);
            $ag->setTipo($ret[$i]["TIPO"]);
            $ag->setHoraChegada($ret[$i]["HORACHEGADA"]);
            $ag->setHoraChamada($ret[$i]["HORACHAMADA"]);
            $ag->setObservacao($ret[$i]["OBSERVACAO"]);
            $ag->setResponsavelAgendamento($ret[$i]["RESPONSAVELAGENDAMENTO"]);
            $ag->setDataAgendamento($ret[$i]["DATAAGENDAMENTO"]);
            $ag->setHoraAgendamento($ret[$i]["HORAAGENDAMENTO"]);
            $ag->setStatus($ret[$i]["STATUS"]);
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["CODIGOUNIDADE"]);
            $ag->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["CODIGOEMPRESA"]);
            $em->setNomeFantasia($ret[$i]["NOMEFANTASIA"]);
            $em->setIntervaloAgendamento($ret[$i]["INTERVALOAGENDAMENTO"]);
            $em->setIntervaloCancelamento($ret[$i]["INTERVALOCANCELAMENTO"]);
            $em->setLimiteAgendamentoWeb($ret[$i]["LIMITEAGENDAMENTOWEB"]);
            $em->setMensagemWeb($ret[$i]["MENSAGEMWEB"]);
            $ag->setEmpresa($em);

            $me = new medico();
            $me->setCodigo($ret[$i]["CODIGOMEDICO"]);
            $me->setNome($ret[$i]["NOMEMEDICO"]);
            $ag->setMedico($me);
            
            $fu = new funcionario();
            $fu->setCodigo($ret[$i]["CODIGOPESSOA"]);
            $fu->setNome($ret[$i]["NOMEPESSOA"]);
            
            $tel = "";
            if (str_pad($ret[$i]["DDDFONE1"], 3, "0", STR_PAD_LEFT) != "000")
                $tel = "(".  str_replace(" ", "", $ret[$i]["DDDFONE1"]).")";
                
            if ((str_pad($ret[$i]["PREFONE1"], 4, "0", STR_PAD_LEFT) != "0000") && 
                    (str_pad($ret[$i]["NUMFONE1"], 4, "0", STR_PAD_LEFT) != "0000")){
                if ($tel != "")
                    $tel .= " ";
                $tel .= $ret[$i]["PREFONE1"].$ret[$i]["NUMFONE1"];
            }
            
            $fu->setTelefone($tel);
            $ag->setFuncionario($fu);
            
            $pt = new postotrabalho();
            $pt->setCodigo($ret[$i]["CODIGOPOSTOTRABALHO"]);
            $pt->setDescricao($ret[$i]["DESCPOSTOTRABALHO"]);
            $ag->setPostoTrabalho($pt);
            
            $se = new setor();
            $se->setCodigo($ret[$i]["CODIGOSETOR"]);
            $se->setNome($ret[$i]["NOMESETOR"]);
            $ag->setSetor($se);
            
            $fu1 = new funcao();
            $fu1->setCodigo($ret[$i]["CODIGOFUNCAO"]);
            $fu1->setNome($ret[$i]["NOMEFUNCAO"]);
            $ag->setFuncao($fu1);
            
            $se1 = new setor();
            $se1->setCodigo($ret[$i]["NOVO_SETOR"]);
            $se1->setNome($ret[$i]["NOMENOVOSETOR"]);
            $ag->setNovoSetor($se1);
            
            $fu2 = new funcao();
            $fu2->setCodigo($ret[$i]["NOVA_FUNCAO"]);
            $fu2->setNome($ret[$i]["NOMENOVAFUNCAO"]);
            $ag->setNovaFuncao($fu2);

            array_push($this->lista, $ag);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function buscarHorariosLivres($filtro = null, $limite = null, $ordem = null){
        $select = array("A.CODIGOUNIDADE", "A.CODIGO", "A.DATA", "A.HORA", 
                        "B.CODIGOMEDICO", "C.NOME");
        
        $filtro["COALESCE(A.CODIGOEMPRESA, '')"] = "";
        $filtro["A.NOMEEMPRESA"]      = array("NULL", "IS");
        $filtro["A.CODIGOPESSOA"]     = array("NULL", "IS");
        $filtro["A.NOMEPESSOA"]       = array("NULL", "IS");
        $filtro["A.CONTROLE_USUARIO"] = array("NULL", "IS");
        
        $join = "JOIN T_AGENDA B ON B.CODIGOUNIDADE = A.CODIGOUNIDADE ".
                "               AND B.CODIGO        = A.CODIGO        ".
                "JOIN T_MEDICO C ON C.CODIGO        = B.CODIGOMEDICO  ";
        
        $fb = new FB("T_AGENDAITEM A");

        $ret = $fb->select($filtro, null, $ordem, $join, $select);        
        $this->totalRegistros = count($ret);

        $ret = $fb->select($filtro, null, array("A.HORA" => "ASC"), $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $ag = new agenda();
            $ag->setCodigo($ret[$i]["CODIGO"]);
            $ag->setData($ret[$i]["DATA"]);
            $ag->setHora($ret[$i]["HORA"]);
           
            $un = new unidade();
            $un->setCodigo($ret[$i]["CODIGOUNIDADE"]);
            $ag->setUnidade($un);

            $me = new medico();
            $me->setCodigo($ret[$i]["CODIGOMEDICO"]);
            $me->setNome($ret[$i]["NOME"]);
            $ag->setMedico($me);
            
            array_push($this->lista, $ag);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }    
    
    function buscarLimiteHorario($matricula, $data, $tipo, $medico, &$horarioMinManha, &$horarioMaxManha, &$horarioMinTarde, &$horarioMaxTarde,
                                 $novosetor, $novafuncao){
        $fb = new FB("SP_AGENDACARREGAHORARIOS('{$_SESSION[config::getSessao()]["unidade"]}', "
                                            . "'{$_SESSION[config::getSessao()]["empresa_ativa"]}', "
                                            . "'{$matricula}', '{$data}', '{$tipo}', '{$medico}', '{$novosetor}', '{$novafuncao}')");
                                            
        $ret = $fb->select();
        if (count($ret) > 0){
            $horarioMinManha = $ret[0]["HORARIOMINMANHA"];
            $horarioMaxManha = $ret[0]["HORARIOMAXMANHA"];
            $horarioMinTarde = $ret[0]["HORARIOMINTARDE"];
            $horarioMaxTarde = $ret[0]["HORARIOMAXTARDE"];
        }else{
            $horarioMinManha = '00:00:00';
            $horarioMaxManha = '00:00:00';
            $horarioMinTarde = '00:00:00';
            $horarioMaxTarde = '00:00:00';
        }
    }
    
    function buscarHorarioMarcado($unidade, $codigo, $hora){
        $filtro["A.CODIGOUNIDADE"]    = $unidade;
        $filtro["A.CODIGO"]           = $codigo;
        $filtro["A.HORA"]             = $hora;
        $filtro["A.CODIGOEMPRESA"]    = array("NULL", "IS");
        $filtro["A.NOMEEMPRESA"]      = array("NULL", "IS");
        $filtro["A.CODIGOPESSOA"]     = array("NULL", "IS");
        $filtro["A.NOMEPESSOA"]       = array("NULL", "IS");
        $filtro["A.CONTROLE_USUARIO"] = "S";
        
        $fb = new FB("T_AGENDAITEM A");
        $ret = $fb->select($filtro);
        $this->totalRegistros = count($ret);

        return $this->totalRegistros > 0;
    }   
    
    function buscarDataAgenda($unidade, $codigo){
        $fb = new FB("T_AGENDA A");
        $ret = $fb->select(array("A.CODIGOUNIDADE" => $unidade,
                                 "A.CODIGO"        => $codigo), 1);
        
        if (count($ret) > 0){
            $this->setData($ret[0]["DATA"]);
            return true;
        }else
            return false;
    }
    
    function buscarEstatisticas($filtro = null){
        $select = array("COUNT(*) AS QTDE", "A.DATA");        
        //$join = "JOIN T_AGENDA B ON B.CODIGOUNIDADE = A.CODIGOUNIDADE ".
//                "               AND B.CODIGO        = A.CODIGO        ";
        
        $fb = new FB("T_AGENDAITEM A");
        $ret = $fb->select($filtro, null, null, null, $select, array("A.DATA"));        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, null, null, null, $select, array("A.DATA"));
        for ($i = 0; $i < count($ret); $i++){
            $ag = new agenda();
            $ag->setData($ret[$i]["DATA"]);
            $ag->setQuantidade($ret[$i]["QTDE"]);
            array_push($this->lista, $ag);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }    
    
    function marcarControle($unidade, $codigo, $hora){
        $valores["CONTROLE_USUARIO"] = "S";
        $valores["TIMEST"] = date("Y-m-d H:i:s");
        
        $where["CODIGOUNIDADE"] = $unidade;
        $where["CODIGO"]        = $codigo;
        $where["HORA"]          = $hora;
        $where["CODIGOEMPRESA"]    = array("NULL", "IS");
        $where["NOMEEMPRESA"]      = array("NULL", "IS");
        $where["CODIGOPESSOA"]     = array("NULL", "IS");
        $where["NOMEPESSOA"]       = array("NULL", "IS");
        $where["CONTROLE_USUARIO"] = array("NULL", "IS");
        
        $fb = new FB("T_AGENDAITEM");
        return $fb->update($valores, $where);      
    }
    
    function desmarcarControle($unidade, $codigo, $hora){
        $valores["CONTROLE_USUARIO"] = "NULL";
        $valores["TIMEST"]           = "";
        
        $where["CODIGOUNIDADE"] = $unidade;
        $where["CODIGO"]        = $codigo;
        $where["HORA"]          = $hora;
        $where["CODIGOEMPRESA"]    = array("NULL", "IS");
        $where["NOMEEMPRESA"]      = array("NULL", "IS");
        $where["CODIGOPESSOA"]     = array("NULL", "IS");
        $where["NOMEPESSOA"]       = array("NULL", "IS");
        $where["CONTROLE_USUARIO"] = "S";
        
        $fb = new FB("T_AGENDAITEM");
        return $fb->update($valores, $where);      
    }    
    
    function agendarHorario($val, $unidade, $codigo, $hora){

        if (strlen($val["NOMEEMPRESA"]) > 60)
            $val["NOMEEMPRESA"] = substr($val["NOMEEMPRESA"], 0, 60);
        if (strlen($val["DESCRICAOPOSTOTRABALHO"]) > 60)
            $val["DESCRICAOPOSTOTRABALHO"] = (substr($val["DESCRICAOPOSTOTRABALHO"], 0, 60));
        
        $fb = new FB("T_AGENDAITEM");
        return $fb->update($val, array("CODIGOUNIDADE" => $unidade,
                                       "CODIGO"        => $codigo,
                                       "HORA"          => $hora), false);
    }
    
    function liberarHorario($unidade, $codigo, $hora){
        $val = array("CODIGOEMPRESA" => "NULL",
                     "NOMEEMPRESA"   => "NULL",
                     "CODIGOPESSOA"  => "NULL",
                     "NOMEPESSOA"    => "NULL",
                     "DDD"           => "NULL",
                     "FONE"          => "NULL",
                     "TIPO"          => "NULL",
                     "CODIGOSETOR"   => "NULL",
                     "NOMESETOR"     => "NULL",
                     "CODIGOFUNCAO"  => "NULL",
                     "NOMEFUNCAO"    => "NULL",
                     "USUARIOAGENDAMENTOWEB"  => "NULL",
                     "NOMEUSUAGENDAMENTO"     => "NULL",
                     "DATAAGENDAMENTO"        => "NULL",
                     "HORAAGENDAMENTO"        => "NULL",
                     "CONTROLE_USUARIO"       => "NULL",
                     "RESPONSAVELAGENDAMENTO" => "NULL",
                     "CODIGOPOSTOTRABALHO"    => "NULL",
                     "DESCRICAOPOSTOTRABALHO" => "NULL",
                     "WEB"                    => "NULL",
                     "NOVO_SETOR"             => "NULL",
                     "NOVA_FUNCAO"            => "NULL");

       $fb = new FB("T_AGENDAITEM");
       return $fb->update($val, array("CODIGOUNIDADE" => $unidade,
                                       "CODIGO"        => $codigo,
                                       "HORA"          => $hora));
    }
    
    function cancelarAgendamento($unidade, $codigo, $hora, $motivo){
        
    }
    
    function remover(){
        
    }
    function alterar(){
        
    }
    function inserir(){
        
    }
}
