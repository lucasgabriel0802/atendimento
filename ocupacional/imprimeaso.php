<?php
require_once("class/config.inc.php");
require_once("class/funcoes.class.php");
require_once("class/empresa.class.php");
require_once("class/fpdf/fpdf.php");
require_once('phpqrcode/qrlib.php');
require_once("class/funcionario.class.php");
    


if (isset($_GET["documento"])) {
    $aso = $_GET["documento"];
}

if (isset($_GET["hash"])) {
    $hash = $_GET["hash"];

    //echo $hash . '<br>';

    $matricula = substr($hash, 0 ,6);
    $empresa = substr($hash, 6 ,6);
    $unidade = substr($hash, 12 ,3);
    $numeroaso = substr($hash, 15 ,10);

    /*
    echo $matricula . '<br>';
    echo $empresa . '<br>';
    echo $unidade . '<br>';
    echo $numeroaso . '<br>';

    die;
    */

    $path    = realpath(dirname(__FILE__))."\\geradoc\\DOC\\";
    $prefixo = $unidade.$empresa.$matricula;
    $arquivoPDF = $path.$prefixo.".pdf";

    $fu = new funcionario();
    if ($fu->buscar(array("A.UNIDADE" => $unidade,
                        "A.EMPRESA" => $empresa,
                        "A.MATRICULA" => $matricula))){
        $fu = $fu->getItemLista(0);
        exec(realpath(dirname(__FILE__))."\\geradoc\\GeraDoc.exe"
        . " -unidade=".$unidade
        . " -empresa=".$empresa
        . " -documento={$_GET["documento"]}"
        . " -funcionario=".$matricula
        . " -numaso=".$numeroaso);
            
        if (file_exists($arquivoPDF)){
            $novoNome = str_replace(" ", "-", funcoes::separarPrimeiroUltimoNome($fu->getNome()))."-".$_GET["documento"];
    
            header("Content-Type: application/pdf");
            if (funcoes::acessoCelular())
                header("Content-disposition: attachment; filename={$novoNome}.pdf");
            readfile($arquivoPDF);
            unlink($arquivoPDF);
        }
    }
}
else
{
    echo "NÂO encontrou ";
    die;

}

/*    
$pdf = new FPDF("P", "mm", "A4");
$pdf->AddPage();
#$pdf->SetMargins(12, 12, 12);
$ImagemQRCODE = "img/qrcode/imagem_qrcode_".$aso.".png";
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$linkQRCODE = $base_url . $_SERVER["REQUEST_URI"];
QRCode::png($linkQRCODE, $ImagemQRCODE);

$pdf->Image("img/logo-relatorio.jpg", 20, 12, 31, 30);
$pdf->SetY(12);
$pdf->SetFont("Arial", "IB", 16);
$pdf->Cell(210, 30, utf8_decode("ATESTADO DE SAÚDE OCUPACIONAL"), false, false, "C");
$pdf->SetY(15);
$pdf->Cell(200, 40, utf8_decode("ASO"), false, false, "C");
$pdf->SetFont("Arial", "", 8);
$pdf->SetY(50);
#$pdf->Cell(12, 10, $linkQRCODE, false, false, "L");
$pdf->Image($ImagemQRCODE, 170, 15, 30, 30);

$pdf->Rect(20, 50, 165, 6 );
$pdf->Rect(22, 51, 4, 4 );
$pdf->Cell(51, 7, utf8_decode('Admissional'), 0, 0, "C", False);

$pdf->Rect(48, 51, 4, 4 );
$pdf->Cell(1, 7, utf8_decode('Periódico'), 0, 0, "C", False);

$pdf->Rect(82, 51, 4, 4 );
$pdf->Cell(59, 7, utf8_decode('Ret. Trabalho'), 0, 0, "C", False);

$pdf->Rect(112, 51, 4, 4 );
$pdf->Cell(2, 7, utf8_decode('Mud. Função'), 0, 0, "C", False);

$pdf->Rect(142, 51, 4, 4 );
$pdf->Cell(55, 7, utf8_decode('Demissional'), 0, 0, "C", False);

if (funcoes::acessoCelular()) {
    $pdf->Output("D");
} else {
    $pdf->Output();
}
*/