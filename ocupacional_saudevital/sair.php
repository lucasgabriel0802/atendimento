<?php
    require_once("class/config.inc.php");

    config::verificaLogin();

    if (isset($_POST["a"])){
        $acao = $_POST["a"];
        
        if ($acao == "sair"){
            unset($_SESSION[config::getSessao()]);
            sleep(1);
            echo json_encode(array("codigo" => 0));
        }
        
        die;
    }
    
?>

<div class="page-head">
    <h2>Saindo, por favor aguarde...</h2>
</div>

<script>
    $(document).ready(function(){
       $.ajax({
            type: 'post',
            url: 'sair.php',
            data: 'a=sair',
            dataType: 'json',
            async: true,
            cache: false
        }).done(function(data){
            if (data.codigo == 0)
                window.location.href = 'index.php';
        });  
    });
</script>