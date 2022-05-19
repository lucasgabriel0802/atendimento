<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");
require_once("cep.class.php");
require_once("responsavel.class.php");

class empresa implements iface {
    private $codigo       = "";
    private $razaosocial  = "";
    private $nomefantasia = "";
    private $cnpjcpf      = "";
    private $ierg         = "";
    private $intervaloagendamento  = -1;
    private $intervalocancelamento = -1;
    private $limiteagendamentoweb  = -1;
    private $mensagemweb           = "";
    private $endereco       = "";
    private $enderecocob    = "";
    private $usaenderecocob = "N";
    private $datarescisao   = "";
    private $responsavel    = "";
    private $limiteAtraso   = 0;
    private $telefone       = "";
    private $exibirValorFalta  = false;
    private $exibirValorAtraso = false;
    private $bloqueado = false;
    private $valorFalta        = 0;
    private $valorAtraso       = 0;
    private $arquivoCertificadoDigital = "";
    private $senhaCertificadoDigital = "";
    private $validadeCertificadoDigital = "";
    private $modeloArquivoDownload = 0;
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getCodigo(){ return $this->codigo; }
    function getRazaoSocial(){ return $this->razaosocial; }
    function getNomeFantasia(){ return $this->nomefantasia; }
    function getCNPJCPF(){ return $this->cnpjcpf; }
    function getIERG(){ return $this->ie; }
    
    function getIntervaloAgendamento(){ 
        if (is_numeric($this->intervaloagendamento))
            return $this->intervaloagendamento;
        else
            return 0;
    }
    
    function getIntervaloCancelamento(){
        if (is_numeric($this->intervalocancelamento))
            return $this->intervalocancelamento;
        else
            return 0;
    }
    
    function getLimiteAgendamentoWeb(){
        if (is_numeric($this->limiteagendamentoweb))
            return $this->limiteagendamentoweb;
        else
            return 0;
    }
    
    function getDataRescisao(){ return $this->datarescisao; }
    function getMensagemWeb(){ return $this->mensagemweb; }
    function getEndereco(){ return $this->endereco; }
    function getEnderecoCobranca(){ return $this->enderecocob; }
    function getUsaEnderecoCobranca(){ return $this->usaenderecocob; }
    function getResponsavel(){ return $this->responsavel; }
    function getLimiteAtraso(){ return $this->limiteAtraso; }
    function getTelefone() { return $this->telefone; }
    function getExibirValorFalta() { return $this->exibirValorFalta; }
    function getExibirValorAtraso() { return $this->exibirValorAtraso; }
    function getValorFalta() { return $this->valorFalta; }
    function getValorAtraso() { return $this->valorAtraso; }
    function getArquivoCertificadoDigital() { return $this->arquivoCertificadoDigital; }
    function getSenhaCertificadoDigital() { return $this->senhaCertificadoDigital; }
    function getValidadeCertificadoDigital() { return $this->validadeCertificadoDigital; }
    function getBloqueado() { return $this->bloqueado=='S'; }
    function getModeloArquivoDownload(){return $this->modeloArquivoDownload; }

