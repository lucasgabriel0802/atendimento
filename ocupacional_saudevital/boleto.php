<?php
    require_once("class/config.inc.php");
    require_once("class/titreceb.class.php");
    require_once("class/empresa.class.php");
    require_once("class/contabanco.class.php");
    require_once("class/containstrucoes.class.php");
    require_once("class/fatura.class.php");
    require_once("class/faturaitem.class.php");
    
    config::verificaLogin();
    
    if ($_SESSION[config::getSessao()]["faturas"] != "S"){
        header("Location: 403.php");
        die;
    }    
    
    $retorno = array("codigo" => 0, "mensagem" => "");
    
    if (!isset($_GET["fatura"]) || !isset($_GET["emissao"]) || !isset($_GET["parcela"])){
        $retorno["codigo"] = 1;
        $retorno["mensagem"] = "Alguns parâmetros não foram encontrados!";
    }else{
        $unidade = $_SESSION[config::getSessao()]["unidade"];
        $empresa = $_SESSION[config::getSessao()]["empresa_ativa"];
        $titulo  = $_GET["fatura"];
        $parcela = $_GET["parcela"];
        $dataemissao = $_GET["emissao"];
        $tipotitulo  = "03";

        $ti = new titreceb();
        if ($ti->buscar(array("A.UNIDADE" => $unidade, "A.EMPRESA" => $empresa,
                              "A.TITULO"  => $titulo,  "A.PARCELA" => $parcela,
                              "A.DATAEMISSAO" => $dataemissao,
                              "A.TIPOTITULO"  => $tipotitulo), 1)){
            $ti = $ti->getItemLista(0);
        
            if ($ti->getSituacao() == "A"){
                // somente faturas em aberto;
                $diasEmAtraso = time() - strtotime($ti->getDataVencimento());
                $diasEmAtraso = floor($diasEmAtraso / 60 / 60 / 24);
                if ($diasEmAtraso <= 30){
                    // vencimento com menos de 30 dias de atraso

                    $em = new empresa();
                    if ($em->buscar(array("A.UNIDADE" => $unidade, "A.CODIGO" => $empresa), 1))
                        $em = $em->getItemLista(0);

                    $fa = new fatura();
                    if ($fa->buscar(array("A.UNIDADE"     => $unidade,
                                          "A.EMPRESA"     => $empresa,
                                          "A.DATAEMISSAO" => $dataemissao,
                                          "A.NUMERO"      => $titulo), 1))
                            $fa = $fa->getItemLista(0);

                    $fi = new faturaitem();
                    $fi->buscar(array("A.UNIDADE"     => $unidade,
                                      "A.EMPRESA"     => $empresa,
                                      "A.DATAEMISSAO" => $dataemissao,
                                      "A.NUMERO"      => $titulo));

                    $cb = new contabanco();
                    if ($cb->buscar(array("A.UNIDADE" => $unidade,
                                          "A.BANCO"   => $ti->getBanco(),
                                          "A.CONTA"   => $ti->getContaBoleto()), 1))
                            $cb = $cb->getItemLista(0);

                    $ci = new containstrucoes();
                    $ci->buscar(array("A.UNIDADE" => $unidade,
                                      "A.BANCO"   => $ti->getBanco(),
                                      "A.CONTA"   => $ti->getContaBoleto()));


                    $cbx = new COM("CobreBemX.ContaCorrente"); 
                    $cbx->ArquivoLicenca = $cb->getLocalArquivo(); #@"D:/Web/agenda/06328976000174-748-A_SICREDI_SVT.conf"; // $cb->getLocalArquivo()

                    if ($cbx->UltimaMensagemErro)
                        echo($cbx->UltimaMensagemErro);

                    if ($cb->getConfiguracao1() != "")
                        $cbx->OutroDadoConfiguracao1 = $cb->getConfiguracao1();
                    if ($cb->getConfiguracao2() != "")
                        $cbx->OutroDadoConfiguracao2 = $cb->getConfiguracao2();
                    if ($cb->getPracaPagamento() != "")
                        $cbx->LocalPagamento         = $cb->getPracaPagamento();

                    $cbx->CodigoAgencia          = $cb->getAgencia();
                    $cbx->NumeroContaCorrente    = $cb->getConta();
                    $cbx->CodigoCedente          = $cb->getCodigoCedente();
                    $cbx->InicioNossoNumero      = "00000001";
                    $cbx->FimNossoNumero         = "99999999";
                    $cbx->PadroesBoleto->PadroesBoletoEmail->URLLogotipo = "/img/boletos/{$cb->getBanco()}.jpg";
                    $cbx->PadroesBoleto->PadroesBoletoEmail->URLImagensCodigoBarras = "/img/boletos/";
                    $cbx->PadroesBoleto->PadroesBoletoEmail->LayoutBoletoEmail = "PadraoHTML";

                    $cbx->DocumentosCobranca->Add();
                    $boleto                  = $cbx->DocumentosCobranca[0];
                    $boleto->NossoNumero     = trim($ti->getNossoNumero()) * 1; // converte de string pra inteiro e remove os zeros a esquerda
                    $boleto->NumeroDocumento = $ti->getTitulo();
                    $boleto->NomeSacado      = "<font face=\"Courier New\" size=\"1\">{$em->getRazaoSocial()}</font>";

                    if (strlen($em->getCNPJCPF()) > 11)
                        $boleto->CNPJSacado = $em->getCNPJCPF();
                    else
                        $boleto->CPFSacado = $em->getCNPJCPF();

                    if ($em->getUsaEnderecoCobranca() == "S"){
                        $boleto->EnderecoSacado = "<font face=\"Courier New\" size=\"1\">{$em->getEnderecoCobranca()->getEndereco()}, {$em->getEnderecoCobranca()->getNumero()} - {$em->getEnderecoCobranca()->getComplemento()}</font>";
                        $boleto->BairroSacado   = "<font face=\"Courier New\" size=\"1\">{$em->getEnderecoCobranca()->getBairro()}</font>";
                        $boleto->CidadeSacado   = "<font face=\"Courier New\" size=\"1\">{$em->getEnderecoCobranca()->getCidade()}</font>";
                        $boleto->EstadoSacado   = "<font face=\"Courier New\" size=\"1\">{$em->getEnderecoCobranca()->getUF()}</font>";
                        $boleto->CEPSacado      = "<font face=\"Courier New\" size=\"1\">{$em->getEnderecoCobranca()->getCEP()}</font>";
                    }else{
                        $boleto->EnderecoSacado = "<font face=\"Courier New\" size=\"1\">{$em->getEndereco()->getEndereco()}, {$em->getEndereco()->getNumero()} - {$em->getEndereco()->getComplemento()}</font>";
                        $boleto->BairroSacado   = "<font face=\"Courier New\" size=\"1\">{$em->getEndereco()->getBairro()}</font>";
                        $boleto->CidadeSacado   = "<font face=\"Courier New\" size=\"1\">{$em->getEndereco()->getCidade()}</font>";
                        $boleto->EstadoSacado   = "<font face=\"Courier New\" size=\"1\">{$em->getEndereco()->getUF()}</font>";
                        $boleto->CEPSacado      = "<font face=\"Courier New\" size=\"1\">{$em->getEndereco()->getCEP()}</font>";
                    }

                    $percMoradia = 0;
                    if ($ti->getPercMoraDia() > 0){
                        $percMoradia = $ti->getPercMoraDia();
                        $boleto->PercentualJurosDiaAtraso = number_format($ti->getPercMoraDia(), 2, ",", ".");
                    }else{
                        $percMoradia = $cb->getMoraDia();
                        $boleto->PercentualJurosDiaAtraso = number_format($cb->getMoraDia(), 2, ",", ".");
                    }

                    $percMultaFixa = 0;
                    if ($ti->getPercMultaFixa() > 0){
                        $percMultaFixa = $ti->getPercMultaFixa();
                        $boleto->PercentualMultaAtraso = number_format($ti->getPercMultaFixa(), 2, ",", ".");
                    }else{
                        $percMultaFixa = $cb->getMultaFixa();
                        $boleto->PercentualMultaAtraso = number_format($cb->getMultaFixa(), 2, ",", "."); 
                    }

                    $valorAcrescimo = 0;
                    if (strtotime($ti->getDataVencimento()) < strtotime(date("Y-m-d"))){
                        $diasEmAtraso = time() - strtotime($ti->getDataVencimento());
                        $diasEmAtraso = floor($diasEmAtraso / 60 / 60 / 24);

                        $valorMulta = $ti->getValorTitulo() * ($percMultaFixa / 100);
                        $valorMora  = ($ti->getValorTitulo() * ($percMoradia / 100)) * $diasEmAtraso;

                        $valorAcrescimo = $valorMulta + $valorMora;

                        $boleto->DataVencimento = date("d/m/Y");
                    }else
                        $boleto->DataVencimento = date("d/m/Y", strtotime($ti->getDataVencimento()));

                    $boleto->DataDocumento     = date("d/m/Y", strtotime($ti->getDataEmissao()));
                    $boleto->DataProcessamento = date("d/m/Y");  
                    
                    $boleto->ValorDocumento = number_format($ti->getValorTitulo() + $valorAcrescimo, 2, ",", ".");        

                    if ($cb->getDiasProtesto() > 0)
                        $boleto->DiasProtesto = $cb->getDiasProtesto();

                    $boleto->PercentualDesconto    = 0;
                    $boleto->ValorOutrosAcrescimos = 0;        

                    $boleto->PadroesBoleto->Demonstrativo = "<font size=\"2\" face=\"Courier New\">";

                    for ($i = 0; $i < $fi->getTotalLista(); $i++){
                        $valorItem = "R$ <b>".  number_format($fi->getItemLista($i)->getValorTotal(), 2, ",", ".").$fi->getItemLista($i)->getOperacao()."</b>";
                        $boleto->PadroesBoleto->Demonstrativo .= $fi->getItemLista($i)->getDescricao()." "
                                                              .  str_pad($valorItem, 86 - strlen($fi->getItemLista($i)->getDescricao()." "), ".", STR_PAD_LEFT)."<br>";
                    }

                    if ($fa->getImpostos() > 0){
                        $textoImpostos = "Impostos ";
                        $valorImpostos = " R$ <b>".  number_format($fa->getImpostos(), 2, ",", ".")."</b>";
                        $boleto->PadroesBoleto->Demonstrativo .= $textoImpostos
                                                              .  str_pad($valorImpostos, 50 - strlen($textoImpostos), ".", STR_PAD_LEFT);
                    }
                    $boleto->PadroesBoleto->Demonstrativo .= "</font>";

                    $boleto->PadroesBoleto->InstrucoesCaixa  = "<font size=\"1\" face=\"Courier New\">";
                    for ($i = 0; $i < $ci->getTotalLista(); $i++)
                        $boleto->PadroesBoleto->InstrucoesCaixa .= "<br>".$ci->getItemLista($i)->getInstrucao();
                    
                    if ($boleto->DataVencimento != date("d/m/Y", strtotime($ti->getDataVencimento())))
                        $boleto->PadroesBoleto->InstrucoesCaixa .= "<br>Vencimento original: ".date("d/m/Y", strtotime($ti->getDataVencimento()));
                    
                    if ($valorAcrescimo > 0)
                        $boleto->PadroesBoleto->InstrucoesCaixa .= "<br>Multa por atraso: ".number_format($valorAcrescimo, 2, ",", ".");
                    
                    $boleto->PadroesBoleto->InstrucoesCaixa .= "</font>";

                    echo utf8_encode($cbx->GeraHTMLBoleto(0));

                    unset($cbx);

                }else{
                    $retorno["codigo"] = 4;
                    $retorno["mensagem"] = "Não é possível re-imprimir uma fatura com mais de 30 dias de atraso!";
                }

            }else{
                $retorno["codigo"] = 3;
                $retorno["mensagem"] = "Não é possível re-imprimir um boleto já pago!";
            }
        }else{
            $retorno["codigo"] = 2;
            $retorno["mensagem"] = "Fatura não encontrada!";
        }
    }
    
    
    if ($retorno["codigo"] > 0): ?>
        <div style="width: 100%; margin-top: 30px; text-align: center; font-family: verdana; font-size: 13px;">
            <?php echo $retorno["mensagem"]." #".$retorno["codigo"]; ?>
        </div>
    <?php
    endif;    

?>
