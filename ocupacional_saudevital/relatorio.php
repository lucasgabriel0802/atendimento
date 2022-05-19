<?php
    require_once("class/config.inc.php");
    require_once("class/funcoes.class.php");
    require_once("class/agenda.class.php");
    require_once("class/funcionario.class.php");
    require_once("class/exameweb.class.php");
    require_once("class/funcexamesacomp.class.php");
    require_once("class/unidade.class.php");
    require_once("class/empresa.class.php");
    require_once("class/medico.class.php");
    require_once("class/fpdf/fpdf.php");

    config::verificaLogin();

    $relat = "";
    if (isset($_GET["r"]))
        $relat = $_GET["r"];
    
    if ($relat == "requisicao"){
        $retorno = array("codigo" => 0, "mensagem" => "");

        if (isset($_GET["codigo"]) && isset($_GET["hora"])){
            if (is_numeric($_GET["codigo"])){
                
                if (funcoes::validarHora($_GET["hora"])){   

                    $pdf = new FPDF("P", "mm", "A4");
                    $pdf->AddPage();
                    
                    $ag = new agenda();
                    if ($ag->buscar(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                          "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                          "A.CODIGO" => $_GET["codigo"], 
                                          "A.HORA" => $_GET["hora"]))){
                        
                        $ag = $ag->getItemLista(0);

                        $un = new unidade();
                        if ($un->buscar(array("A.CODIGO" => $ag->getUnidade()->getCodigo())))
                            $un = $un->getItemLista(0);

                        $fu = new funcionario();
                        if ($fu->buscar(array("A.UNIDADE" => $ag->getUnidade()->getCodigo(),
                                              "A.EMPRESA" => $ag->getEmpresa()->getCodigo(),
                                              "A.MATRICULA" => $ag->getFuncionario()->getCodigo()), 1))
                            $fu = $fu->getItemLista(0);

                        $exweb = new exameweb();
                        if ($exweb->buscar(array("A.UNIDADE" => $ag->getUnidade()->getCodigo())))
                            $exweb = $exweb->getItemLista(0);

                        $fe = new funcexamesacomp();

                        $fe->buscarExames($ag->getEmpresa()->getCodigo(), 
                                          $ag->getUnidade()->getCodigo(), 
                                          $ag->getPostoTrabalho()->getCodigo(), 
                                          $ag->getFuncionario()->getCodigo(), 
                                          ($ag->getTipo() == 'M' ? $ag->getNovoSetor()->getCodigo() : $ag->getSetor()->getCodigo()), 
                                          ($ag->getTipo() == 'M' ? $ag->getNovaFuncao()->getCodigo() : $ag->getFuncao()->getCodigo()),  
                                          $ag->getTipo(), $ag->getData());

                        $fe->buscar(array("A.UNIDADE" => $ag->getUnidade()->getCodigo(),
                                          "A.EMPRESA" => $ag->getEmpresa()->getCodigo(),
                                          "A.MATRICULA" => $ag->getFuncionario()->getCodigo()), null, array("B.DESCRICAO" => "ASC"));
                        
                        $em = new empresa;
                        if ($em->buscar(array("A.UNIDADE" => $ag->getUnidade()->getCodigo(), "A.CODIGO" => $ag->getEmpresa()->getCodigo()), 1))
                            $em = $em->getItemLista(0);

                        $me = new medico();
                        if ($me->buscar(array("A.CODIGO" => $ag->getMedico()->getCodigo())))
                            $me = $me->getItemLista(0);
                        
                        if (config::getModeloRequisicao() == 1){
                            $pdf->SetMargins(12, 12, 12); 
                            
                            $pdf->Image("img/logo-relatorio.jpg", 12, 12, 31, 30);
                            $pdf->SetY(12);
                            $pdf->SetFont("Arial", "IB", 16);
                            $pdf->Cell(190, 30, utf8_decode("Requisição de exames"), false, false, "C");

                            $pdf->SetY(45);
                            $pdf->SetFont("Arial", "I", 11);
                            $pdf->Cell(31, 6, utf8_decode("Empresa:"), "", false, "R");
                            $pdf->SetFont("Arial", "IB");
                            $pdf->Cell(155, 6, $ag->getEmpresa()->getNomeFantasia(), "", true);

                            $pdf->SetFont("Arial", "I");
                            $pdf->Cell(31, 6, utf8_decode("Funcionário:"), "", false, "R");
                            $pdf->SetFont("Arial", "IB");
                            $pdf->Cell(85, 6, $fu->getNome(), "", true);
//                            $pdf->SetFont("Arial", "I");
//                            $pdf->Cell(15, 6, utf8_decode("RG:"), "", false, "R");
//                            $pdf->SetFont("Arial", "IB");
//                            $pdf->Cell(55, 6, $fu->getRG(), "", true);
//
//                            $pdf->SetFont("Arial", "I");
//                            $pdf->Cell(31, 6, utf8_decode("Cart. Trab/Série:"), "", false, "R");
//                            $pdf->SetFont("Arial", "IB");
//                            $pdf->Cell(85, 6, $fu->getCTPS());
//                            $pdf->SetFont("Arial", "I");
//                            $pdf->Cell(15, 6, utf8_decode("PIS:"), "", false, "R");
//                            $pdf->SetFont("Arial", "IB");
//                            $pdf->Cell(55, 6, $fu->getPIS(), "", true);;

                            $pdf->SetFont("Arial", "I");
                            $pdf->Cell(31, 6, utf8_decode("Função:"), "", false, "R");
                            $pdf->SetFont("Arial", "IB");
                            $pdf->Cell(155, 6, $fu->getFuncao()->getNome(), "", true);

                            $pdf->SetFont("Arial", "I");
                            $pdf->Cell(31, 6, utf8_decode("Setor:"), "", false, "R");
                            $pdf->SetFont("Arial", "IB");
                            $pdf->Cell(155, 6, $fu->getSetor()->getNome(), "", true);

                            $pdf->SetFont("Arial", "I");
                            $pdf->Cell(31, 6, utf8_decode("Emitida em:"), "", false, "R");
                            $pdf->SetFont("Arial", "IB");
                            $pdf->Cell(78, 6, date("d/m/Y H:i:s"));
                            $pdf->SetFont("Arial", "I");
                            $pdf->Cell(22, 6, utf8_decode("Admissão:"), "", false, "R");
                            $pdf->SetFont("Arial", "IB");
                            
                            if (funcoes::validarData($fu->getDataAdmissao()))
                                $pdf->Cell(55, 6, date("d/m/Y", strtotime($fu->getDataAdmissao())), "", true);
                            else
                                $pdf->Cell(55, 6, "", "", true);
                            
                            $pdf->SetFont("Arial", "I");
                            $pdf->Cell(31, 6, utf8_decode("Requisitado por:"), "", false, "R");

                            $responsavel = $ag->getResponsavelAgendamento();
                            if (strpos($ag->getResponsavelAgendamento(), " "))
                                $responsavel = substr($ag->getResponsavelAgendamento(), 0, strpos($ag->getResponsavelAgendamento(), " "));

                            $pdf->SetFont("Arial", "IB");
                            $pdf->Cell(145, 6, $responsavel, "", true);                            

                            if ($ag->getTipo() == "M"){
                                $pdf->SetFont("Arial", "I");
                                $pdf->Cell(31, 6, utf8_decode("Nova função:"), "", false, "R");
                                $pdf->SetFont("Arial", "IB");
                                $pdf->Cell(155, 6, $ag->getNovaFuncao()->getNome(), "", true);

                                $pdf->SetFont("Arial", "I");
                                $pdf->Cell(31, 6, utf8_decode("Novo setor:"), "", false, "R");
                                $pdf->SetFont("Arial", "IB");
                                $pdf->Cell(155, 6, $ag->getNovoSetor()->getNome(), "", true);
                            }

                            $pdf->Ln();
                            $pdf->SetFont("Arial", "IB");
                            $pdf->Cell(190, 6, utf8_decode("O portador desta requisição está autorizado a realizar os exames abaixo:"), "", true);

                            $qtdeExame = 0;
                            $pdf->SetY($pdf->GetY() + 2);
                            $pdf->SetFont("Arial", "I");
                            if ($exweb->permiteAdmissional()){
                                $pdf->Cell(4.5, 4.5, ($ag->getTipo() == "A" ? "X" : ""), 1);
                                $pdf->Cell(43.5, 5, utf8_decode("Admissional"));
                                $qtdeExame++;
                            }
                            if ($exweb->permitePeriodico()){
                                $pdf->Cell(4.5, 4.5, ($ag->getTipo() == "P" ? "X" : ""), 1);
                                $pdf->Cell(43.5, 5, utf8_decode("Periódico"));
                                $qtdeExame++;
                            }
                            if ($exweb->permiteMudancaFuncao()){
                                $pdf->Cell(4.5, 4.5, ($ag->getTipo() == "M" ? "X" : ""), 1);
                                $pdf->Cell(43.5, 5, utf8_decode("Mudança de função"));
                                $qtdeExame++;
                            }
                            if ($exweb->permiteConsultaClinica()){
                                $pdf->Cell(4.5, 4.5, ($ag->getTipo() == "C" ? "X" : ""), 1);
                                $pdf->Cell(43.5, 5, utf8_decode("Consulta clínica"));
                                $qtdeExame++;
                            }
                            if ($exweb->permiteDemissional()){
                                if ($qtdeExame == 4){
                                    $pdf->Ln();
                                    $pdf->SetY($pdf->GetY() + 2);
                                    $qtdeExame = 0;
                                }
                                $pdf->Cell(4.5, 4.5, ($ag->getTipo() == "D" ? "X" : ""), 1);
                                $pdf->Cell(43.5, 5, utf8_decode("Demissional"));
                                $qtdeExame++;
                            }
                            if ($exweb->permiteRetornoTrabalho()){
                                if ($qtdeExame == 4){
                                    $pdf->Ln();
                                    $pdf->SetY($pdf->GetY() + 2);
                                    $qtdeExame = 0;
                                }
                                $pdf->Cell(4.5, 4.5, ($ag->getTipo() == "R" ? "X" : ""), 1);
                                $pdf->Cell(43.5, 5, utf8_decode("Retorno ao trabalho"));
                                $qtdeExame++;
                            }
                            if ($exweb->permiteIndefinido()){
                                if ($qtdeExame == 4){
                                    $pdf->Ln();
                                    $pdf->SetY($pdf->GetY() + 2);
                                    $qtdeExame = 0;
                                }
                                $pdf->Cell(4.5, 4.5, ($ag->getTipo() == "A" ? "I" : ""), 1);
                                $pdf->Cell(43.5, 5, utf8_decode("Indefinido"));
                                $qtdeExame++;
                            }

                            $pdf->Ln();
                            $pdf->Ln();

                            if ($fe->getTotalLista() > 0){
                                $pdf->SetFont("Arial", "IB");
                                $pdf->Cell(190, 6, "Complementares:");
                                $pdf->SetFont("Arial", "I");

                                for ($i = 0; $i < $fe->getTotalLista(); $i++){
                                    $pdf->Ln();
                                        $pdf->Cell(95, 6, $fe->getItemLista($i)->getExame()->getDescricao());
                                }
                            }

                            $pdf->Ln();
                            $pdf->Ln();
                            $pdf->SetFontSize(16);
                            $pdf->SetFont("Arial", "IB");
                            $pdf->SetFillColor(200, 200, 200);
                            $pdf->Cell(186, 10, utf8_decode("Agendado para o dia ".date("d/m/Y", strtotime($ag->getData()))." às ".$ag->getHora()), "", true, "C", true);
                            
                            $pdf->SetFont("Arial", "IB", 11);
                            $pdf->Cell(190, 6, utf8_decode("Favor não esquecer documento de identidade!"), "", true, "C");
                            
                            if ($em->getExibirValorAtraso() || $em->getExibirValorFalta()) {
                                $mensagem2 = "";
                                if ($em->getExibirValorAtraso() && !$em->getExibirValorFalta()) {
                                    $mensagem = "Em caso de atraso será cobrado o valor de R$ " . number_format($em->getValorAtraso(), 2, ",", ".");
                                } else
                                    if ($em->getExibirValorFalta() && !$em->getExibirValorAtraso()) {
                                        $mensagem = "Em caso de falta será cobrado o valor de R$ " . number_format($em->getValorFalta(), 2, ",", ".");
                                    } else {
                                        $mensagem = "Em caso de falta, será cobrado o valor de R$ " . number_format($em->getValorFalta(), 2, ",", ".") . ".";
                                        $mensagem2 = "Em caso de atraso, será cobrado o valor de R$ " . number_format($em->getValorAtraso(), 2, ",", ".") . ".";
                                    }
                                
                                $pdf->SetFont("Arial", "IB", 11);
                                $pdf->Cell(190, 6, utf8_decode($mensagem), "", true, "C");
                                
                                if (!empty($mensagem2)) {
                                    $pdf->SetFont("Arial", "IB", 11);
                                    $pdf->Cell(190, 6, utf8_decode($mensagem2), "", true, "C");
                                }
                            }

                            $pdf->Ln();
                            $pdf->Ln();
                            $pdf->SetFont("Arial", "I", 8);
                            $pdf->Cell(186, 4, "(assinatura e carimbo da empresa)", "T", true, "C");

                            $pdf->Ln();
                            $pdf->SetFont("Arial", "I");
                            $pdf->Cell(18, 4, utf8_decode("ENDEREÇO:"));

                            $end = $un->getEndereco();
                            if ($un->getNumero() != "")
                                $end .= ", ".$un->getNumero();
                            if ($un->getComplemento() != "")
                                $end .= " - ".$un->getComplemento();
                            $end .= " - ".$un->getCidade();

                            $pdf->SetFont("Arial", "IB");
                            $pdf->Cell(77, 4, $end);
                            $pdf->Ln();
                            $pdf->SetFont("Arial", "I", 8);
                            $pdf->Cell(190, 4, utf8_decode("Documento de uso exclusivo da ".utf8_encode($un->getNomeFantasia())));
                            $pdf->Ln();

                            $pdf->Rect(10, 10, 190, $pdf->GetY() - 8);
                        }else
                            if (config::getModeloRequisicao() == 2){
                                $pdf->SetMargins(10, 10, 10); 
                                $pdf->SetLineWidth(0.01);
                                
                                $pdf->SetFont("Arial", "", 16);
                                $pdf->SetXY(41, 10);
                                $pdf->Cell(159, 30, "GUIA  DE  ENCAMINHAMENTO", 1, 0, "C");
                                $pdf->Cell(169, 5, "", 0, 0, "C");
                                $pdf->SetY(45);

                                $tipoExame = "";
                                if($ag->getTipo() == "A")
                                    $tipoExame = "ADMISSIONAL";
                                else
                                    if($ag->getTipo() == "D")
                                        $tipoExame = "DEMISSIONAL";
                                    else
                                        if($ag->getTipo() == "P")
                                            $tipoExame = "PERIÓDICO";
                                        else
                                            if($ag->getTipo() == "M"){
                                                $tipoExame = "MUDANÇA DE FUNÇÃO";
                                            }else
                                                if($ag->getTipo() == "R")
                                                    $tipoExame = "RETORNO AO TRABALHO";

                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(30, 5, "Empresa:", 0, false, "R");
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(156, 5, "{$em->getNomeFantasia()} ({$em->getCodigo()})", 0, true, "L");
                                
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(30, 5, utf8_decode('Funcionário:'), 0, false, "R");
                                $pdf->Cell(156, 5, trim($ag->getFuncionario()->getNome())." ({$ag->getFuncionario()->getCodigo()})", 0, true, "L");

                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(30, 5, "Setor:", 0, false, "R");
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(156, 5, "{$ag->getSetor()->getNome()}", 0, true, "L");

                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(30, 5, utf8_decode('Função:'), 0, false, "R");
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(156, 5, "{$ag->getFuncao()->getNome()}", 0, true, "L");

                                if ($ag->getTipo() == "M"){
                                    $pdf->SetFont("Arial", "B", 10);
                                    $pdf->Cell(30, 5, "Novo setor:", 0, false, "R");
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(156, 5, "{$ag->getNovoSetor()->getNome()}", 0, true, "L");

                                    $pdf->SetFont("Arial", "B", 10);
                                    $pdf->Cell(30, 5, utf8_decode('Nova função:'), 0, false, "R");
                                    $pdf->SetFont("Arial", "", 10);
                                    $pdf->Cell(156, 5, "{$ag->getNovaFuncao()->getNome()}", 0, true, "L");
                                }
                                
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->Cell(30, 5, "Tipo de exame:", 0, false, "R");
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Cell(156, 5, utf8_decode($tipoExame), 0, true, "L");

                                $pdf->Ln();
                                $pdf->SetFont("Arial", "B", 14);
                                $pdf->SetFillColor(220, 220, 220);
                                $pdf->Cell(190, 9, "DATA E HORA DO EXAME: ".utf8_decode(date("d/m/Y", strtotime($ag->getData()))." às ".$ag->getHora()), 1, true, "C", true);

                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->SetY($pdf->GetY() + 3);
                                $pdf->Cell(70, 5, "EXAME", "LBT", false, "L", true);
                                $pdf->Cell(120, 5, utf8_decode('OBSERVAÇÕES'), true, false, "L", true);
                                $pdf->SetFont("Arial", "", 10);
                                $pdf->Ln();
                                
                                $pdf->SetFont("Arial", "", 9);
                                for ($i = 0; $i < $fe->getTotalLista(); $i++){
                                    // primero escreve as orientações pra pegar quantas linhas vai usar
                                    $yAnt = $pdf->GetY();
                                    $pdf->SetX(80);
                                    $pdf->MultiCell(120, 5, $fe->getItemLista($i)->getExame()->getOrientacoes(), true, "L");
                                    $yDep = $pdf->GetY();
                                    
                                    // então volta pra linha inicial
                                    $pdf->SetXY(10, $yAnt);
                                    
                                    // calcula a quantidade de linhas
                                    $linhas = ($yDep - $yAnt) / 5;
                                    $desc = $fe->getItemLista($i)->getExame()->getDescricao();
                                    
                                    // insere quebra de linha conforme a quantidade de linhas das orientações
                                    for ($j = 1; $j < $linhas; $j++)
                                        $desc .= " \n\n";
                                    
                                    // escreve a descrição com as quebras
                                    $pdf->MultiCell(70, 5, $desc, true, "L");
                                    
                                }
                                 
                                $end = $me->getEndereco()->getEndereco();
                                if ($me->getEndereco()->getNumero() != "")
                                    $end .= ", ".$me->getEndereco()->getNumero();
                                
                                if ($me->getEndereco()->getComplemento() != "")
                                    $end .= " - ".$me->getEndereco()->getComplemento();
                                
                                $end .= " - {$me->getEndereco()->getCidade()} - {$me->getEndereco()->getUF()}";
                                
                                $pdf->SetFont("Arial", "B", 10);
                                $pdf->SetY($pdf->GetY() + 3);
                                $pdf->Cell(190, 5, utf8_decode("ENDEREÇO PARA ATENDIMENTO"), true, false, "L", true);
                                $pdf->SetFont("Arial", "", 9);
                                $pdf->Ln();
                                $pdf->Cell(190, 8, $end, true, true, "L");
                                
                                if (trim($me->getObservacao()) != ""){
                                    $pdf->SetFont("Arial", "B", 10);
                                    $pdf->SetY($pdf->GetY() + 3);
                                    $pdf->Cell(190, 5, utf8_decode("PONTOS DE REFERÊNCIA"), true, false, "L", true);
                                    $pdf->SetFont("Arial", "", 9);
                                    $pdf->Ln();
                                    $pdf->MultiCell(190, 5, $me->getObservacao(), true, "L");
                                }
                                
                                $pdf->Image("img/logo-relatorio.jpg", 10 ,10, 31, 30, "", "http://www.saudevital.med.br");
                                $pdf->Rect(10, 10, 190, $pdf->GetY() - 10);
                            }
                        
                        if (funcoes::acessoCelular())
                            $pdf->Output("D");
                        else
                            $pdf->Output();
                    }else{
                        $retorno["codigo"] = 4;
                        $retorno["mensagem"] = "Agendamento não encontrado!";
                    }
                    
                }else{
                    $retorno["codigo"] = 3;
                    $retorno["mensagem"] = "Alguns parâmetros são inválidos!";
                }
                
            }else{
                $retorno["codigo"] = 2;
                $retorno["mensagem"] = "Alguns parâmetros são inválidos!";
            }
        }else{
            $retorno["codigo"] = 1;
            $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
        }
        
        if ($retorno["codigo"] > 0): ?>
            <div style="width: 100%; margin-top: 30px; text-align: center; font-family: verdana; font-size: 13px;">
                <?php echo $retorno["mensagem"]." #".$retorno["codigo"]; ?>
            </div>
        <?php
        endif;         
    }
?>