<?php

/**
 * @author Kayser Informatica
 */
interface iface {
    function getLista();
    function getTotalLista();
    function getTotalRegistros();
    function getItemLista($index);
    function buscar($filtro = null, $limite = null, $ordem = null);
    function remover();
    function alterar();
    function inserir();
}
