<?php
    require_once("class/config.inc.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="img/favicon.png">
        <title><?php echo config::getTituloSite(); ?></title>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,400italic,700,800" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Raleway:300,200,100" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/nanoscroller.css">
        <link rel="stylesheet" href="css/style.css">    
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.nanoscroller.js"></script>
        <script type="text/javascript" src="js/cleanzone.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>    
        <!--if lt IE 9script(src='js/html5shiv.js')-->
    </head>
    <body class="texture">
        <div id="cl-wrapper" class="error-container" style="background: url('img/bg.jpg')">
            <div class="page-error">
                <h1 class="number text-center">403</h1>
                <h2 class="description text-center">Sua sessão expirou ou você não tem permissão para acessar essa página!</h2>
                <h3 class="text-center"><a href=".">Clique aqui</a> para iniciar!</h3>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function(){
                App.init();
                $('.error-container').css('height', $(window).height() - 50);
            });
        </script>
    </body>
</html>