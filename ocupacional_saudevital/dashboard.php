<?php
    require_once("class/config.inc.php");
    require_once("class/agenda.class.php");
    require_once("class/funcionario.class.php");
    require_once("class/fatura.class.php");
    require_once("class/titreceb.class.php");
    require_once("class/funcoes.class.php");
    require_once("class/tabela.class.php");

    config::verificaLogin();
    
    $totalFuncionario  = 0;
    $totalFuncHomens   = 0;
    $totalFuncMulheres = 0;
    
    $fu = new funcionario();
    if ($fu->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
						  "UPPER(A.SITUACAO)" => "ATIVO",
                          "A.DATADEMISSAO" => array("NULL", "IS")), null, null, true)){
        $totalFuncionario = $fu->getTotalRegistros();
    }
    if ($fu->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
						  "UPPER(A.SITUACAO)" => "ATIVO",
                          "A.DATADEMISSAO" => array("NULL", "IS"),
                          "A.SEXO" => "M"), null, null, true)){
        $totalFuncHomens = $fu->getTotalRegistros();
    }
    if ($fu->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
						  "UPPER(A.SITUACAO)" => "ATIVO",
                          "A.DATADEMISSAO" => array("NULL", "IS"),
                          "A.SEXO" => "F"), null, null, true)){
        $totalFuncMulheres = $fu->getTotalRegistros();
    }
    
    if ($totalFuncionario > 0){
        $percHomens = ($totalFuncHomens * 100) / $totalFuncionario;
        $percMulheres = ($totalFuncMulheres * 100) / $totalFuncionario;
    }else{
        $percHomens = 0;
        $percMulheres = 0;
    }
    
    if (($percHomens + $percMulheres) != 100){
        if (($percHomens > 0) && ($percMulheres > 0)){
            if ($percHomens > $percMulheres)
                $percHomens = $percHomens + (100 - ($percHomens + $percMulheres));
            else
                $percMulheres = $percMulheres + (100 - ($percHomens + $percMulheres));
        }
    }
    
    $percHomens = number_format($percHomens, 2, ",", ".");
    $percMulheres = number_format($percMulheres, 2, ",", ".");
    
    $valorUltimaFatura = 0;
    $situacaoUltimaFatura = "";
    $fa = new fatura();
    if ($fa->buscar(array("A.UNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                          "A.EMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"]), 1, array("A.NUMERO" => "DESC"))){
        $valorUltimaFatura = number_format($fa->getItemLista(0)->getValor(), 2, ",", ".");
        
        $ti = new titreceb();
        if ($ti->buscar(array("A.UNIDADE" => $fa->getItemLista(0)->getUnidade()->getCodigo(),
                              "A.EMPRESA" => $fa->getItemLista(0)->getEmpresa()->getCodigo(),
                              "A.TITULO"  => $fa->getItemLista(0)->getNumero(),
                              "A.PARCELA" => "1",
                              "A.DATAEMISSAO" => $fa->getItemLista(0)->getDataEmissao(),
                              "A.TIPOTITULO"  => "03",
                              "A.SITUACAO"    => array(array("A", "P"), "IN")))){
            if ($ti->getItemLista(0)->getSituacao() == "A")
                $situacaoUltimaFatura = "Aberto";
            else
                if ($ti->getItemLista(0)->getSituacao() == "P")
                    $situacaoUltimaFatura = "Paga";
                else
                    $situacaoUltimaFatura = "&nbsp;";
        }else
            $situacaoUltimaFatura = "&nbsp;";
        
    }
?>

<!--<div class="page-head">
    <h2>Dashboard</h2>
    <ol class="breadcrumb">
        <li class="active">Início</li>
    </ol>
</div>-->
<div class="cl-mcont">
    <div class="stats_bar">
        <div class="butpro butstyle">
            <div class="sub">
                <h2>FUNCIONÁRIOS</h2>
                <span><?php echo $totalFuncionario; ?></span>
            </div>
            <div class="stat">
                <i class="fa fa-users"></i> Somente ativos
            </div>
        </div>
        <div class="butpro butstyle">
            <div class="sub">
                <h2>HOMENS</h2>
                <span><?php echo $totalFuncHomens ?></span>
            </div>
            <div class="stat">
                <i class="fa fa-user" style="color: #00F;"></i> <?php echo $percHomens; ?>%
            </div>
        </div>
        <div class="butpro butstyle">
            <div class="sub">
                <h2>MULHERES</h2>
                <span><?php echo $totalFuncMulheres; ?></span>
            </div>
            <div class="stat">
                <i class="fa fa-user" style="color: #F0F;"></i> <?php echo $percMulheres; ?>%
            </div>
        </div>
        <?php if ($_SESSION[config::getSessao()]["faturas"] == "S"): ?>
        <div class="butpro butstyle">
            <div class="sub">
                <h2>ÚLTIMA FATURA</h2>
                <span><?php echo $situacaoUltimaFatura; ?></span>
            </div>
            <div class="stat">
                <i class="fa fa-money" style="color: #0F0;"></i> R$ <?php echo $valorUltimaFatura; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="row dash-cols">
        <div class="col-sm-12 col-md-12 col-lg-6">
            <div class="block">
                <div class="header no-border">
                    <h2>Histórico de agendamento</h2>
                </div>
                <div class="content full-width">
                    <div id="legenda-estatisticas-agenda" class="legend-container"></div>
                    <div id="estatisticas-agenda" style="height:180px;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-6">
            <div class="block">
                <div class="header no-border">
                    <h2>Próximos agendamentos</h2>
                </div>
                <div class="content full-width" style="<?php if (!funcoes::acessoCelular()) echo "height: 288px; overflow-y: scroll"; ?>">
                    <?php
                        $ag = new agenda();
                        if ($ag->buscar(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                              "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                              "A.DATA" => array(date("Y-m-d", strtotime("+7 day")), "BETWEEN CURRENT_DATE AND")), null, 
                                        array("A.DATA" => "ASC", "A.HORA" => "ASC"))){
                            
                            $tab = new tabela(array("class" => "table no-border table-hover table-responsive"));
                            $cab = new cabecalho(array("class" => "no-border"));
                            $cab->addItem("Data", array("style" => "width: 65px; font-weight: bold; text-align: center;"));
                            $cab->addItem("Horário", array("style" => "width: 65px; font-weight: bold; text-align: center;"));
                            $cab->addItem("Funcionário", array("style" => "font-weight: bold"));
                            $tab->addCabecalho($cab);
                            
                            for ($i = 0; $i < $ag->getTotalLista(); $i++){
                                $datahora = strtotime($ag->getItemLista($i)->getData()." ".$ag->getItemLista($i)->getHora());
                                
                                if ($datahora >= time()){
                                    $reg = new registro(array("class" => "no-border-y"));
                                    $reg->addItem(date("d/m/Y", strtotime($ag->getItemLista($i)->getData())), array("style" => "font-weight: bold; text-align: center;"));
                                    $reg->addItem($ag->getItemLista($i)->getHora(), array("style" => "font-weight: bold; text-align: center;"));

                                    if (funcoes::acessoCelular())
                                        $reg->addItem(utf8_encode(funcoes::separarPrimeiroUltimoNome($ag->getItemLista($i)->getFuncionario()->getNome())));
                                    else
                                        $reg->addItem(utf8_encode($ag->getItemLista($i)->getFuncionario()->getNome()));

                                    $tab->addRegistro($reg);
                                }
                            }
                            
                            echo $tab->gerar();
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
    function showTooltip(x, y, contents) {
        $("<div id='tooltip'>" + contents + "</div>").css({
            'position': 'absolute',
            'display': 'none',
            'top': y + 5,
            'left': x - 55,
            'border': '1px solid #000',
            'padding': '5px',
            'color': '#fff',
            'border-radius':'2px',
            'font-size':'11px',
            'background-color': '#000',
            'opacity': 0.8,
            'width' : '110px'
        }).appendTo("body").fadeIn(200);
    }
    $('document').ready(function(){
        var agendamento = [ 
            <?php
                $ag = new agenda();
                if ($ag->buscarEstatisticas(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                  "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                  "A.DATA"          => array(date("Y-m-d", strtotime("-15 days")), ">=")))){
                    $quantidades = array();
                    for ($i = 0; $i < $ag->getTotalLista(); $i++)
                        $quantidades[$ag->getItemLista($i)->getData()] = $ag->getItemLista($i)->getQuantidade();

                    $datas = array();
                    for ($i = strtotime(date("Y-m-d", strtotime("-15 days"))); $i <= strtotime(date("Y-m-d")); $i = strtotime("+1 day", $i)){
                        $qtde = 0;
                        
                        if (isset($quantidades[date("Y-m-d", $i)]))
                            $qtde = $quantidades[date("Y-m-d", $i)];
                        
                        array_push($datas, "['".($i * 1000)."', {$qtde}]");
                    }
                    echo implode(",", $datas);
                }
            ?>
        ];
        
        var faltas = [ 
            <?php
                $ag = new agenda();
                if ($ag->buscarEstatisticas(array("A.CODIGOUNIDADE" => $_SESSION[config::getSessao()]["unidade"],
                                                  "A.CODIGOEMPRESA" => $_SESSION[config::getSessao()]["empresa_ativa"],
                                                  "A.DATA"          => array(date("Y-m-d", strtotime("-15 days")), ">="),
                                                  "A.STATUS"        => "F"))){
                    $quantidades = array();
                    for ($i = 0; $i < $ag->getTotalLista(); $i++)
                        $quantidades[$ag->getItemLista($i)->getData()] = $ag->getItemLista($i)->getQuantidade();

                    $datas = array();
                    for ($i = strtotime(date("Y-m-d", strtotime("-15 days"))); $i <= strtotime(date("Y-m-d")); $i = strtotime("+1 day", $i)){
                        $qtde = 0;
                        
                        if (isset($quantidades[date("Y-m-d", $i)]))
                            $qtde = $quantidades[date("Y-m-d", $i)];
                        
                        array_push($datas, "['".($i * 1000)."', {$qtde}]");
                    }
                    echo implode(",", $datas);
                }
            ?>
        ];
      
        var plot_statistics = $.plot($("#estatisticas-agenda"), [{
                data: agendamento, showLabels: true, label: "Agendamento", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
            },{
                data: faltas, showLabels: true, label: "Falta", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
            }], {
                series: {
                    lines: {
                        show: true,
                        lineWidth: 0.5, 
                        fill: true,
                        fillColor: { colors: [{ opacity: 0.5 }, { opacity: 0.5 }] }
                    },
                    fillColor: "rgba(0, 0, 0, 1)",
                    points: {
                        show: true,
                        fill: true
                    },
                    shadowSize: 2
                },
                legend:{
                    show: true,
                    position:"nw",
                    backgroundColor: "green",
                    container: $("#legenda-estatisticas-agenda")
                },
                grid: {
                    show: true,
                    margin: 0,
                    labelMargin: 0,
                    axisMargin: 0,
                    hoverable: true,
                    clickable: true,
                    tickColor: "rgba(245, 245, 245, 1)",
                    borderWidth: 0
                },
                colors: ["#2494F2", "#FD6A5E"],
                xaxis: {
                    autoscaleMargin: 0,
                    ticks: <?php if (funcoes::acessoCelular()) echo "7"; else echo "15"; ?>,
                    tickDecimals: 0,
                    mode: "time",
                    timeformat: "%d/%m"
                },
                yaxis: {
                    autoscaleMargin: 0.2,
                    ticks: 5,
                    tickDecimals: 0
                }
            });
            
        $("#estatisticas-agenda").bind("plothover", function (event, pos, item) {
            var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2), y = item.datapoint[1].toFixed(2);
                    
                    var novaData = new Date(item.series.data[item.dataIndex][0] / 1);
                    novaData = $.strPad(novaData.getDate(), 2, '0') + "/" + $.strPad((novaData.getMonth() + 1), 2, '0');

                    var texto = "Dia " + novaData + "<br>" + Math.round(y) + " " + item.series.label;
                    if (parseInt(y) > 1)
                        texto = texto + "s";

                    showTooltip(item.pageX, item.pageY, texto);
                }
            }else{
                $("#tooltip").remove();
                previousPoint = null;
            }
        }); 
    });
</script>