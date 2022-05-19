<?php

    session_start();
    date_default_timezone_set("America/Sao_Paulo");

    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);

    class config{
        static function verificaLogin() {
            if (strpos($_SERVER["SCRIPT_NAME"], "index.php") > 0){
                if (isset($_SESSION[config::getSessao()])){
                    header("Location: admin.php");
                    die;
                }
            }else{
                if (!isset($_SESSION[config::getSessao()])){
                    header("Location: 403.php");
                    die;
                }
            }
        }

        private static $servidor  = "interno2/3052";
        private static $usuario   = "SYSDBA";
        private static $senha     = "masterkey";
        private static $banco     = "d:\\firebird\\teste\\ocupacional\\fb4\\newmedt.fdb";
        private static $charset   = "WIN1252";
        private static $sessao    = "+-SESSAO_AGENDA_WEB_KAYSER-+";
        private static $limPagina = 8;

        static function getServidor(){ return self::$servidor; }
        static function getUsuario(){ return self::$usuario; }
        static function getSenha(){ return self::$senha; }
        static function getBanco(){ return self::$banco; }
        static function getCharset(){ return self::$charset; }
        static function getSessao(){ return self::$sessao; }
        static function getLimitePagina(){ return self::$limPagina; }
        static function setLimitePagina($valor){ self::$limPagina = $valor; }

        private static $urlSite    = "http://agenda.kayser.com.br/";
        private static $tituloSite = "Atendimento online";

        static function getURLSite(){ return self::$urlSite; }
        static function getTituloSite(){ return self::$tituloSite; }

        private static $emailServidor  = "smtplw.com.br";
        private static $emailPorta     = 587;
        private static $emailAutentica = true;
        private static $emailUsuario   = "kayserinf";
        private static $emailSenha     = "enFyWArw2223";
        private static $emailContato   = array("lucas@kayser.com.br");

        static function getEmailServidor(){ return self::$emailServidor; }
        static function getEmailPorta(){ return self::$emailPorta; }
        static function getEmailAutentica(){ return self::$emailAutentica; }
        static function getEmailUsuario(){ return self::$emailUsuario; }
        static function getEmailSenha(){ return self::$emailSenha; }
        static function getEmailContato(){ return self::$emailContato; }

        private static $modeloRequisicao = 1;
        private static $modeloAgendamento = 1;
        private static $exibirCargoCadFuncionario = 1;
        private static $exibirRevezamentoCadFuncionario = 1;
        private static $exibirExamesVencidosVencer = 'S';
        
        static function getModeloRequisicao(){ return self::$modeloRequisicao; }
        static function getModeloAgendamento(){ return self::$modeloAgendamento; }
        static function getExibirCargoCadFuncionario(){ return self::$exibirCargoCadFuncionario; }
        static function getExibirRevezamentoCadFuncionario(){ return self::$exibirRevezamentoCadFuncionario; }
        static function getExibirExamesVencidosVencer(){ return self::$exibirExamesVencidosVencer; }

    }
?>