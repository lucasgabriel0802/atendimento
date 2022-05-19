<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("setor.class.php");
require_once("funcao.class.php");
require_once("postotrabalho.class.php");

class funcionario implements iface {
    private $codigo   = "";
    private $nome     = "";
    private $telefone = "";
    private $setor    = "";
    private $funcao   = "";
    private $postotrabalho = "";
    private $rg            = "";
    private $cpf           = "";
    private $ctps          = "";
    private $pis           = "";
    private $dataadmissao  = "";
    private $datademissao  = "";
    private $datacadastro  = "";
    private $datanascimento  = "";
    private $ultnumaso     = "";
    private $nomemae     = "";
    private $sexo     = "";
    private $estadocivil     = "";
    private $ctpsnumero     = "";
    private $ctpsserie     = "";
    private $ctpsuf     = "";
    private $matriculaESocial = "";
    private $codigoMatriculaESocial = "";
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;
    
    function getCodigo(){ return $this->codigo; }
    function getNome(){ return $this->nome; }
    function getTelefone(){ return $this->telefone; }
    function getSetor(){ return $this->setor; }
    function getFuncao(){ return $this->funcao; }
    function getPostoTrabalho(){ return $this->postotrabalho; }
    function getRG(){ return $this->rg; }
    function getCPF(){ return $this->cpf; }
    function getCTPS(){ return $this->ctps; }
    function getPIS(){ return $this->pis; }
    function getDataAdmissao(){ return $this->dataadmissao; }
    function getDataDemissao(){ return $this->datademissao; }
    function getDataCadastro(){ return $this->datacadastro; }
    function getDataNascimento(){ return  date("d/m/Y", strtotime($this->datanascimento)); }
    function getUltNumeroASO(){ return $this->ultnumaso; }
    function getSexo(){ return $this->sexo; }
    function getSexoCAT(){ 
        if ($this->sexo == "M")
            return '1'; 
        else
            return '3'; 
    }
    function getEstadoCivilCAT(){ 
        if ($this->estadocivil == "S")
            return '1'; 
        else
            if ($this->estadocivil == "C")
                return '2'; 
            else
                if ($this->estadocivil == "D")
                    return '4'; 
                else
                    if ($this->estadocivil == "V")
                        return '3'; 
                    else
                        if ($this->estadocivil == "O")
                            return '5'; 
    }

    function getCTPSNumero(){ return $this->ctpsnumero; }
    function getCTPSSerie(){ return $this->ctpsserie; }
    function getCTPSUF(){ return $this->ctpsuf; }

    function getDDD(){
        $temp = explode(" ", $this->getTelefone());
        if (count($temp) > 1)
            return str_replace("(", "", str_replace(")", "", $temp[0]));
        else
            return 0;
    }
    function getTelefoneSemDDD(){
        $temp = explode(" ", $this->getTelefone());
        if (count($temp) == 1)
            return $temp[0];
        else
            if (count($temp) > 1)
                return $temp[1];
            else
                return "";
    }
    
    function getNomeMae(){ return $this->nomemae; }
    function getMatriculaESocial(){ return $this->matriculaESocial; }
    function getCodigoCategoriaESocial(){ return $this->codigoMatriculaESocial; }

    function setCodigo($valor){ $this->codigo = $valor; }
    function setNome($valor){ $this->nome = $valor; }
    function setTelefone($valor){ $this->telefone = $valor; }
    function setSetor($valor){ $this->setor = $valor; }
    function setFuncao($valor){ $this->funcao = $valor; }
    function setPostoTrabalho($valor){ $this->postotrabalho = $valor; }
    function setRG($valor){ $this->rg = $valor; }
    function setCPF($valor){ $this->cpf = $valor; }
    function setCTPS($valor){ $this->ctps = $valor; }
    function setPIS($valor){ $this->pis = $valor; }
    function setDataAdmissao($valor){ $this->dataadmissao = $valor; }
    function setDataDemissao($valor){ $this->datademissao = $valor; }
    function setDataCadastro($valor){ $this->datacadastro = $valor; }
    function setDataNascimento($valor){ $this->datanascimento = $valor; }
    function setUltNumeroASO($valor){ $this->ultnumaso = $valor; }
    function setNomeMae($valor){ $this->nomemae = $valor; }
    function setSexo($valor){ $this->sexo = $valor; }
    function setEstadoCivil($valor){ $this->estadocivil = $valor; }
    
