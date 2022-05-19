<?php

    //  error_reporting(E_ALL);
    //  ini_set('display_errors', 'On');

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("funcionario.class.php");
require_once("criptografia.class.php");
require_once("funcoes.class.php");
require_once("empresa.class.php");
require_once("class/funcoes.class.php");

//require_once 'C:/PHP_FONTES/atendimento/ocupacional_saudevital/vendor/autoload.php';
require_once ('./vendor/autoload.php');

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class registrocat implements iface {
    private $unidade        = "";
    private $empresa        = "";
    private $numero         = 0;
    private $funcionario    = "";
    private $localemissao   = "";
    private $dataemissao    = null;
    private $emitente       = "";
    private $tipocat        = "";
    private $datacomunobito = null;
    private $unidadetrabalho  = "";
    private $numerocat      = "";
    private $dataregistro   = null;
    private $nomefuncionario  = "";
    private $datanascimento  = null;
    private $sexo  = "";
    private $estadocivil  = "";
    private $ctpsnumero  = "";
    private $ctpsserie  = "";
    private $ctpsdata  = null;
    private $ctpsuf  = "";
    private $remuneracao  = 0.00;
    private $rgnumero  = "";
    private $rgdataemissao  = null;
    private $rgemissor  = "";
    private $rguf  = "";
    private $pispasep  = "";
    private $endcep  = "";
    private $endereco = "";
    private $endnumero  = "";
    private $bairro  = "";
    private $cidade  = "";
    private $estado  = "";
    private $dddfone  = "";
    private $prefone  = "";
    private $numfone  = "";
    private $ocupacao  = "";
    private $codigocbo  = "";
    private $filiacaops  = "";
    private $aposentado  = "";
    private $areas  = "";
    private $dataacidente  = null;
    private $horaacidente  = null;
    private $qtdehorastrabalho  = null;
    private $tipoacidente  = "";
    private $houveafastamento  = "";
    private $ultimodiatrab  = null;
    private $localacidente  = "";
    private $localespecifica  = "";
    private $localcnpj  = "";
    private $localcidade  = "";
    private $localbairro  = "";
    private $localuf  = "";
    private $partesatingidas  = "";
    private $agentecausador  = "";
    private $situacao  = "";
    private $registropolicial  = "";
    private $houvemorte  = "";
    private $testem1_nome  = "";
    private $testem1_cep  = "";
    private $testem1_endereco  = "";
    private $testem1_endnumer  = "";
    private $testem1_endcompl  = "";
    private $testem1_bairro  = "";
    private $testem1_cidade  = "";
    private $testem1_estado  = "";
    private $testem1_dddfone  = "";
    private $testem1_prefone  = "";
    private $testem1_numfone  = "";
    private $testem2_nome  = "";
    private $testem2_cep  = "";
    private $testem2_endereco  = "";
    private $testem2_endnumer  = "";
    private $testem2_endcompl  = "";
    private $testem2_bairro  = "";
    private $testem2_cidade  = "";
    private $testem2_estado  = "";
    private $testem2_dddfone  = "";
    private $testem2_prefone  = "";
    private $testem2_numfone  = "";
    private $tpacidente_esocial = "";
    private $situacaogeradora_cat = "";
    private $codigoibge_cidade = "";
    private $codigoagentecausador = "";
    private $codigonaturezalesao = "";
    private $codigocid10 = "";
    private $tipoinscricaoregistrador = 0;
    private $cnpjregistrador = "";
    private $iniciativacat = 0;
    private $descricaologradouro = "";
    private $numerologradouro = 0;
    private $ceplocalacidente = "";
    private $atestado_cnes = "";
    private $atestado_data = null;
    private $atestado_hora = null;
    private $atestado_internacao = "";
    private $atestado_duracaodias = 0;
    private $atestado_diagnostico = "";
    private $atestado_nomeemitente = "";
    private $atestado_orgaoclasse = "";
    private $atestado_numeroinscricao = "";
    private $atestado_uforgaoclasse = "";
    private $data_cat_origem = null;
    private $numero_cat_origem = "";
    private $observacao = "";
    private $codigoparteatingida = "";
    private $codigolateralidade = "";
    private $descricaocomplementarlesao = "";
    private $atestado_observacao = "";

    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getCodigo(){ return $this->codigo;}
    function getUnidade(){ return $this->unidade;}
    function getEmpresa(){ return $this->empresa;}
    function getNumero(){ return $this->numero;}
    function getFuncionario() { return $this->funcionario;}
    function getLocalEmissao(){ return $this->localemissao;}
    function getDataEmissao(){ return $this->dataemissao;}
    function getEmitente(){ return $this->emitente;}
    function getTipoCAT(){ return $this->tipocat;}
    function getDataComunObito(){ return $this->datacomunobito;}
    function getUnidadeTrabalho(){ return $this->unidadetrabalho;}
    function getNumeroCAT(){ return $this->numerocat;}
    function getDataRegistro(){ return $this->dataregistro;}
    function getNomeFuncionario(){ return $this->nomefuncionario;}
    function getDataNascimento(){ return $this->datanascimento;}
    function getSexo(){ return $this->sexo;}
    function getEstadocivil(){ return $this->estadocivil;}
    function getCtpsNumero(){ return $this->ctpsnumero;}
    function getCtpsSerie(){ return $this->ctpsserie;}
    function getCtpsData(){ return $this->ctpsdata;}
    function getCtpsUF(){ return $this->ctpsuf;}
    function getRemuneracao(){ return $this->remuneracao;}
    function getRgNumero(){ return $this->rgnumero;}
    function getRgDataEmissao(){ return $this->rgdataemissao;}
    function getRgEmissor(){ return $this->rgemissor;}
    function getRgUf(){ return $this->rguf;}
    function getPisPasep(){ return $this->pispasep;}
    function getEndCep(){ return $this->endcep;}
    function getEndereco(){ return $this->endereco;}
    function getEndNumero(){ return $this->endnumero;}
    function getBairro(){ return $this->bairro;}
    function getCidade(){ return $this->cidade;}
    function getEstado(){ return $this->estado;}
    function getDddFone(){ return $this->dddfone;}
    function getPreFone(){ return $this->prefone;}
    function getNumFone(){ return $this->numfone;}
    function getOcupacao(){ return $this->ocupacao;}
    function getCodigoCbo(){ return $this->codigocbo;}
    function getFiliacaoPs(){ return $this->filiacaops;}
    function getAposentado(){ return $this->aposentado;}
    function getAreas(){ return $this->areas;}
    function getDataAcidente(){ return $this->dataacidente;}
    function getHoraAcidente(){ return $this->horaacidente;}
    function getQtdeHorasTrabalho(){ return $this->qtdehorastrabalho;}
    function getTipoAcidente(){ return $this->tipoacidente;}
    function getHouveAfastamento(){ return $this->houveafastamento;}
    function getUltimoDiaTrab(){ return $this->ultimodiatrab;}
    function getLocalAcidente(){ return $this->localacidente;}
    function getLocalEspecifica(){ return $this->localespecifica;}
    function getLocalCnpj(){ return $this->localcnpj;}
    function getLocalCidade(){ return $this->localcidade;}
    function getLocalBairro(){ return $this->localbairro;}
    function getLocalUf(){ return $this->localuf;}
    function getPartesAtingidas(){ return $this->partesatingidas;}
    function getAgenteCausador(){ return $this->agentecausador;}
    function getSituacao(){ return $this->situacao;}
    function getRegistroPolicial(){ return $this->registropolicial;}
    function getHouveMorte(){ return $this->houvemorte;}
    function getTestem1_Nome(){ return $this->testem1_nome;}
    function getTestem1_Cep(){ return $this->testem2_cep;}
    function getTestem1_Endereco(){ return $this->testem1_endereco;}
    function getTestem1_EndNumer(){ return $this->testem1_endnumer;}
    function getTestem1_EndCompl(){ return $this->testem1_endcompl;}
    function getTestem1_Bairro(){ return $this->testem1_bairro;}
    function getTestem1_Cidade(){ return $this->testem1_cidade;}
    function getTestem1_Estado(){ return $this->testem1_estado;}
    function getTestem1_DddFone(){ return $this->testem1_dddfone;}
    function getTestem1_PreFone(){ return $this->testem1_prefone;}
    function getTestem1_NumFone(){ return $this->testem1_numfone;}
    function getTestem2_Nome(){ return $this->testem2_nome;}
    function getTestem2_Cep(){ return $this->testem2_cep;}
    function getTestem2_Endereco(){ return $this->testem2_endereco;}
    function getTestem2_EndNumer(){ return $this->testem2_endnumer;}
    function getTestem2_EndCompl(){ return $this->testem2_endcompl;}
    function getTestem2_Bairro(){ return $this->testem2_bairro;}
    function getTestem2_Cidade(){ return $this->testem2_cidade;}
    function getTestem2_Estado(){ return $this->testem2_estado;}
    function getTestem2_DddFone(){ return $this->testem2_dddfone;}
    function getTestem2_PreFone(){ return $this->testem2_prefone;}
    function getTestem2_NumFone(){ return $this->testem2_numfone;}
    function getTipoAcidente_ESocial(){ return $this->tpacidente_esocial ;}
    function getSituacaoGeradoraCat(){ return $this->situacaogeradora_cat ;}
    function getCodigoIBGE_Cidade(){ return $this->codigoibge_cidade ;}
    function getCodigoAgenteCausador(){ return $this->codigoagentecausador ;}
    function getCodigoNaturezaLesao(){ return $this->codigonaturezalesao ;}
    function getCodigoCid10(){ return $this->codigocid10 ;}
    function getTipoInscricaoRegistrador(){ return $this->tipoinscricaoregistrador ;}
    function getCnpjRegistrador(){ return $this->cnpjregistrador ;}
    function getIniciativaCat(){ return $this->iniciativacat ;}
    function getDescricaoLogradouro(){ return $this->descricaologradouro ;}
    function getNumeroLogradouro(){ return $this->numerologradouro ;}
    function getCepLocalAcidente(){ return $this->ceplocalacidente ;}
    function getAtestadoCnes(){ return $this->atestado_cnes ;}
    function getAtestadoData(){ return $this->atestado_data;}
    function getAtestadoHora(){ return $this->atestado_hora;}
    function getAtestadoInsternacao(){ return $this->atestado_internacao ;}
    function getAtestadoDuracaoDias(){ return $this->atestado_duracaodias ;}
    function getAtestadoDiagnostico(){ return $this->atestado_diagnostico ;}
    function getAtestadoNomeEmitente(){ return $this->atestado_nomeemitente ;}
    function getAtestadoOrgaoClasse(){ return $this->atestado_orgaoclasse ;}
    function getAtestadoNumeroInscricao(){ return $this->atestado_numeroinscricao ;}
    function getAtestadoUfOrgaoClasse(){ return $this->atestado_uforgaoclasse ;}
    function getDataCatOrigem(){ return $this->data_cat_origem;}
    function getNumeroCatOrigem(){ return $this->numero_cat_origem;}
    function getObservacao(){ return $this->observacao;}
    function getCodigoParteAtingida(){ return $this->codigoparteatingida;}
    function getCodigoLateralidade(){ return $this->codigolateralidade;}
    function getDescricaoComplementarLesao(){ return $this->descricaocomplementarlesao;}
    function getAtestadoObservacao(){ return $this->atestado_observacao;}
    
    function getIntervaloCancelamento(){
        if (is_numeric($this->numero_cat_origem))
            return $this->numero_cat_origem;
        else
            return 0;
    }
    

    function setCodigo($valor){ $this->codigo = $valor;}
    function setUnidade($valor){ $this->unidade = $valor;}
    function setEmpresa($valor){ $this->empresa = $valor;}
    function setNumero($valor){ $this->numero = $valor;}
    function setFuncionario($valor) { $this->funcionario = $valor;}
    function setLocalEmissao($valor){ $this->localemissao = $valor;}
    function setDataEmissao($valor){ $this->dataemissao = $valor;}
    function setEmitente($valor){ $this->emitente = $valor;}
    function setTipoCAT($valor){ $this->tipocat = $valor;}
    function setDataComunObito($valor){ $this->datacomunobito = $valor;}
    function setUnidadeTrabalho($valor){ $this->unidadetrabalho = $valor;}
    function setNumeroCAT($valor){ $this->numerocat = $valor;}
    function setDataRegistro($valor){ $this->dataregistro = $valor;}
    function setNomeFuncionario($valor){ $this->nomefuncionario = $valor;}
    function setDataNascimento($valor){ $this->datanascimento = $valor;}
    function setSexo($valor){ $this->sexo = $valor;}
    function setEstadocivil($valor){ $this->estadocivil = $valor;}
    function setCtpsNumero($valor){ $this->ctpsnumero = $valor;}
    function setCtpsSerie($valor){ $this->ctpsserie = $valor;}
    function setCtpsData($valor){ $this->ctpsdata = $valor;}
    function setCtpsUF($valor){ $this->ctpsuf = $valor;}
    function setRemuneracao($valor){ $this->remuneracao = $valor;}
    function setRgNumero($valor){ $this->rgnumero = $valor;}
    function setRgDataEmissao($valor){ $this->rgdataemissao = $valor;}
    function setRgEmissor($valor){ $this->rgemissor = $valor;}
    function setRgUf($valor){ $this->rguf = $valor;}
    function setPisPasep($valor){ $this->pispasep = $valor;}
    function setEndCep($valor){ $this->endcep = $valor;}
    function setEndereco($valor){ $this->endereco = $valor;}
    function setEndNumero($valor){ $this->endnumero = $valor;}
    function setBairro($valor){ $this->bairro = $valor;}
    function setCidade($valor){ $this->cidade = $valor;}
    function setEstado($valor){ $this->estado = $valor;}
    function setDddFone($valor){ $this->dddfone = $valor;}
    function setPreFone($valor){ $this->prefone = $valor;}
    function setNumFone($valor){ $this->numfone = $valor;}
    function setOcupacao($valor){ $this->ocupacao = $valor;}
    function setCodigoCbo($valor){ $this->codigocbo = $valor;}
    function setFiliacaoPs($valor){ $this->filiacaops = $valor;}
    function setAposentado($valor){ $this->aposentado = $valor;}
    function setAreas($valor){ $this->areas = $valor;}
    function setDataAcidente($valor){ $this->dataacidente = $valor;}
    function setHoraAcidente($valor){ $this->horaacidente = $valor;}
    function setQtdeHorasTrabalho($valor){ $this->qtdehorastrabalho = $valor;}
    function setTipoAcidente($valor){ $this->tipoacidente = $valor;}
    function setHouveAfastamento($valor){ $this->houveafastamento = $valor;}
    function setUltimoDiaTrab($valor){ $this->ultimodiatrab = $valor;}
    function setLocalAcidente($valor){ $this->localacidente = $valor;}
    function setLocalEspecifica($valor){ $this->localespecifica = $valor;}
    function setLocalCnpj($valor){ $this->localcnpj = $valor;}
    function setLocalCidade($valor){ $this->localcidade = $valor;}
    function setLocalBairro($valor){ $this->localbairro = $valor;}
    function setLocalUf($valor){ $this->localuf = $valor;}
    function setPartesAtingidas($valor){ $this->partesatingidas = $valor;}
    function setAgenteCausador($valor){ $this->agentecausador = $valor;}
    function setSituacao($valor){ $this->situacao = $valor;}
    function setRegistroPolicial($valor){ $this->registropolicial = $valor;}
    function setHouveMorte($valor){ $this->houvemorte = $valor;}
    function setTestem1_Nome($valor){ $this->testem1_nome = $valor;}
    function setTestem1_Cep($valor){ $this->testem2_cep = $valor;}
    function setTestem1_Endereco($valor){ $this->testem1_endereco = $valor;}
    function setTestem1_EndNumer($valor){ $this->testem1_endnumer = $valor;}
    function setTestem1_EndCompl($valor){ $this->testem1_endcompl = $valor;}
    function setTestem1_Bairro($valor){ $this->testem1_bairro = $valor;}
    function setTestem1_Cidade($valor){ $this->testem1_cidade = $valor;}
    function setTestem1_Estado($valor){ $this->testem1_estado = $valor;}
    function setTestem1_DddFone($valor){ $this->testem1_dddfone = $valor;}
    function setTestem1_PreFone($valor){ $this->testem1_prefone = $valor;}
    function setTestem1_NumFone($valor){ $this->testem1_numfone = $valor;}
    function setTestem2_Nome($valor){ $this->testem2_nome = $valor;}
    function setTestem2_Cep($valor){ $this->testem2_cep = $valor;}
    function setTestem2_Endereco($valor){ $this->testem2_endereco = $valor;}
    function setTestem2_EndNumer($valor){ $this->testem2_endnumer = $valor;}
    function setTestem2_EndCompl($valor){ $this->testem2_endcompl = $valor;}
    function setTestem2_Bairro($valor){ $this->testem2_bairro = $valor;}
    function setTestem2_Cidade($valor){ $this->testem2_cidade = $valor;}
    function setTestem2_Estado($valor){ $this->testem2_estado = $valor;}
    function setTestem2_DddFone($valor){ $this->testem2_dddfone = $valor;}
    function setTestem2_PreFone($valor){ $this->testem2_prefone = $valor;}
    function setTestem2_NumFone($valor){ $this->testem2_numfone = $valor;}
    function setTipoAcidente_ESocial($valor){ $this->tpacidente_esocial  = $valor;}
    function setSituacaoGeradoraCat($valor){ $this->situacaogeradora_cat  = $valor;}
    function setCodigoIBGE_Cidade($valor){ $this->codigoibge_cidade  = $valor;}
    function setCodigoAgenteCausador($valor){ $this->codigoagentecausador  = $valor;}
    function setCodigoNaturezaLesao($valor){ $this->codigonaturezalesao  = $valor;}
    function setCodigoCid10($valor){ $this->codigocid10  = $valor;}
    function setTipoInscricaoRegistrador($valor){ $this->tipoinscricaoregistrador  = $valor;}
    function setCnpjRegistrador($valor){ $this->cnpjregistrador  = $valor;}
    function setIniciativaCat($valor){ $this->iniciativacat  = $valor;}
    function setDescricaoLogradouro($valor){ $this->descricaologradouro  = $valor;}
    function setNumeroLogradouro($valor){ $this->numerologradouro  = $valor;}
    function setCepLocalAcidente($valor){ $this->ceplocalacidente  = $valor;}
    function setAtestadoCnes($valor){ $this->atestado_cnes  = $valor;}
    function setAtestadoData($valor){ $this->atestado_data = $valor;}
    function setAtestadoHora($valor){ $this->atestado_hora = $valor;}
    function setAtestadoInsternacao($valor){ $this->atestado_internacao  = $valor;}
    function setAtestadoDuracaoDias($valor){ $this->atestado_duracaodias  = $valor;}
    function setAtestadoDiagnostico($valor){ $this->atestado_diagnostico  = $valor;}
    function setAtestadoNomeEmitente($valor){ $this->atestado_nomeemitente  = $valor;}
    function setAtestadoOrgaoClasse($valor){ $this->atestado_orgaoclasse  = $valor;}
    function setAtestadoNumeroInscricao($valor){ $this->atestado_numeroinscricao  = $valor;}
    function setAtestadoUfOrgaoClasse($valor){ $this->atestado_uforgaoclasse  = $valor;}
    function setDataCatOrigem($valor){ $this->data_cat_origem = $valor;}
    function setNumeroCatOrigem($valor){ $this->numero_cat_origem  = $valor;}
    function setObservacao($valor){ $this->observacao  = $valor;}
    function setCodigoParteAtingida($valor){ $this->codigoparteatingida  = $valor;}
    function setCodigoLateralidade($valor){ $this->codigolateralidade  = $valor;}
    function setDescricaoComplementarLesao($valor){$this->descricaocomplementarlesao = $valor;}
    function setAtestadoObservacao($valor){$this->atestado_observacao = $valor;}
    
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
            return new registrocat();
    }  

    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.UNIDADE", "A.EMPRESA", "A.NUMERO", "A.FUNCIONARIO", "A.LOCALEMISSAO",
                        "A.DATAEMISSAO", "A.EMITENTE", "A.TIPOCAT", "A.DATACOMUNOBITO", "A.UNIDADETRABALHO",
                        "A.NUMEROCAT", "A.DATAREGISTRO", "A.NOMEFUNCIONARIO", "A.NOMEMAEFUNCION",
                        "A.DATANASCIMENTO", "A.SEXO", "A.ESTADOCIVIL", "A.CTPSNUMERO", "A.CTPSSERIE",
                        "A.CTPSDATA", "A.CTPSUF", "A.REMUNERACAO", "A.RGNUMERO", "A.RGDATAEMISSAO",
                        "A.RGEMISSOR", "A.RGUF", "A.PISPASEP", "A.ENDCEP", "A.ENDERECO", "A.ENDNUMERO",
                        "A.BAIRRO", "A.CIDADE", "A.ESTADO", "A.DDDFONE", "A.PREFONE", "A.NUMFONE",
                        "A.OCUPACAO", "A.CODIGOCBO", "A.FILIACAOPS", "A.APOSENTADO", "A.AREAS",
                        "A.DATAACIDENTE", "A.HORAACIDENTE", "A.QTDHORASTRABALHO", "A.TIPOACIDENTE",
                        "A.HOUVEAFASTAMENTO", "A.ULTIMODIATRAB", "A.LOCALACIDENTE", "A.LOCALESPESIFICA",
                        "A.LOCALCNPJ", "A.LOCALCIDADE", "A.LOCALUF", "A.PARTESATINGIDAS", "A.AGENTECAUSADOR",
                        "A.SITUACAO", "A.REGISTROPOLICIAL", "A.HOUVEMORTE", "A.TESTEM1_NOME",
                        "A.TESTEM1_CEP", "A.TESTEM1_ENDERECO", "A.TESTEM1_ENDNUMER", "A.TESTEM1_ENDCOMPL",
                        "A.TESTEM1_BAIRRO", "A.TESTEM1_CIDADE", "A.TESTEM1_ESTADO", "A.TESTEM1_DDDFONE",
                        "A.TESTEM1_PREFONE", "A.TESTEM1_NUMFONE", "A.TESTEM2_NOME", "A.TESTEM2_CEP",
                        "A.TESTEM2_ENDERECO", "A.TESTEM2_ENDNUMER", "A.TESTEM2_ENDCOMPL",
                        "A.TESTEM2_BAIRRO", "A.TESTEM2_CIDADE", "A.TESTEM2_ESTADO", "A.TESTEM2_DDDFONE",
                        "A.TESTEM2_PREFONE", "A.TESTEM2_NUMFONE", "A.TPACIDENTE_ESOCIAL",
                        "A.SITUACAOGERADORA_CAT", "A.CODIGOIBGE_CIDADE", "A.CODIGOAGENTECAUSADOR",
                        "A.CODIGONATUREZALESAO", "A.CODIGOCID10", "A.TIPOINSCRICAOREGISTRADOR",
                        "A.CNPJCPFREGISTRADOR", "A.INICIATIVACAT", "A.DESCRICAOLOGRADOURO",
                        "A.NUMEROLOGRADOURO", "A.CEPLOCALACIDENTE", "A.ATESTADO_CNES", "A.ATESTADO_DATA",
                        "A.ATESTADO_HORA", "A.ATESTADO_INTERNACAO", "A.ATESTADO_DURACAODIAS",
                        "A.ATESTADO_DIAGNOSTICO", "A.ATESTADO_NOMEEMITENTE", "A.ATESTADO_ORGAOCLASSE",
                        "A.ATESTADO_NUMEROINSCRICAO", "A.ATESTADO_UFORGAOCLASSE", "A.DATA_CAT_ORIGEM",
                        "A.NUMERO_CAT_ORIGEM", "A.OBSERVACAO", "A.LOCALBAIRRO", "A.CODIGOPARTEATINGIDA",
                        "A.CODIGOLATERALIDADE", "A.DESCRICAOCOMPLEMENTARLESAO", "A.ATESTADO_OBSERVACAO");
        
        $join = "  "
               ." ";
        $fb = new FB("T_REGISTROCAT A");
        $ret = $fb->select($filtro, null, null, $join, $select, null, false);
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        
        for ($i = 0; $i < count($ret); $i++){
            $cat = new registrocat();
            $cat->setCodigo($ret[$i]["CODIGO"]);
            $cat->setCodigo($ret[$i]["CODIGO"]);
            $cat->setUnidade($ret[$i]["UNIDADE"]);
            $cat->setEmpresa($ret[$i]["EMPRESA"]);
            $cat->setNumero($ret[$i]["NUMERO"]);
            $cat->setFuncionario($ret[$i]["FUNCIONARIO"]);
            $cat->setLocalEmissao($ret[$i]["LOCALEMISSAO"]);
            $cat->setDataEmissao($ret[$i]["DATAEMISSAO"]);
            $cat->setEmitente($ret[$i]["EMITENTE"]);
            $cat->setTipoCAT($ret[$i]["TIPOCAT"]);
            $cat->setDataComunObito($ret[$i]["DATACOMUNOBITO"]);
            $cat->setUnidadeTrabalho($ret[$i]["UNIDADETRABALHO"]);
            $cat->setNumeroCAT($ret[$i]["NUMEROCAT"]);
            $cat->setDataRegistro($ret[$i]["DATAREGISTRO"]);
            $cat->setNomeFuncionario($ret[$i]["NOMEFUNCIONARIO"]);
            $cat->setDataNascimento($ret[$i]["DATANASCIMENTO"]);
            $cat->setSexo($ret[$i]["SEXO"]);
            $cat->setEstadocivil($ret[$i]["ESTADOCIVIL"]);
            $cat->setCtpsNumero($ret[$i]["CTPSNUMERO"]);
            $cat->setCtpsSerie($ret[$i]["CTPSSERIE"]);
            $cat->setCtpsData($ret[$i]["CTPSDATA"]);
            $cat->setCtpsUF($ret[$i]["CTPSUF"]);
            $cat->setRemuneracao($ret[$i]["REMUNERACAO"]);
            $cat->setRgNumero($ret[$i]["RGNUMERO"]);
            $cat->setRgDataEmissao($ret[$i]["RGDATAEMISSAO"]);
            $cat->setRgEmissor($ret[$i]["RGEMISSOR"]);
            $cat->setRgUf($ret[$i]["RGUF"]);
            $cat->setPisPasep($ret[$i]["PISPASEP"]);
            $cat->setEndCep($ret[$i]["ENDCEP"]);
            $cat->setEndereco($ret[$i]["ENDERECO"]);
            $cat->setEndNumero($ret[$i]["ENDNUMERO"]);
            $cat->setBairro($ret[$i]["BAIRRO"]);
            $cat->setCidade($ret[$i]["CIDADE"]);
            $cat->setEstado($ret[$i]["ESTADO"]);
            $cat->setDddFone($ret[$i]["DDDFONE"]);
            $cat->setPreFone($ret[$i]["PREFONE"]);
            $cat->setNumFone($ret[$i]["NUMFONE"]);
            $cat->setOcupacao($ret[$i]["OCUPACAO"]);
            $cat->setCodigoCbo($ret[$i]["CODIGOCBO"]);
            $cat->setFiliacaoPs($ret[$i]["FILIACAOPS"]);
            $cat->setAposentado($ret[$i]["APOSENTADO"]);
            $cat->setAreas($ret[$i]["AREAS"]);
            $cat->setDataAcidente($ret[$i]["DATAACIDENTE"]);
            $cat->setHoraAcidente($ret[$i]["HORAACIDENTE"]);
            $cat->setQtdeHorasTrabalho($ret[$i]["QTDHORASTRABALHO"]);
            $cat->setTipoAcidente($ret[$i]["TIPOACIDENTE"]);
            $cat->setHouveAfastamento($ret[$i]["HOUVEAFASTAMENTO"]);
            $cat->setUltimoDiaTrab($ret[$i]["ULTIMODIATRAB"]);
            $cat->setLocalAcidente($ret[$i]["LOCALACIDENTE"]);
            $cat->setLocalEspecifica($ret[$i]["LOCALESPESIFICA"]);
            $cat->setLocalCnpj($ret[$i]["LOCALCNPJ"]);
            $cat->setLocalCidade($ret[$i]["LOCALCIDADE"]);
            $cat->setLocalBairro($ret[$i]["LOCALBAIRRO"]);
            $cat->setLocalUf($ret[$i]["LOCALUF"]);
            $cat->setPartesAtingidas($ret[$i]["PARTESATINGIDAS"]);
            $cat->setAgenteCausador($ret[$i]["AGENTECAUSADOR"]);
            $cat->setSituacao($ret[$i]["SITUACAO"]);
            $cat->setRegistroPolicial($ret[$i]["REGISTROPOLICIAL"]);
            $cat->setHouveMorte($ret[$i]["HOUVEMORTE"]);
            $cat->setTestem1_Nome($ret[$i]["TESTEM1_NOME"]);
            $cat->setTestem1_Cep($ret[$i]["TESTEM1_CEP"]);
            $cat->setTestem1_Endereco($ret[$i]["TESTEM1_ENDERECO"]);
            $cat->setTestem1_EndNumer($ret[$i]["TESTEM1_ENDNUMER"]);
            $cat->setTestem1_EndCompl($ret[$i]["TESTEM1_ENDCOMPL"]);
            $cat->setTestem1_Bairro($ret[$i]["TESTEM1_BAIRRO"]);
            $cat->setTestem1_Cidade($ret[$i]["TESTEM1_CIDADE"]);
            $cat->setTestem1_Estado($ret[$i]["TESTEM1_ESTADO"]);
            $cat->setTestem1_DddFone($ret[$i]["TESTEM1_DDDFONE"]);
            $cat->setTestem1_PreFone($ret[$i]["TESTEM1_PREFONE"]);
            $cat->setTestem1_NumFone($ret[$i]["TESTEM1_NUMFONE"]);
            $cat->setTestem2_Nome($ret[$i]["TESTEM2_NOME"]);
            $cat->setTestem2_Cep($ret[$i]["TESTEM2_CEP"]);
            $cat->setTestem2_Endereco($ret[$i]["TESTEM2_ENDERECO"]);
            $cat->setTestem2_EndNumer($ret[$i]["TESTEM2_ENDNUMER"]);
            $cat->setTestem2_EndCompl($ret[$i]["TESTEM2_ENDCOMPL"]);
            $cat->setTestem2_Bairro($ret[$i]["TESTEM2_BAIRRO"]);
            $cat->setTestem2_Cidade($ret[$i]["TESTEM2_CIDADE"]);
            $cat->setTestem2_Estado($ret[$i]["TESTEM2_ESTADO"]);
            $cat->setTestem2_DddFone($ret[$i]["TESTEM2_DDDFONE"]);
            $cat->setTestem2_PreFone($ret[$i]["TESTEM2_PREFONE"]);
            $cat->setTestem2_NumFone($ret[$i]["TESTEM2_NUMFONE"]);
            $cat->setTipoAcidente_ESocial($ret[$i]["TPACIDENTE_ESOCIAL"]);
            $cat->setSituacaoGeradoraCat($ret[$i]["SITUACAOGERADORA_CAT"]);
            $cat->setCodigoIBGE_Cidade($ret[$i]["CODIGOIBGE_CIDADE"]);
            $cat->setCodigoAgenteCausador($ret[$i]["CODIGOAGENTECAUSADOR"]);
            $cat->setCodigoNaturezaLesao($ret[$i]["CODIGONATUREZALESAO"]);
            $cat->setCodigoCid10($ret[$i]["CODIGOCID10"]);
            $cat->setTipoInscricaoRegistrador($ret[$i]["TIPOINSCRICAOREGISTRADOR"]);
            $cat->setCnpjRegistrador($ret[$i]["CNPJCPFREGISTRADOR"]);
            $cat->setIniciativaCat($ret[$i]["INICIATIVACAT"]);
            $cat->setDescricaoLogradouro($ret[$i]["DESCRICAOLOGRADOURO"]);
            $cat->setNumeroLogradouro($ret[$i]["NUMEROLOGRADOURO"]);
            $cat->setCepLocalAcidente($ret[$i]["CEPLOCALACIDENTE"]);
            $cat->setAtestadoCnes($ret[$i]["ATESTADO_CNES"]);
            $cat->setAtestadoData($ret[$i]["ATESTADO_DATA"]);
            $cat->setAtestadoHora($ret[$i]["ATESTADO_HORA"]);
            $cat->setAtestadoInsternacao($ret[$i]["ATESTADO_INTERNACAO"]);
            $cat->setAtestadoDuracaoDias($ret[$i]["ATESTADO_DURACAODIAS"]);
            $cat->setAtestadoDiagnostico($ret[$i]["ATESTADO_DIAGNOSTICO"]);
            $cat->setAtestadoNomeEmitente($ret[$i]["ATESTADO_NOMEEMITENTE"]);
            $cat->setAtestadoOrgaoClasse($ret[$i]["ATESTADO_ORGAOCLASSE"]);
            $cat->setAtestadoNumeroInscricao($ret[$i]["ATESTADO_NUMEROINSCRICAO"]);
            $cat->setAtestadoUfOrgaoClasse($ret[$i]["ATESTADO_UFORGAOCLASSE"]);
            $cat->setDataCatOrigem($ret[$i]["DATA_CAT_ORIGEM"]);
            $cat->setNumeroCatOrigem($ret[$i]["NUMERO_CAT_ORIGEM"]);
            $cat->setObservacao($ret[$i]["OBSERVACAO"]);
            $cat->setCodigoParteAtingida($ret[$i]["CODIGOPARTEATINGIDA"]);
            $cat->setCodigoLateralidade($ret[$i]["CODIGOLATERALIDADE"]);
            $cat->setDescricaoComplementarLesao($ret[$i]["DESCRICAOCOMPLEMENTARLESAO"]);
            $cat->setAtestadoObservacao($ret[$i]["ATESTADO_OBSERVACAO"]);
            
            array_push($this->lista, $cat);
        }       

        $this->totalLista = count($this->lista);

        return $this->totalLista > 0;
    }
    
    function remover(){
        
    }
    function alterar(){
        
    }
    function inserir($val=null){
        if ($val != null){
            if (is_array($val)){
                $fb = new FB("T_REGISTROCAT");
                $numeroCAT = $fb->proximoCodigoGeral($val["UNIDADE"], "REGCAT");
                $val["NUMERO"] = $numeroCAT;
                if ($fb->insert($val,false)){
                    $this->setNumero($val["NUMERO"]);
                    return true;
                }else
                    return false;
            }else
                return false;
        }else
            return false;           
    }
    function inserirParteAtingida($val=null){
        if ($val != null){
            if (is_array($val)){
                $fb = new FB("T_CATPARTEATINGIDA");
                if ($fb->insert($val,false)){
                    return true;
                }else
                    return false;
            }else
                return false;
        }else
            return false; 
    }
    function inserirAgenteCausador($val=null){
        if ($val != null){
            if (is_array($val)){
                $fb = new FB("T_CATAGENTECAUSADOR");
                if ($fb->insert($val,false)){
                    return true;
                }else
                    return false;
            }else
                return false;
        }else
            return false; 
    }
    
    function existeCAT($numero = null){
        $select = array("A.NUMERO");
        $filtro = ["A.NUMERO" => $numero];
        $fb   = new FB("T_REGISTROCAT A");
        $ret = $fb->select($filtro);        
        return count($ret)> 0;
    }
    

    function montarXML($unidade,$empresa,$numero){
        $cat = new registrocat();
        if ($cat->buscar(array("A.UNIDADE" => $unidade, "A.EMPRESA" => $empresa, "A.NUMERO" => $numero))){
            $cat = $cat->getItemLista(0);
            $funcionario = new funcionario();
            if ($funcionario->buscar(array("A.UNIDADE" => $unidade, "A.EMPRESA" => $empresa, "A.MATRICULA" => $cat->getFuncionario()))){
                $funcionario = $funcionario->getItemLista(0);

                $emp = new empresa();

                if ($emp->buscar(array("A.UNIDADE" => $unidade, "A.CODIGO" => $empresa)))
                    $emp = $emp->getItemLista(0);
                
                $cert = $emp->getArquivoCertificadoDigital();//file_get_contents('expired_certificate.pfx');
                //carrega a classe responsavel por lidar com os certificados
                $password = criptografia::deCriptografia($emp->getSenhaCertificadoDigital());

                try {
                    $certificate = Certificate::readPfx($cert, $password);
                } catch (\Exception $th) {
                    echo $th->getMessage();
                    die;
                }
                $xml = "";
                $config = [
                    'tpAmb' => 1,
                    //tipo de ambiente 1 - Produção; 2 - Produção restrita - dados reais;3 - Produção restrita - dados fictícios.
                    'verProc' => 'S_1.0.0',
                    //Versão do processo de emissão do evento. Informar a versão do aplicativo emissor do evento.
                    'eventoVersion' => 'S.1.0.0',
                    //versão do layout do evento
                    'serviceVersion' => '1.5.0',
                    //versão do webservice
                    'empregador' => [
                        'tpInsc' => 1, //1-CNPJ, 2-CPF
                        'nrInsc' => substr(funcoes::somenteNumeros($emp->getCNPJCPF()),0,8), //numero do documento
                        'nmRazao' => funcoes::removerCaracteres($emp->getNomeFantasia()),
                    ],
                    'transmissor' => [
                        'tpInsc' => 1, //1-CNPJ, 2-CPF
                        'nrInsc' => substr(funcoes::somenteNumeros($certificate->getCnpj()), 0, 8) //numero do documento
                    ],
                ];
                
                $configJson = json_encode($config);
                // print_r($configJson);
                // print_r('Config JSON: ' . $configJson);
                // die;

                $std = new \stdClass();
                $std->sequencial = 1;
                $std->indretif = 1;
                // $std->nrrecibo = '';

                $std->tpinsc = 1;                
                $std->nrinsc = strval(funcoes::somenteNumeros($emp->getCNPJCPF()));
                
                $std->cpftrab = funcoes::somenteNumeros($funcionario->getCPF());
                $std->matricula = funcoes::removerCaracteres($funcionario->getMatriculaESocial());
                if ($funcionario->getCodigoCategoriaESocial() != '')
                    $std->codcateg = $funcionario->getCodigoCategoriaESocial();
                
                
                $std->dtacid = $cat->getDataAcidente();//'2017-12-10';
                $std->tpacid = $cat->getTipoAcidente();
                $std->hracid = substr(funcoes::somenteNumeros($cat->getHoraAcidente()),0,4);//'0522';
                $std->hrstrabantesacid = substr(str_replace(':','',$cat->getQtdeHorasTrabalho()),0,4);//'0522';

                $std->tpcat = $cat->getTipoCAT();
                $std->indcatobito = $cat->getHouveMorte();
                if($cat->getHouveMorte() == 'S')
                    $std->dtobito = $cat->getDataComunObito();

                $std->indcomunpolicia = $cat->getRegistroPolicial();
                $std->codsitgeradora = $cat->getSituacaoGeradoraCat();

                $std->iniciatcat = $cat->getIniciativaCat();
                if ($cat->getObservacao() != '')
                    $std->obscat = funcoes::removerCaracteres(utf8_encode($cat->getObservacao()));

                $std->tplocal = $cat->getLocalAcidente();
                $std->dsclocal = funcoes::removerCaracteres(utf8_encode($cat->getLocalEspecifica()));
                // $std->tplograd = '';
                $std->dsclograd = funcoes::removerCaracteres(utf8_encode($cat->getDescricaoLogradouro()));
                if($cat->getNumeroLogradouro() > 0)
                    $std->nrlograd = funcoes::somenteNumeros($cat->getNumeroLogradouro());
                else
                    $std->nrlograd = '0';

                //$std->complemento = '';
                $std->bairro = funcoes::removerCaracteres(utf8_encode($cat->getLocalBairro()));
                $std->cep = funcoes::somenteNumeros($cat->getCepLocalAcidente());
                $std->codmunic =funcoes::somenteNumeros($cat->getCodigoIBGE_Cidade());
                $std->uf = $cat->getLocalUf();
                // $std->pais = '105';
                // $std->codpostal = '123456789012';
                
                $std->idelocalacid = new \stdClass();
                $std->idelocalacid->tpinsc = 1;
                $std->idelocalacid->nrinsc = funcoes::somenteNumeros($cat->getLocalCnpj());
                
                $std->parteatingida = new \stdClass();
                $std->parteatingida->codparteating = $cat->getCodigoParteAtingida();
                $std->parteatingida->lateralidade = $cat->getCodigoLateralidade();

                $std->agentecausador = new \stdClass();
                $std->agentecausador->codagntcausador = $cat->getCodigoAgenteCausador();

                $std->atestado = new \stdClass();
                $std->atestado->dtatendimento = $cat->getAtestadoData();
                $std->atestado->hratendimento = substr(funcoes::somenteNumeros($cat->getAtestadoHora()),0,4);
                $std->atestado->indinternacao = $cat->getAtestadoInsternacao();
                $std->atestado->durtrat = funcoes::somenteNumeros($cat->getAtestadoDuracaoDias());
                $std->atestado->indafast = $cat->getHouveAfastamento();
                $std->atestado->dsclesao = funcoes::removerCaracteres(utf8_encode($cat->getCodigoNaturezaLesao()));
                $std->atestado->dsccompLesao = funcoes::removerCaracteres(utf8_encode($cat->getDescricaoComplementarLesao()));
                $std->atestado->diagprovavel = funcoes::removerCaracteres(utf8_encode(substr($cat->getAtestadoDiagnostico(),0,100)));
                $std->atestado->codcid = str_replace(['-','.','/',','],['','','',''],$cat->getCodigoCid10());
                $std->atestado->observacao = funcoes::removerCaracteres(utf8_encode($cat->getAtestadoObservacao()));
                $std->atestado->nmemit = funcoes::removerCaracteres(utf8_encode($cat->getAtestadoNomeEmitente()));                
                $std->atestado->ideoc = $cat->getAtestadoOrgaoClasse();
                $std->atestado->nroc = funcoes::somenteNumeros( $cat->getAtestadoNumeroInscricao());
                $std->atestado->ufoc = $cat->getAtestadoUfOrgaoClasse();
                
                // $std->catorigem = new \stdClass();
                // $std->catorigem->nrreccatorig = '1.1.1234567890123456789';
                try {
               
                    //cria o evento e retorna o XML assinado
                    $xml = Event::evtCAT(
                        $configJson,
                        $std,
                        $certificate,
                        date('Y-m-d H:i:s')
                    );
                    // print_r('passou');
                    // die;
                    //$xml = Evento::s2210($json, $std, $certificate)->toXML();
                    //$json = Event::evtCAT($configjson, $std, $certificate)->toJson();
                
                    header('Content-type: text/xml; charset=UTF-8');
                    // print_r($xml);
                    // die;
                    return $xml;
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                    die;
                    return $e->getMessage();
                }
    
            }
   
    

        }

                
    }

    
}