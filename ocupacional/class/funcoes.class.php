<?php

/**
 * @author Kayser Informatica
 */
class funcoes {
    const CANONICAL = [true,false,null,null];
    
    static function somenteNumeros($string = ''){
        return preg_replace('/[^0-9]/', '', $string);
    }

    private static function tratarCertificado($key) {
        $key = str_replace([
            '-----BEGIN CERTIFICATE-----',
            '-----END CERTIFICATE-----',
            "\r\n",
            "\n",
        ], [
            '',
            '',
            '',
            ''
        ], $key);
        
        return $key;
    }
    
    private static function canonize(DOMNode $node, $canonical = self::CANONICAL) {
        return $node->C14N(
            $canonical[0],
            $canonical[1],
            $canonical[2],
            $canonical[3]
        );
    }
    
    private static function digest(DOMNode $node, $algorithm, $canonical = self::CANONICAL) {
        $c14n = self::canonize($node, $canonical);
        $hashValue = hash($algorithm, $c14n, true);
        return base64_encode($hashValue);
    }
    
    static function compactarArquivos($arrayArquivos, $pathArquivoZip) {
        $zip = new ZipArchive();
        $zip->open($pathArquivoZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        foreach ($arrayArquivos as $arquivo) {
            $nome = substr($arquivo, strrpos($arquivo,'/') + 1);
            $zip->addFile($arquivo, $nome);
        }
        
        $zip->close();
    }
    
    static function gerarId($cnpjcpf, $sequencial = 0) {
        $cnpjcpf = preg_replace('/[^\d]/', '', $cnpjcpf);
        
        if (strlen($cnpjcpf) == 14) {
            $cnpjcpf = substr($cnpjcpf, 0, 8);
        }
        
        if ($sequencial == 0) {
            $sequencial = random_int(1, 99999);
        }
        
        return '1' . str_pad($cnpjcpf, 14, '0', STR_PAD_RIGHT) . date('YmdHis') . str_pad($sequencial, 5, '0', STR_PAD_LEFT);
    }

    static function assinar(
        $certificado,
        $senha,
        $xml,
        $tag,
        $mark = 'Id',
        $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = self::CANONICAL
    ) {
//        $xml = funcoes::assinar(
//            $arqCertificado,
//            $senCertificado,
//            $xml,
//            'eSocial',
//            '',
//            OPENSSL_ALGO_SHA256,
//            [true, false, null, null]
//        );

        openssl_pkcs12_read($certificado, $certificados, $senha);
        $chavePrivada = $certificados["pkey"];
        $chavePublica = $certificados["cert"];
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $root = $dom->documentElement;
        $node = $dom->getElementsByTagName($tag)->item(0);
        
        if ($nodeSignature = $node->getElementsByTagName('Signature')->item(0)) {
            $node->removeChild($nodeSignature);
        }
        
        $nsCannonMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsSignatureMethod = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
        $nsDigestMethod = 'http://www.w3.org/2000/09/xmldsig#sha1';
        $digestAlgorithm = 'sha1';
        
        if ($algorithm == OPENSSL_ALGO_SHA256) {
            $digestAlgorithm = 'sha256';
            $nsSignatureMethod = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
            $nsDigestMethod = 'http://www.w3.org/2001/04/xmlenc#sha256';
        }
        
        $nsTransformMethod1 ='http://www.w3.org/2000/09/xmldsig#enveloped-signature';
        $nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        
        $idSigned = trim($node->getAttribute($mark));
        $digestValue = self::digest($node, $digestAlgorithm, $canonical);
        
        $signatureNode = $dom->createElementNS("http://www.w3.org/2000/09/xmldsig#", "Signature");
        $root->appendChild($signatureNode);
        
        $signedInfoNode = $dom->createElement("SignedInfo");
        $signatureNode->appendChild($signedInfoNode);
        
        $canonicalNode = $dom->createElement('CanonicalizationMethod');
        $canonicalNode->setAttribute('Algorithm', $nsCannonMethod);
        $signedInfoNode->appendChild($canonicalNode);
        
        $signatureMethodNode = $dom->createElement('SignatureMethod');
        $signatureMethodNode->setAttribute('Algorithm', $nsSignatureMethod);
        $signedInfoNode->appendChild($signatureMethodNode);

        if (!empty($idSigned)) {
            $idSigned = "#$idSigned";
        }
        
        $referenceNode = $dom->createElement('Reference');
        $signedInfoNode->appendChild($referenceNode);
        $referenceNode->setAttribute('URI', $idSigned);
        
        $transformsNode = $dom->createElement('Transforms');
        $referenceNode->appendChild($transformsNode);
        
        $transfNode1 = $dom->createElement('Transform');
        $transformsNode->appendChild($transfNode1);
        $transfNode1->setAttribute('Algorithm', $nsTransformMethod1);
        
        $transfNode2 = $dom->createElement('Transform');
        $transformsNode->appendChild($transfNode2);
        $transfNode2->setAttribute('Algorithm', $nsTransformMethod2);
        
        $digestMethodNode = $dom->createElement('DigestMethod');
        $referenceNode->appendChild($digestMethodNode);
        $digestMethodNode->setAttribute('Algorithm', $nsDigestMethod);
        
        $digestValueNode = $dom->createElement('DigestValue', $digestValue);
        $referenceNode->appendChild($digestValueNode);
        
        $c14n = self::canonize($signedInfoNode, $canonical);
        openssl_sign($c14n, $signature, $chavePrivada, $algorithm);
        $signatureValue = base64_encode($signature);
        
        $signatureValueNode = $dom->createElement('SignatureValue', $signatureValue);
        $signatureNode->appendChild($signatureValueNode);
        
        $keyInfoNode = $dom->createElement('KeyInfo');
        $signatureNode->appendChild($keyInfoNode);
        
        $x509DataNode = $dom->createElement('X509Data');
        $keyInfoNode->appendChild($x509DataNode);
        
        $x509CertificateNode = $dom->createElement('X509Certificate', self::tratarCertificado($chavePublica));
        $x509DataNode->appendChild($x509CertificateNode);
        
        return $dom->saveXML();
    }
    
    static function validarEmail($email){
        return preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $email);
    }
    static function validarData($data){
        return preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/", $data) || 
                preg_match("/^[0-9]{4}\-(0[1-9]|1[0-2])\-(0[1-9]|[1-2][0-9]|3[0-1])$/", $data);
    }
    static function validarHora($hora){
        return preg_match("/^(0[0-9]|1[0-9]|2[0-3])\:[0-5]{1}[0-9]{1}\:[0-5]{1}[0-9]{1}$/", $hora);
    }
    
