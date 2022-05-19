<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("unidade.class.php");
require_once("empresa.class.php");

class usuarios implements iface {
    private $unidade, $empresa, $codigo, $usuarioweb, $senhaweb, 
            $tipocadastro, $tipousuario, $email, $acessofaturas,
            $acessodocumentos, $acessoesocial, $acessointerno,
            $permitegerarfichamedica, $permitegerarfichamedicaocp,
            $permitegeraracuidadevisual, $permitegeraraudiometria,
            $permitegerarkit;
    
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
    function getCodigo(){ return $this->codigo; }
    function getUsuarioWeb(){ return $this->usuarioweb; }
    function getSenhaWeb(){ return $this->senhaweb; }
    function getTipoCadastro(){ return $this->tipocadastro; }
    function getTipoUsuario(){ return $this->tipousuario; }
    function getEmail(){ return $this->email; }
    function getAcessoFaturas(){ return $this->acessofaturas; }
    function getAcessoDocumentos(){ return $this->acessodocumentos; }
    function getAcessoESocial(){ return $this->acessoesocial; }
    function getAcessoInterno(){ return $this->acessointerno; }
    function getPermiteGerarFichaMedica(){ return $this->permitegerarfichamedica; }
    function getPermiteGerarFichaMedicaOcp(){ return $this->permitegerarfichamedicaocp; }
    function getPermiteGerarAcuidadeVisual(){ return $this->permitegeraracuidadevisual; }
    function getPermiteGerarAudiometria(){ return $this->permitegeraraudiometria; }
    function getPermiteGerarKIT(){ return $this->permitegerarkit; }
    
    function setUnidade($valor){ $this->unidade = $valor; }
    function setEmpresa($valor){ $this->empresa = $valor; }
    function setCodigo($valor){ $this->codigo = $valor; }
    function setUsuarioWeb($valor){ $this->usuarioweb = $valor; }
    function setSenhaWeb($valor){ $this->senhaweb = $valor; }
    function setTipoCadastro($valor){ $this->tipocadastro = $valor; }
    function setTipoUsuario($valor){ $this->tipousuario = $valor; }
    function setEmail($valor){ $this->email = $valor; }
    function setAcessoFaturas($valor){ $this->acessofaturas = $valor; }
    function setAcessoDocumentos($valor){ $this->acessodocumentos = $valor; }
    function setAcessoESocial($valor){ $this->acessoesocial = $valor; }
    function setAcessoInterno($valor){ $this->acessointerno = $valor; }
    function setPermiteGerarFichaMedica($valor){ $this->permitegerarfichamedica = $valor; }
    function setPermiteGerarFichaMedicaOcp($valor){ $this->permitegerarfichamedicaocp = $valor; }
    function setPermiteGerarAcuidadeVisual($valor){ $this->permitegeraracuidadevisual = $valor; }
    function setPermiteGerarAudiometria($valor){ $this->permitegeraraudiometria = $valor; }
    function setPermiteGerarKit($valor){ $this->permitegerarkit = $valor; }
    
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
            return new usuarios();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.CODIGO", "A.USUARIOWEB", "A.SENHAWEB",
                        "A.TIPO_CADASTRO", "A.TIPO_USUARIO", "A.EMAIL", "A.ACESSO_FATURAS",
                        "A.ACESSO_DOCUMENTOS", "A.ACESSO_ESOCIAL", "B.RAZAOSOCIAL AS RAZAOSOCIAL_U", 
                        "B.NOMEFANTASIA AS NOMEFANTASIA_U", "B.CNPJ AS CNPJ_U", "B.INSCREST AS IE_U",
                        "C.RAZAOSOCIAL AS RAZAOSOCIAL_E", "C.NOMEFANTASIA AS NOMEFANTASIA_E",
                        "C.CNPJCEICPF AS CNPJCPF_E", "C.INSCREST AS IE_E", "C.RESCISAOCONTRATO",
                        "A.ACESSO_INTERNO", "A.PERMITEGERARFICHAMEDICA", "A.PERMITEGERARFICHAMEDICAOCP",
                        "A.PERMITEGERARACUIDADEVISUAL", "A.PERMITEGERARAUDIOMETRIA", "A.PERMITEGERARKIT");
        
        $join   = "JOIN T_UNIDADE B ON B.CODIGO = A.UNIDADE ".
                  "JOIN T_EMPRESA C ON C.CODIGO = A.EMPRESA AND C.UNIDADE = A.UNIDADE";
        $fb     = new FB("T_USERWEB A");
        $ret    = $fb->select($filtro, null, null, $join, $select);
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        for ($i = 0; $i < count($ret); $i++){
            $u = new usuarios();
            $u->setCodigo($ret[$i]["CODIGO"]);
            $u->setEmail($ret[$i]["EMAIL"]);
            $u->setSenhaWeb($ret[$i]["SENHAWEB"]);
            $u->setTipoCadastro($ret[$i]["TIPO_CADASTRO"]);
            $u->setTipoUsuario($ret[$i]["TIPO_USUARIO"]);
            $u->setUsuarioWeb($ret[$i]["USUARIOWEB"]);
            $u->setAcessoFaturas($ret[$i]["ACESSO_FATURAS"]);
            $u->setAcessoDocumentos($ret[$i]["ACESSO_DOCUMENTOS"]);
            $u->setAcessoESocial($ret[$i]["ACESSO_ESOCIAL"]);
            $u->setAcessoInterno($ret[$i]["ACESSO_INTERNO"]);
            $u->setPermiteGerarFichaMedica($ret[$i]["PERMITEGERARFICHAMEDICA"]);
            $u->setPermiteGerarFichaMedicaOcp($ret[$i]["PERMITEGERARFICHAMEDICAOCP"]);
            $u->setPermiteGerarAcuidadeVisual($ret[$i]["PERMITEGERARACUIDADEVISUAL"]);
            $u->setPermiteGerarAudiometria($ret[$i]["PERMITEGERARAUDIOMETRIA"]);
            $u->setPermiteGerarkIT($ret[$i]["PERMITEGERARKIT"]);
            
            $un = new unidade();
            $un->setCodigo($ret[$i]["UNIDADE"]);
            $un->setRazaoSocial($ret[$i]["RAZAOSOCIAL_U"]);
            $un->setNomeFantasia($ret[$i]["NOMEFANTASIA_U"]);
            $un->setCNPJ($ret[$i]["CNPJ_U"]);
            $un->setIE($ret[$i]["IE_U"]);
            $u->setUnidade($un);
            
            $em = new empresa();
            $em->setCodigo($ret[$i]["EMPRESA"]);
            $em->setRazaoSocial($ret[$i]["RAZAOSOCIAL_E"]);
            $em->setNomeFantasia($ret[$i]["NOMEFANTASIA_E"]);
            $em->setCNPJCPF($ret[$i]["CNPJCPF_E"]);
            $em->setIERG($ret[$i]["IE_E"]);
            $em->setDataRescisao($ret[$i]["RESCISAOCONTRATO"]);
            $u->setEmpresa($em);
            
            array_push($this->lista, $u);
        }       
        
        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }
    
    function alterarSenha($unidade, $empresa, $codigo, $senha){
        $fb = new FB("T_USERWEB");
        $values = array("SENHAWEB" => $senha);
        $where  = array("UNIDADE" => $unidade,
                        "EMPRESA" => $empresa,
                        "CODIGO"  => $codigo);
        
        return $fb->update($values, $where);
    }
    
    function remover(){
        
    }
    function alterar(){
        
    }
    function inserir(){
        
    }
}
