<?php

/**
 * @author Kayser Informatica
 */
require_once("iface.class.php");
require_once("firebird.class.php");

class ocorrenciaESocial implements iface
{
    private $codigo      = "";
    private $descricao   = "";
    private $localizacao = "";

    private $lista = array();
    private $totalLista = 0;
    private $totalRegistros = 0;

    function getCodigo()
    {
        return $this->codigo;
    }
    function getDescricao()
    {
        return $this->descricao;
    }
    function getLocalizacao()
    {
        return $this->localizacao;
    }

    function setCodigo($valor)
    {
        $this->codigo = $valor;
    }
    function setDescricao($valor)
    {
        $this->descricao = $valor;
    }
    function setLocalizacao($valor)
    {
        $this->localizacao = $valor;
    }

    function getLista()
    {
        return $this->lista;
    }
    function getTotalLista()
    {
        return $this->totalLista;
    }
    function getTotalRegistros()
    {
        return $this->totalRegistros;
    }
    function getItemLista($index)
    {
        if (isset($this->lista[$index]))
            return $this->lista[$index];
        else
            return new ocorrenciaESocial();
    }

    function buscar($filtro = null, $limite = null, $ordem = null)
    {
        $select = array(
            "A.UNIDADE", "A.AMBIENTE", "A.EVENTO", "A.CHAVE", "A.ITEM",
            "A.CODIGO", "A.DESCRICAO", "A.LOCALIZACAO", "A.EMPRESA"
        );
        $fb   = new FB("T_ESOCIAL_OCORRENCIAS A");

        $ret = $fb->select($filtro, null, null, null, null, null, false);
        $this->totalRegistros = count($ret);


        $ret = $fb->select($filtro, $limite, $ordem, null, $select);

        for ($i = 0; $i < count($ret); $i++) {
            $item = new ocorrenciaESocial();
            $item->setCodigo($ret[$i]["CODIGO"]);
            $item->setDescricao($ret[$i]["DESCRICAO"]);
            $item->setLocalizacao($ret[$i]["LOCALIZACAO"]);

            array_push($this->lista, $item);
        }

        $this->totalLista = count($this->lista);
        return $this->totalLista > 0;
    }

    function remover()
    {
    }
    function alterar()
    {
    }
    function inserir()
    {
    }
    function deletarOcorrencias($unidade, $empresa, $chave, $marcar_processar)
    {
        $fb = new FB("T_ESOCIAL_OCORRENCIAS A");
        $fb->delete([
            'A.UNIDADE' => $unidade,
            'A.EMPRESA' => $empresa,
            'A.CHAVE' =>  $chave
        ], false);

        if ($marcar_processar) {
            $fb = new FB("T_ESOCIAL");
            $fb->update([
                'SITUACAO' => 'P',
                'FLAG_REPROCESSAR_WEB' => 'S'
            ], [
                'UNIDADE' => $unidade,
                'EMPRESA' => $empresa,
                'CHAVE' =>  $chave

            ], false);
        }
    }
}