    function setCodigo($valor){ $this->codigo = $valor; }
    function setRazaoSocial($valor){ $this->razaosocial = $valor; }
    function setNomeFantasia($valor){ $this->nomefantasia = $valor; }
    function setCNPJCPF($valor){ $this->cnpjcpf = $valor; }
    function setIERG($valor){ $this->ierg = $valor; }
    function setIntervaloAgendamento($valor){ $this->intervaloagendamento = $valor; }
    function setIntervaloCancelamento($valor){ $this->intervalocancelamento = $valor; }
    function setLimiteAgendamentoWeb($valor){ $this->limiteagendamentoweb = $valor; }
    function setMensagemWeb($valor){ $this->mensagemweb = $valor; }
    function setEndereco($valor){ $this->endereco = $valor; }
    function setEnderecoCobranca($valor){ $this->enderecocob = $valor; }
    function setUsaEnderecoCobranca($valor){ $this->usaenderecocob = $valor; }
    function setDataRescisao($valor){ $this->datarescisao = $valor; }
    function setResponsavel($valor){ $this->responsavel = $valor; }
    function setTelefone($valor) { $this->telefone = $valor; }
    function setExibirValorFalta($valor) { $this->exibirValorFalta = $valor; }
    function setExibirValorAtraso($valor) { $this->exibirValorAtraso = $valor; }
    function setValorFalta($valor) { $this->valorFalta = $valor; }
    function setValorAtraso($valor) { $this->valorAtraso = $valor; }
    function setLimiteAtraso($valor){
        if (is_numeric(trim($valor))){
            $this->limiteAtraso = $valor;
        }
    }
    function setBloqueado($valor) { $this->bloqueado = ($valor=="S"); }
    function setArquivoCertificadoDigital($valor) { $this->arquivoCertificadoDigital = $valor; }
    function setSenhaCertificadoDigital($valor) { $this->senhaCertificadoDigital = $valor; }
    function setValidadeCertificadoDigital($valor) { $this->validadeCertificadoDigital = $valor; }
    function setModeloArquivoDownload($valor){$this->modeloArquivoDownload = $valor; }

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
            return new empresa();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $select = array("A.CODIGO", "A.RAZAOSOCIAL", "A.NOMEFANTASIA",
                        "A.CNPJCEICPF", "A.INSCREST", "A.INTERVALOAGENDAMENTO",
                        "A.INTERVALOCANCELAMENTO", "A.LIMITEAGENDAMENTOWEB",
                        "A.MENSAGEMWEB", "A.RESCISAOCONTRATO",
                        "A.USARENDCOBRANCA", "A.ENDERECO", "A.NUMERO", "A.COMPLEMENTO", 
                        "A.BAIRRO", "A.CIDADE", "A.ESTADO", "A.CEP",
                        "A.COBENDERECO", "A.COBNUMERO", "A.COBCOMPLEMENTO", "A.COBBAIRRO",
                        "A.COBCIDADE", "A.COBENDERECO", "A.COBCEP", "A.COBESTADO",
                        "A.TECNICO_RESPONSAVEL", "A.BLOQUEARATENDIMENTO",
                        "B.NOME AS NOMERESPONSAVEL", "B.EMAIL AS EMAILRESPONSAVEL",
                        "A.DDDFONE1", "A.NUMFONE1", "A.PREFONE1", "A.ARQUIVOCERTIFICADODIGITAL",
                        "A.EXIBIRVALORFALTACOMPROVANTEWEB", "A.VALOR_FALTA",
                        "A.EXIBIRVALORATRASOCOMPROVANTEWEB", "A.VALOR_ATRASO",
                        "A.SENHACERTIFICADODIGITAL", "A.VALIDADECERTIFICADODIGITAL","A.BLOQUEAR",
                        "A.ARQUIVO_ESOCIAL_PERSONALIZADO");
        
