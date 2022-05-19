<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");


class unidade implements iface {
    private $codigo, $razaosocial, $nomefantasia, $cnpj, $ie, 
            $endereco, $numero, $complemento, $cidade, $telefone;
    
    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    private $arquivoCertificadoDigital = "";
    private $senhaCertificadoDigital = "";
    private $validadeCertificadoDigital = "";

    function getCodigo(){ return $this->codigo; }
    function getRazaoSocial(){ return $this->razaosocial; }
    function getNomeFantasia(){ return $this->nomefantasia; }
    function getCNPJ(){ return $this->cnpj; }
    function getIE(){ return $this->ie; }
    function getEndereco(){ return $this->endereco; }
    function getNumero(){ return $this->numero; }
    function getComplemento(){ return $this->complemento; }
    function getCidade(){ return $this->cidade; }
    function getTelefone(){ return $this->telefone; }
    function getArquivoCertificadoDigital() { return $this->arquivoCertificadoDigital; }
    function getSenhaCertificadoDigital() { return $this->senhaCertificadoDigital; }
    function getValidadeCertificadoDigital() { return $this->validadeCertificadoDigital; }
    
    function setCodigo($valor){ $this->codigo = $valor; }
    function setRazaoSocial($valor){ $this->razaosocial = $valor; }
    function setNomeFantasia($valor){ $this->nomefantasia = $valor; }
    function setCNPJ($valor){ $this->cnpj = $valor; }
    function setIE($valor){ $this->ie = $valor; }
    function setEndereco($valor){ $this->endereco = $valor; }
    function setNumero($valor){ $this->numero = $valor; }
    function setComplemento($valor){ $this->complemento = $valor; }
    function setCidade($valor){ $this->cidade = $valor; }
    function setTelefone($valor){ $this->telefone = $valor; }

    function setArquivoCertificadoDigital($valor) { $this->arquivoCertificadoDigital = $valor; }
    function setSenhaCertificadoDigital($valor) { $this->senhaCertificadoDigital = $valor; }
    function setValidadeCertificadoDigital($valor) { $this->validadeCertificadoDigital = $valor; }
    
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
            return new unidade();
    }  
    
    function buscar($filtro = null, $limite = null, $ordem = null){
        $fb = new FB("T_UNIDADE A");
        $ret = $fb->select($filtro);        
        $this->totalRegistros = count($ret);
        
        $ret = $fb->select($filtro, $limite, $ordem);
        for ($i = 0; $i < count($ret); $i++){
            $un = new unidade();
            $un->setCodigo($ret[$i]["CODIGO"]);
            $un->setRazaoSocial($ret[$i]["RAZAOSOCIAL"]);
            $un->setNomeFantasia($ret[$i]["NOMEFANTASIA"]);
            $un->setCNPJ($ret[$i]["CNPJ"]);
            $un->setIE($ret[$i]["INSCREST"]);
            $un->setEndereco($ret[$i]["ENDERECO"]);
            $un->setNumero($ret[$i]["NUMERO"]);
            $un->setComplemento($ret[$i]["COMPLEMENTO"]);
            $un->setCidade($ret[$i]["CIDADE"]);

            if ((str_pad($ret[$i]["PREFONE"], 4, "0", STR_PAD_LEFT) != "0000") &&
                 (str_pad($ret[$i]["NUMFONE"], 4, "0", STR_PAD_LEFT) != "0000")){
                $ddd = "";
                if (str_pad($ret[$i]["DDDFONE"], 3, "0", STR_PAD_LEFT) != "000")
                    $ddd = "({$ret[$i]["DDDFONE"]}) ";
                    
                $un->setTelefone($ddd."{$ret[$i]["PREFONE"]}-{$ret[$i]["NUMFONE"]}");
            }
            
            if (!empty($ret[$i]["ARQUIVOCERTIFICADODIGITAL"])) {
                $arquivo = utf8_decode($ret[$i]["ARQUIVOCERTIFICADODIGITAL"]);

                $blob_data = ibase_blob_info($fb->getConexao(), $arquivo);
                $blob_hndl = ibase_blob_open($fb->getConexao(), $arquivo);
                $un->setArquivoCertificadoDigital(ibase_blob_get($blob_hndl, $blob_data[0]));
                ibase_blob_close($blob_hndl);
                //echo $e->getArquivoCertificadoDigital();die;
            }
            
            $un->setSenhaCertificadoDigital($ret[$i]["SENHACERTIFICADODIGITAL"]);
            $un->setValidadeCertificadoDigital($ret[$i]["VALIDADECERTIFICADODIGITAL"]);

            array_push($this->lista, $un);
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
        $fb = new FB("T_UNIDADE");
        $blob_hndl = ibase_blob_create($fb->getConexao());
        ibase_blob_add($blob_hndl, $certificado);
        $blobid = ibase_blob_close($blob_hndl);
        
        return $fb->executeQueryBlob("UPDATE T_UNIDADE SET SENHACERTIFICADODIGITAL = '{$senha}', "
                                                        . "VALIDADECERTIFICADODIGITAL = '{$validade}', "
                                                        . "ARQUIVOCERTIFICADODIGITAL = ? "
                                    . "WHERE CODIGO = '{$unidade}' ", $blobid);
        
    }

}
