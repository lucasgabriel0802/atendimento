<?php

    class cabecalho{
        private $itens;
        private $class;
        private $style;
        
        function getClass(){
            return $this->class;
        }
        function getStyle(){
            return $this->style;
        }
        
        function __construct($array = null) {
            $this->itens = array();
            
            if ($array != null)
                if (is_array($array)){
                    if (isset($array["class"]))
                        $this->class = $array["class"];
                    if (isset($array["style"]))
                        $this->style = $array["style"];
                }
        }

        function __destruct() {
            unset($this->itens);
        }
        
        function addItem($texto, $array = null){
            if (is_a($texto, "item")){
                array_push($this->itens, $texto);
            }else{
                $style   = null;
                $onclick = null;
                $link    = null;
                $classe  = null;
                $id      = null;
                $colspan = null;

                if ($array != null){
                    if (is_array($array)){
                        if (isset($array["style"]))
                            $style = $array["style"];

                        if (isset($array["onclick"]))
                            $onclick = $array["onclick"];

                        if (isset($array["link"]))
                            $link = $array["link"];

                        if (isset($array["class"]))
                            $classe= $array["class"];

                        if (isset($array["id"]))
                            $id = $array["id"];

                        if (isset($array["colspan"]))
                            $colspan= $array["colspan"];
                    }else
                        $texto = "Não é um array";
                }

                array_push($this->itens, new item($texto, $style, $onclick, $link, $classe, $id, $colspan));
            }
        }
        
        function getTotalItens(){
            return count($this->itens);
        }
        
        function getItem($index){
            if (isset($this->itens[$index]))
                return $this->itens[$index];
            else
                return new item();
        }        
    }
    
    class registro{
        private $itens;
        private $class;
        private $style;
        private $acesso;
        
        function getClass(){
            return $this->class;
        }
        function getStyle(){
            return $this->style;
        }
        function getAcesso(){
            return $this->acesso;
        }
        
        function __construct($array = null) {
            $this->itens = array();
            
            if ($array != null)
                if (is_array($array)){
                    if (isset($array["class"]))
                        $this->class = $array["class"];
                    if (isset($array["style"]))
                        $this->style = $array["style"];
                    if (isset($array["acesso"]))
                        $this->acesso = $array["acesso"];
                }
        }

        function __destruct() {
            unset($this->itens);
        }
        
        function addItem($texto, $array = null){
            if (is_a($texto, "item")){
                array_push($this->itens, $texto);
            }else{
                $style   = null;
                $onclick = null;
                $link    = null;
                $classe  = null;
                $id      = null;
                $colspan = null;

                if ($array != null){
                    if (is_array($array)){
                        if (isset($array["style"]))
                            $style = $array["style"];

                        if (isset($array["onclick"]))
                            $onclick = $array["onclick"];

                        if (isset($array["link"]))
                            $link = $array["link"];

                        if (isset($array["class"]))
                            $classe= $array["class"];

                        if (isset($array["id"]))
                            $id = $array["id"];

                        if (isset($array["colspan"]))
                            $colspan= $array["colspan"];
                    }else
                        $texto = "Não é um array";
                }

                array_push($this->itens, new item($texto, $style, $onclick, $link, $classe, $id, $colspan));
            }
        }
        
        function getTotalItens(){
            return count($this->itens);
        }
        
        function getItem($index){
            if (isset($this->itens[$index]))
                return $this->itens[$index];
            else
                return new item();
        }
    }
    
    class item{
        private $texto, $style, $onclick, $link, $classe, $id, $colspan;
        
        function __construct($texto = null, $style = null, $onclick = null, $link = null, $classe = null, $id = null, $colspan = null){
            $this->setTexto($texto);
            $this->setStyle($style);
            $this->setOnClick($onclick);
            $this->setLink($link);
            $this->setClasse($classe);
            $this->setId($id);
            $this->setColspan($colspan);
        }
        
        function setTexto($v)  { $this->texto   = $v; }
        function setStyle($v)  { $this->style   = $v; }
        function setOnClick($v){ $this->onclick = $v; }
        function setLink($v)   { $this->link    = $v; }
        function setClasse($v) { $this->classe  = $v; }
        function setId($v)     { $this->id      = $v; }
        function setColspan($v){ $this->colspan = $v; }
        
        function getTexto()  { return $this->texto;   }
        function getStyle()  { return $this->style;   }
        function getOnClick(){ return $this->onclick; }
        function getLink()   { return $this->link;    }
        function getClasse() { return $this->classe;  }
        function getId()     { return $this->id;      }
        function getColspan(){ return $this->colspan; }
    }
    
    class tabela{
        private $cabecalho, $registro;
        private $style, $id, $classe; 
        
        function __construct($array = null) {
            if ($array != null){
                if (is_array($array)){
                    
                    if (isset($array["style"]))
                        $this->setStyle($array["style"]);
                    
                    if (isset($array["id"]))
                        $this->setId($array["id"]);
                    
                    if (isset($array["class"]))
                        $this->setClasse($array["class"]);
                }
            }
            
            $this->cabecalho = array();
            $this->registro  = array();
        }
        
        function __destruct() {
            unset($this->cabecalho);
            unset($this->registro);
        }
        
        function setStyle($v)  { $this->style   = $v; }
        function setClasse($v) { $this->classe  = $v; }
        function setId($v)     { $this->id      = $v; }
        
        function getStyle()  { return $this->style;   }
        function getClasse() { return $this->classe;  }
        function getId()     { return $this->id;      }        
        
        function addCabecalho($cabecalho){
            array_push($this->cabecalho, $cabecalho);
        }
        
        function addRegistro($registro){
            array_push($this->registro, $registro);
        }
        
        function getTotalCabecalho(){
            return count($this->cabecalho);
        }
        
        function registro(){
            return $this->registro;
        }

        function getTotalRegistro(){
            return count($this->registro);
        }        
        
        function gerar(){
            $t = "<table";
            if ($this->getClasse() != null)
                $t .= " class=\"".$this->getClasse()."\"";
            if ($this->getId() != null)
                $t .= " id=\"".$this->getId()."\"";
            if ($this->getStyle() != null)
                $t .= " style=\"".$this->getStyle()."\"";
            $t .= ">";
            
            if (count($this->cabecalho) > 0){
                $t .= "<thead>";
                for ($i = 0; $i < $this->getTotalCabecalho(); $i++){
                    $t .= "<tr";
                    if ($this->cabecalho[$i]->getClass() != "")
                        $t .= " class=\"{$this->cabecalho[$i]->getClass()}\"";
                    if ($this->cabecalho[$i]->getStyle() != "")
                        $t .= " style=\"{$this->cabecalho[$i]->getStyle()}\"";
                    $t .= ">";
                    
                    for ($j = 0; $j < $this->cabecalho[$i]->getTotalItens(); $j++){
                        $t .= "<th";
                        
                        if ($this->cabecalho[$i]->getItem($j)->getStyle() != null)
                            $t .= " style=\"{$this->cabecalho[$i]->getItem($j)->getStyle()}\"";
                        
                        if ($this->cabecalho[$i]->getItem($j)->getClasse() != null)
                            $t .= " class=\"{$this->cabecalho[$i]->getItem($j)->getClasse()}\"";
                        
                        if ($this->cabecalho[$i]->getItem($j)->getOnClick() != null)
                            $t .= " onclick=\"{$this->cabecalho[$i]->getItem($j)->getOnClick()}\"";
                        
                        if ($this->cabecalho[$i]->getItem($j)->getColspan() != null)
                            $t .= " colspan=\"{$this->cabecalho[$i]->getItem($j)->getColspan()}\"";
                            
                        $t .= ">";
                        
                        if ($this->cabecalho[$i]->getItem($j)->getLink() != null)
                            $t .= "<a href=\"{$this->cabecalho[$i]->getItem($j)->getLink()}\">";
                        
                        $t.= $this->cabecalho[$i]->getItem($j)->getTexto();
                        
                        if ($this->cabecalho[$i]->getItem($j)->getLink() != null)
                            $t .= "</a>";
                        
                        $t .= "</th>";
                    }
                    
                    $t .= "</tr>";
                    $t .= "</thead>";
                }
            }
            
            if (count($this->registro) > 0){
                $t .= "<tbody>";
                for ($i = 0; $i < $this->getTotalRegistro(); $i++){                    
                    $t .= "<tr";
                    if ($this->registro[$i]->getClass() != "")
                        $t .= " class=\"{$this->registro[$i]->getClass()}\"";
                    if ($this->registro[$i]->getStyle() != "")
                        $t .= " style=\"{$this->registro[$i]->getStyle()}\"";
                    if ($this->registro[$i]->getAcesso() != "")
                        $t .= " acesso=\"{$this->registro[$i]->getAcesso()}\"";
                    $t .= ">";
                    
                    for ($j = 0; $j < $this->registro[$i]->getTotalItens(); $j++){
                        $t .= "<td";
                        
                        if ($this->registro[$i]->getItem($j)->getStyle() != null)
                            $t .= " style=\"".$this->registro[$i]->getItem($j)->getStyle()."\"";
                        
                        if ($this->registro[$i]->getItem($j)->getClasse() != null)
                            $t .= " class=\"".$this->registro[$i]->getItem($j)->getClasse()."\"";
                        
                        if ($this->registro[$i]->getItem($j)->getOnClick() != null)
                            $t .= " onclick=\"".$this->registro[$i]->getItem($j)->getOnClick()."\""; 
                        
                        if ($this->registro[$i]->getItem($j)->getColspan() != null)
                            $t .= " colspan=\"".$this->registro[$i]->getItem($j)->getColspan()."\"";                        
                        
                        $t .= ">";
                        
                        if ($this->registro[$i]->getItem($j)->getLink() != null)
                            $t .= "<a href=\"".$this->registro[$i]->getItem($j)->getLink()."\">";
                        
                        $t.= $this->registro[$i]->getItem($j)->getTexto();
                        
                        if ($this->registro[$i]->getItem($j)->getLink() != null)
                            $t .= "</a>";
                        
                        $t .= "</td>";
                    }
                    
                    $t .= "</tr>";
                }
                $t .= "</tbody>";
            }
            
            $t .= "</table>";
            
            return $t;
        }
    }

?>