    static function removerCaracteres($texto) {
        $utf8 = array(
            '/[áàâãªä]/u' =>   'a',
            '/[ÁÀÂÃÄ]/u'  =>   'A',
            '/[ÍÌÎÏ]/u'   =>   'I',
            '/[íìîï]/u'   =>   'i',
            '/[éèêë]/u'   =>   'e',
            '/[ÉÈÊË]/u'   =>   'E',
            '/[óòôõºö]/u' =>   'o',
            '/[ÓÒÔÕÖ]/u'  =>   'O',
            '/[úùûü]/u'   =>   'u',
            '/[ÚÙÛÜ]/u'   =>   'U',
            '/ç/'         =>   'c',
            '/Ç/'         =>   'C',
            '/ñ/'         =>   'n',
            '/Ñ/'         =>   'N',
            '/–/'         =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'   =>   ' ', // Double quote
            '/ /'         =>   ' ', // nonbreaking space (equiv. to 0x160)
        );

        return preg_replace(array_keys($utf8), array_values($utf8), $texto);
    }
    
    static function converterData($data, $mostrar_hora = null){
        $d1 = "";
        $h1 = "";
        if (strpos($data, " ")){
            $temp = explode(" ", $data);
            $d1 = $temp[0];
            $h1 = $temp[1];
        }else
            $d1 = $data;
        
        if (strpos($d1, "-")){
            $temp = explode("-", $d1);
            $d1 = "{$temp[2]}/$temp[1]/$temp[0]";
        }else
            if (strpos($d1, "/")){
                $temp = explode("/", $d1);
                $d1 = "{$temp[2]}-{$temp[1]}-{$temp[0]}";
            }
        
        $data = $d1;
        if($data == '')
            $data = null;
        if ($mostrar_hora)
            $data .= " {$h1}";
            
        return $data;
    }
    