        $join = "LEFT JOIN T_RESPONSAVEL B ON B.UNIDADE = A.UNIDADE             "
               ."                         AND B.CODIGO  = A.TECNICO_RESPONSAVEL ";
        $fb = new FB("T_EMPRESA A");
        $ret = $fb->select($filtro, null, null, $join, $select);
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem, $join, $select);
        
        for ($i = 0; $i < count($ret); $i++){
            $e = new empresa();
            $e->setCodigo($ret[$i]["CODIGO"]);
            $e->setRazaoSocial($ret[$i]["RAZAOSOCIAL"]);
            $e->setNomeFantasia($ret[$i]["NOMEFANTASIA"]);
            $e->setCNPJCPF($ret[$i]["CNPJCEICPF"]);
            $e->setIERG($ret[$i]["INSCREST"]);
            $e->setIntervaloAgendamento($ret[$i]["INTERVALOAGENDAMENTO"]);
            $e->setIntervaloCancelamento($ret[$i]["INTERVALOCANCELAMENTO"]);
            $e->setLimiteAgendamentoWeb($ret[$i]["LIMITEAGENDAMENTOWEB"]);
            $e->setMensagemWeb($ret[$i]["MENSAGEMWEB"]);
            $e->setDataRescisao($ret[$i]["RESCISAOCONTRATO"]);
            $e->setLimiteAtraso($ret[$i]["BLOQUEARATENDIMENTO"]);
            
            $e->setUsaEnderecoCobranca($ret[$i]["USARENDCOBRANCA"]);
            
            $c1 = new cep();
            $c1->setEndereco($ret[$i]["ENDERECO"]);
            $c1->setNumero($ret[$i]["NUMERO"]);
            $c1->setComplemento($ret[$i]["COMPLEMENTO"]);
            $c1->setBairro($ret[$i]["BAIRRO"]);
            $c1->setCidade($ret[$i]["CIDADE"]);
            $c1->setUF($ret[$i]["ESTADO"]);
            $c1->setCEP($ret[$i]["CEP"]);
            $e->setEndereco($c1);
            
            $c2 = new cep();
            $c2->setEndereco($ret[$i]["COBENDERECO"]);
            $c2->setNumero($ret[$i]["COBNUMERO"]);
            $c2->setComplemento($ret[$i]["COBCOMPLEMENTO"]);
            $c2->setBairro($ret[$i]["COBBAIRRO"]);
            $c2->setCidade($ret[$i]["COBCIDADE"]);
            $c2->setUF($ret[$i]["COBESTADO"]);
            $c2->setCEP($ret[$i]["COBCEP"]);
            $e->setEnderecoCobranca($c2);

            $r = new responsavel();
            $r->setCodigo($ret[$i]["TECNICO_RESPONSAVEL"]);
            $r->setNome($ret[$i]["NOMERESPONSAVEL"]);
            $r->setEmail($ret[$i]["EMAILRESPONSAVEL"]);
            $e->setResponsavel($r);
            
            $e->setTelefone("(" . $ret[$i]["DDDFONE1"] . ") " . $ret[$i]["NUMFONE1"] . $ret[$i]["PREFONE1"]);
            $e->setExibirValorFalta($ret[$i]["EXIBIRVALORFALTACOMPROVANTEWEB"] == "S");
            $e->setExibirValorAtraso($ret[$i]["EXIBIRVALORATRASOCOMPROVANTEWEB"] == "S");
            $e->setValorFalta($ret[$i]["VALOR_FALTA"]);
            $e->setValorAtraso($ret[$i]["VALOR_ATRASO"]);
            $e->setBloqueado($ret[$i]["BLOQUEAR"]);
            $e->setModeloArquivoDownload($ret[$i]["ARQUIVO_ESOCIAL_PERSONALIZADO"]);
            
            if (!empty($ret[$i]["ARQUIVOCERTIFICADODIGITAL"])) {
                $arquivo = utf8_decode($ret[$i]["ARQUIVOCERTIFICADODIGITAL"]);

                $blob_data = ibase_blob_info($fb->getConexao(), $arquivo);
                $blob_hndl = ibase_blob_open($fb->getConexao(), $arquivo);
                $e->setArquivoCertificadoDigital(ibase_blob_get($blob_hndl, $blob_data[0]));
                ibase_blob_close($blob_hndl);
                //echo $e->getArquivoCertificadoDigital();die;
            }
            
            $e->setSenhaCertificadoDigital($ret[$i]["SENHACERTIFICADODIGITAL"]);
            $e->setValidadeCertificadoDigital($ret[$i]["VALIDADECERTIFICADODIGITAL"]);
            
            array_push($this->lista, $e);
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
    function salvarCertificadoDigital($unidade, $empresa, $certificado, $senha, $validade){
        $fb = new FB("T_EMPRESA");
        $blob_hndl = ibase_blob_create($fb->getConexao());
        ibase_blob_add($blob_hndl, $certificado);
        $blobid = ibase_blob_close($blob_hndl);
        
        return $fb->executeQueryBlob("UPDATE T_EMPRESA SET SENHACERTIFICADODIGITAL = '{$senha}', "
                                                        . "VALIDADECERTIFICADODIGITAL = '{$validade}', "
                                                        . "ARQUIVOCERTIFICADODIGITAL = ? "
                                    . "WHERE UNIDADE = '{$unidade}' AND CODIGO = '{$empresa}'", $blobid);
        
    }
}