    function setCTPSNumero($valor){ $this->ctpsnumero = $valor; }
    function setCTPSSerie($valor){ $this->ctpsserie = $valor; }
    function setCTPSUF($valor){ $this->ctpsuf = $valor; }
    function setMatriculaESocial($valor){ $this->matriculaESocial = $valor; }
    function setCodigoCategoriaESocial($valor){ $this->codigoMatriculaESocial = $valor; }
    
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
            return new funcionario();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null, $apenasTotal = false){
        $select = array("A.MATRICULA", "A.NOME", "A.DDDFONE1", "A.PREFONE1", "A.NUMFONE1", 
                        "A.SETOR", "UPPER(B.NOME) AS NOMESETOR", "A.FUNCAO", 
                        "UPPER(C.NOME) AS NOMEFUNCAO", "B.POSTOTRABALHO",
                        "UPPER(D.DESCRICAO) AS DESCPOSTOTRABALHO", 
                        "A.NUMERO_RG", "A.CTPS_NUMERO", "A.CTPS_SERIE", "A.CTPS_UF", 
                        "A.PISPASEPCEI", "A.DATAADMISSAO", "A.ULTNUMASO",
                        "A.DATADEMISSAO", "A.DATACADASTRO", "A.CPF", "A.NOME_MAE", "A.DATANASCIMENTO",
                        "A.SEXO", "A.ESTADOCIVIL", "A.CTPS_NUMERO", "A.CTPS_SERIE", "A.CTPS_UF",
                        "A.MATRICULAESOCIAL", "A.COD_CATEGORIA_TABELA01");
        
        $join = "JOIN T_SETOR B ON B.UNIDADE = A.UNIDADE             "
               ."              AND B.EMPRESA = A.EMPRESA             "
               ."              AND B.CODIGO  = A.SETOR               "
               ."              AND B.POSTOTRABALHO = A.POSTOTRABALHO "
               ."JOIN T_FUNCAO C ON C.UNIDADE = A.UNIDADE            "
               ."               AND C.EMPRESA = A.EMPRESA            "
               ."               AND C.CODIGO  = A.FUNCAO             "
               ."JOIN T_EMPPOSTOTRABALHO D ON D.UNIDADE = A.UNIDADE  "
               ."                         AND D.EMPRESA = A.EMPRESA  "
               ."                         AND D.CODIGO  = A.POSTOTRABALHO";
        
        $fb   = new FB("T_FUNCIONARIO A");
        $ret = $fb->select($filtro, null, null, $join, $select);        
        $this->totalRegistros = count($ret);
        
        if (!$apenasTotal){ // se true calcula apenas o total de registros e não passa pelo laço abaixo
            $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
            for ($i = 0; $i < count($ret); $i++){
                $f = new funcionario();
                $f->setCodigo($ret[$i]["MATRICULA"]);
                $f->setNome($ret[$i]["NOME"]);

                $tel = "";
                if (str_pad($ret[$i]["DDDFONE1"], 3, "0", STR_PAD_LEFT) != "000")
                    $tel = "(".trim($ret[$i]["DDDFONE1"]).")";

                if ((str_pad($ret[$i]["PREFONE1"], 4, "0", STR_PAD_LEFT) != "0000") && 
                        (str_pad($ret[$i]["NUMFONE1"], 4, "0", STR_PAD_LEFT) != "0000")){
                    if ($tel != "")
                        $tel .= " ";
                    $tel .= $ret[$i]["PREFONE1"].$ret[$i]["NUMFONE1"];
                }

                $f->setTelefone($tel);
                $f->setRG($ret[$i]["NUMERO_RG"]);
                $f->setCPF($ret[$i]["CPF"]);
                $f->setCTPS($ret[$i]["CTPS_NUMERO"]."/".$ret[$i]["CTPS_SERIE"]."-".$ret[$i]["CTPS_UF"]);
                $f->setPIS($ret[$i]["PISPASEPCEI"]);
                $f->setDataAdmissao($ret[$i]["DATAADMISSAO"]);
                $f->setDataDemissao($ret[$i]["DATADEMISSAO"]);
                $f->setDataCadastro($ret[$i]["DATACADASTRO"]);
                $f->setDataNascimento($ret[$i]["DATANASCIMENTO"]);
                $f->setUltNumeroASO($ret[$i]["ULTNUMASO"]);
                $f->setSexo($ret[$i]["SEXO"]);
                $f->setEstadoCivil($ret[$i]["ESTADOCIVIL"]);
                $f->setCTPSNumero($ret[$i]["CTPS_NUMERO"]);
                $f->setCTPSSerie($ret[$i]["CTPS_SERIE"]);
                $f->setCTPSUF($ret[$i]["CTPS_UF"]);

                $fu = new funcao();
                $fu->setCodigo($ret[$i]["FUNCAO"]);
                $fu->setNome($ret[$i]["NOMEFUNCAO"]);
                $f->setFuncao($fu);

                $se = new setor();
                $se->setCodigo($ret[$i]["SETOR"]);
                $se->setNome($ret[$i]["NOMESETOR"]);
                $f->setSetor($se);

                $pt = new postotrabalho();
                $pt->setCodigo($ret[$i]["POSTOTRABALHO"]);
                $pt->setDescricao($ret[$i]["DESCPOSTOTRABALHO"]);
                $f->setPostoTrabalho($pt);

                $f->setNomeMae($ret[$i]["NOME_MAE"]);
                $f->setMatriculaESocial($ret[$i]["MATRICULAESOCIAL"]);
                $f->setCodigoCategoriaESocial($ret[$i]["COD_CATEGORIA_TABELA01"]);
                

                array_push($this->lista, $f);
            }       
        }
        $this->totalLista = count($this->lista);
        if ($apenasTotal)
            return $this->totalRegistros > 0;
        else
            return $this->totalLista > 0;
    }
    
    function remover($unidade = null, $empresa = null, $matricula = null){
        $fb   = new FB("T_FUNCIONARIO");
        return $fb->delete([
                    "UNIDADE"   => $unidade,
                    "EMPRESA"   => $empresa,
                    "MATRICULA" => $matricula
                ]);
    }
    
    function alterar($val = null, $where = null){
        if ($val != null && $where != null){
            if (is_array($val) && is_array($where)){
                $fb = new FB("T_FUNCIONARIO");
                
                return $fb->update($val, $where);
            }else
                return false;
        }else
            return false;
    }
    
    function inserir($val = null){
        if ($val != null){
            if (is_array($val)){
                $fb = new FB("T_FUNCIONARIO");
                $val["MATRICULA"] = str_pad($fb->proximoCodigo($val["UNIDADE"], $val["EMPRESA"], "FUNCIO"), 6, "0", STR_PAD_LEFT);
                
                $val["ULTNUMASO"] = 0;
                $val["SITUACAO"] = "ATIVO";
                if (!isset($val["TIPODEFICIENCIA"]) || ($val["TIPODEFICIENCIA"] == ""))
                    $val["TIPODEFICIENCIA"] = 0;
                
                if ($fb->insert($val)){
                    $this->setCodigo($val["MATRICULA"]);
                    return true;
                }else
                    return false;
            }else
                return false;
        }else
            return false;                
    }
}