    static function gerarPaginacao($pagAtual, $totReg, $acao){
        $totPagina = ceil($totReg / config::getLimitePagina());
        
        if (self::acessoCelular()){
            echo "<div class=\"form-group-sm\" style=\"margin-top: 22px;\">
                    <div class=\"col-md-3\">
                        <label>Página: </label>
                        <select class=\"form-control input-sm\" onchange=\"".str_replace("'<pagina>'", "$(this).val()", $acao)."\" style=\"width: auto; display: initial;\">";
            for ($i = 1; $i <= $totPagina; $i++){
                echo "<option value=\"{$i}\"";
                if ($pagAtual == $i)
                    echo " selected=\"selected\" ";
                echo ">{$i}</option>";
            }
            echo "      </select>
                        <label> de {$totPagina}</label>
                    </div>
                  </div>";
        }else{
            $exibRegInicial = (($pagAtual - 1) * config::getLimitePagina()) + 1;
            $exibRegFinal   = (($pagAtual) * config::getLimitePagina());

            if ($totReg == 0)
                $exibRegInicial = 0;

            if ($exibRegFinal > $totReg)
                $exibRegFinal = $totReg;

            $exibRegInicial = number_format($exibRegInicial, 0, ".", ".");
            $exibRegFinal   = number_format($exibRegFinal, 0, ".", ".");
            $exibRegTotal   = number_format($totReg, 0, ".", ".");
            echo "<table style=\"width: 100%; padding: 0px;\">
                    <tr>
                        <td style=\"padding: 0px 10px; border: 0px;\">
                            <div>
                                <small>Exibindo de {$exibRegInicial} &agrave; {$exibRegFinal} de {$exibRegTotal} registro".($exibRegTotal > 1 ? "s" : "")."</small>
                            </div>
                        </td>
                        <td style=\"text-align: right; padding: 0px 10px; border: 0px;\">";

            $ocAnte = "onclick=\"".str_replace("<pagina>", ($pagAtual - 1), $acao)."\"";
            $dsAnte = " class=\"disabled\"";
            if ($pagAtual <= 1)
                $ocAnte = "";
            else
                $dsAnte = "";

            echo "<ul class=\"pagination pagination-sm\">
                    <li{$dsAnte}>
                        <a href=\"javascript:;\" title=\"Página anterior\" {$ocAnte}>«</a>
                    </li>";

            $pagInf = $pagAtual - 2;
            if ($pagInf < 1)
                $pagInf = 1;

            $pagSup = $pagInf + 4;
            if ($pagSup > $totPagina)
                $pagSup = $totPagina;

            if (($pagSup - 2) < $pagAtual)
                $pagInf = $pagSup - 4;

            if ($pagInf < 1)
                $pagInf = 1;

            for ($i = $pagInf; $i <= $pagSup; $i++)
                echo "<li".(($i == $pagAtual) ? " class=\"active\"" : "").">
                        <a href=\"javascript:;\" id=\"pagina-ativa\" title=\"Página {$i}\" onclick=\"".str_replace("<pagina>", $i, $acao)."\">{$i}</a>
                    </li>";


            $ocProx = "onclick=\"".str_replace("<pagina>", ($pagAtual + 1), $acao)."\"";
            $dsProx = " class=\"disabled\"";
            if ($pagAtual >= $totPagina)
                $ocProx = "";
            else
                $dsProx = "";

            echo "  <li{$dsProx}>
                        <a href=\"javascript:;\" title=\"Próxima página\" {$ocProx}>»</a>
                    </li>
                </ul>";

            echo "      </td>
                    </tr>
                </table>";
        }
    }    
    
    static function validarCPF($cpf){
	$cpf = preg_replace("/[^0-9]/", "", (string) $cpf);

        if (strlen($cpf) != 11)
            return false;

        for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--)
            $soma += $cpf{$i} * $j;
            
        $resto = $soma % 11;
        if ($cpf{9} != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--)
            $soma += $cpf{$i} * $j;
            
        $resto = $soma % 11;
        return $cpf{10} == ($resto < 2 ? 0 : 11 - $resto);
    }

    static function validarPIS($pis){
        $nis = sprintf('%011s', preg_replace('{\D}', '', $pis));

        if ((strlen($nis) != 11) || (intval($nis) == 0)) {
            return false;
        }

        for ($d = 0, $p = 2, $c = 9; $c >= 0; $c--, ($p < 9) ? $p++ : $p = 2) {
            $d += $nis[$c] * $p;
        }

        return ($nis[10] == (((10 * $d) % 11) % 10));
    }
    
    static function acessoCelular(){
        $iphone  = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $ipad    = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
        $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
        $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
        $berry   = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
        $ipod    = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $symbian =  strpos($_SERVER['HTTP_USER_AGENT'],"Symbian");
        
        return ($iphone || $ipad || $android || $palmpre || $ipod || $berry || $symbian);
    }

    static function enviarEmail($de, $para, $assunto, $mensagem, &$erro = null){
        require_once("config.inc.php");
        require_once("phpmailer.class.php");
        require_once("smtp.class.php");
        
        try{
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->Host        = config::getEmailServidor();
            $mail->Port        = config::getEmailPorta();
            $mail->SMTPAuth    = config::getEmailAutentica();
            $mail->Username    = config::getEmailUsuario();
            $mail->Password    = config::getEmailSenha();
            
            //if (is_array($de))
                //$mail->setFrom($de[0], $de[0]);
            //else
                //$mail->setFrom($de, $de);
            
            $mail->setFrom(config::getEmailUsuario(), config::getEmailUsuario());
            //$mail->addReplyTo($de, $de);
            if (is_array($de))
                $mail->addReplyTo($de[0], $de[0]);
            else
                $mail->addReplyTo($de, $de);            
            if (is_array($para)){
                for ($i = 0; $i < count($para); $i++)
                    $mail->addAddress($para[$i], $para[$i]);
            }else
                $mail->addAddress($para, $para);
            $mail->Subject = $assunto;
            $mail->msgHTML($mensagem);

            if ($mail->send())
                return true;
            else{
                $erro = $mail->ErrorInfo;
                return false;
            }
        }catch (Exception $e){
            $erro = $e->getMessage();
            return false;
        }
    }
    
    static function separarPrimeiroUltimoNome($nome){
        $temp = explode(" ", trim($nome));
        
        if (count($temp) > 1){
            return $temp[0]." ".$temp[count($temp) - 1];
        }else
            return $nome;
    }
    
    static function separarPrimeiroNome($nome){
        $temp = explode(" ", trim($nome));
        
        if (count($temp) > 1){
            return $temp[0];
        }else
            return $nome;
    }

    static function montarEmailAgendamento($tipo, $data, $hora, $funcionario, $responsavel, $motivo){
        if ($tipo == "A"){
            $titulo = "CONFIRMAÇÃO DE AGENDAMENTO";
            $mensagem = "Está confirmado seu horário para o funcionário %funcionarioAgendamento% na data de %dataAgendamento% às %horaAgendamento%.<br><br>";
        }else{
            $titulo = "CANCELAMENTO DE AGENDAMENTO";
            $mensagem = "Informamos que o atendimento agendado para %dataAgendamento% às %horaAgendamento% foi cancelado.<br>".
                        "Responsável pelo cancelamento: %responsavelCancelamento%. <br>".
                        "Motivo do cancelamento: %motivoCancelamento%. <br><br>";
        }
        
        $mensagem = str_replace("%dataAgendamento%", date("d/m/Y", strtotime($data)), $mensagem);
        $mensagem = str_replace("%horaAgendamento%", $hora, $mensagem);
        $mensagem = str_replace("%funcionarioAgendamento%", utf8_encode($funcionario), $mensagem);
        $mensagem = str_replace("%responsavelCancelamento%", utf8_encode($responsavel), $mensagem);
        $mensagem = str_replace("%motivoCancelamento%", $motivo, $mensagem);
        
        $html = file_get_contents("class/modelo-email.html");
        $html = str_replace("%titulo%", $titulo, $html);
        $html = str_replace("%mensagem%", $mensagem, $html);
        
        $html = utf8_decode($html);
        
        return $html;
    }
}
