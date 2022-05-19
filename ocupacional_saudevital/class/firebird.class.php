<?php

class FB{
    protected
        $conexao, $tabela, $erro = 0;
    
    function __construct($tabela, $conexao = null){
        if ($conexao == null){
            try{
                $this->conexao = ibase_connect(config::getServidor().":".config::getBanco(), config::getUsuario(), config::getSenha(), config::getCharset());
                if (ibase_errcode() != 0)
                    throw new Exception(ibase_errcode());
            } catch (Exception $erro) {
                //$this->erro = $erro->getMessage();
               echo "pau";
                die;
            }
        }else
            $this->conexao = $conexao;
        $this->tabela = $tabela;
    }

    function __destruct(){
        if ($this->erro == 0)
            ibase_close($this->conexao);
        else
            echo "Ocorreu algum erro durante a conexão com o banco de dados! <br> Informe o código \"" . $this->erro . "\" ao administrador!";
    }

    function getConexao(){
        return $this->conexao;
    }
	
    function setTabela($tabela){
        $this->tabela = $tabela;
    }

    function verifica($value){
        $pesquisa = array("drop", "truncate", "delete", "update", "insert", "DROP", "TRUNCATE", "DELETE", "UPDATE", "INSERT");
        return str_replace($pesquisa, "", $value);
    }

    function select($where = null, $limit = null, $order = null, $join = null, $select = null, $group = null, $echo = false){
        if ($limit <> null)
            if (is_array($limit))
                $limit = "FIRST {$limit[0]} SKIP {$limit[1]}";
            else
                $limit = "FIRST {$limit}";
        
        $query = "SELECT ".addslashes($limit);
  
        if (is_array($select)){
            $temp = array();
            
            for ($i = 0; $i < count($select); $i++)
                array_push($temp, addslashes($select[$i]));
            
            $select = implode(", ", $temp);
        }else
            $select = "*";
           
        $query .= " {$select} FROM {$this->tabela} {$join} ";
        
        if (is_array($where)){
            $keys = array_keys($where);
            $temp = array();
            for ($i = 0; $i < count($keys); $i++){
                if (is_array($where[$keys[$i]])){
                    $op = $where[$keys[$i]][1];
                    
                    //$tp = addslashes($keys[$i]);
                    $tp = $keys[$i];
                    if ($op == "IN"){
                        $tp .= " ".addslashes($where[$keys[$i]][1])." ('";
                        $in = $where[$keys[$i]][0];
                        for ($j = 0; $j < count($in); $j++)
                            $in[$j] = addslashes($in[$j]);
                        
                        $tp .= implode("', '", $in)."')";
                    }else
                        if ($op == "IS")
                            $tp .= " ".addslashes($where[$keys[$i]][1])." ".addslashes($where[$keys[$i]][0]);
                        else
                            $tp .= " ".addslashes($where[$keys[$i]][1])." '".addslashes($where[$keys[$i]][0])."'";
                    
                    if (count($where[$keys[$i]]) == 3) {
                        $tp .= $where[$keys[$i]][2];
                    }
                        
                    array_push($temp, $tp);
                }else {
                    array_push($temp, $keys[$i]." = '".addslashes($where[$keys[$i]])."'");
                }
            }
            /*
            for ($i = 0; $i < count($keys); $i++){
                if (is_array($where[$keys[$i]])){
                    $op = $where[$keys[$i]][1];
                    
                    $tp = $keys[$i];
                    if ($op == "IN"){
                        $tp .= " IN ('";
                        $in = $where[$keys[$i]][0];
                        for ($j = 0; $j < count($in); $j++)
                            $in[$j] = addslashes($in[$j]);
                        
                        $tp .= implode("', '", $in)."')";
                    }else
                        if ($op == "IS")
                            $tp .= " {$where[$keys[$i]][1]} ".addslashes($where[$keys[$i]][0]);
                        else
                            $tp .= " {$where[$keys[$i]][1]} '".addslashes($where[$keys[$i]][0])."'";
                    
                    array_push($temp, $tp);
                }else
                    array_push($temp, "{$keys[$i]} = '".addslashes($where[$keys[$i]])."'");
            }
             */
            
            $query .= " WHERE ".implode(" AND ", $temp);
        }
        
        if (is_array($group)){
            $temp = array();
            
            for ($i = 0; $i < count($group); $i++)
                array_push($temp, $group[$i]);

            $query .= " GROUP BY ".implode(", ", $temp);
        }        
        
        if (is_array($order)){
            $temp = array();
            $keys = array_keys($order);
            
            for ($i = 0; $i < count($keys); $i++)
                array_push($temp, addslashes($keys[$i]." ".$order[$keys[$i]]));

            $query .= " ORDER BY ".implode(", ", $temp);
        }
            
        if ($echo)
            echo $query;
        
        $query = ibase_query($this->conexao, $query);

        $i = 0;
        $return = null;
        if($query){
            while($array = ibase_fetch_assoc($query)){
                $return[$i] = $array;
                $i++;
            }
        }
        ibase_free_result($query);
        return $return;
    }

    function update($values, $where, $echo = false){
        $query = "UPDATE {$this->tabela} SET ";
        if (is_array($values)){
            $keys = array_keys($values);
            $temp = array();
            for ($i = 0; $i < count($keys); $i++){
                if (strtoupper($values[$keys[$i]]) == "NULL")
                    array_push($temp, addslashes($keys[$i])." = NULL");
                else
                    array_push($temp, addslashes($keys[$i])." = '".addslashes($values[$keys[$i]])."'");
            }
         
            $query .= implode(", ", $temp);
        }else{
            return false;
            die;
        }
        
        if (is_array($where)){
            $keys = array_keys($where);
            $temp = array();
            for ($i = 0; $i < count($keys); $i++){
                if (is_array($where[$keys[$i]])){
                    $op = $where[$keys[$i]][1];
                    
                    $tp = addslashes($keys[$i]);
                    if ($op == "IN"){
                        $tp .= " ".addslashes($where[$keys[$i]][1])." ('";
                        $in = $where[$keys[$i]][0];
                        for ($j = 0; $j < count($in); $j++)
                            $in[$j] = addslashes($in[$j]);
                        
                        $tp .= implode("', '", $in)."')";
                    }else
                        if ($op == "IS")
                            $tp .= " ".addslashes($where[$keys[$i]][1])." ".addslashes($where[$keys[$i]][0]);
                        else
                            $tp .= " ".addslashes($where[$keys[$i]][1])." '".addslashes($where[$keys[$i]][0])."'";
                    
                    array_push($temp, $tp);
                }else
                    array_push($temp, addslashes($keys[$i])." = '".addslashes($where[$keys[$i]])."'");
            }
            
            $query .= " WHERE ".implode(" AND ", $temp);
        }else{
            return false;
            die;
        }     
        
        if ($echo)
            echo $query;
        return ibase_query($this->conexao, $query);
    }    
    
    function insert($values, $echo = false){
        $keys = array_keys($values);
        
        $temp = array();
        for ($i = 0; $i < count($keys); $i++)
            array_push($temp, addslashes($keys[$i]));
        
        $temp2 = array();
        for ($i = 0; $i < count($keys); $i++){
            if (strtoupper($values[$keys[$i]]) == "NULL")
                array_push($temp2, "NULL");
            else
                array_push($temp2, "'".addslashes($values[$keys[$i]])."'");
        }
        
        $query = "INSERT INTO {$this->tabela} (".implode(", ", $temp).") VALUES (".implode(", ", $temp2).");";

        if ($echo)
            echo $query;
        
        return ibase_query($query);
    }
    
    function executeProcedure($procedure, $parametros){
        $query = "EXECUTE PROCEDURE {$procedure}";
        
        if (is_array($parametros)){
            for ($i = 0; $i < count($parametros); $i++)
                $parametros[$i] = addslashes($parametros[$i]);
            
            $query .= "('".implode("', '", $parametros)."')";
        }else{
            return false;
            die;
        }
        
        return ibase_query($this->conexao, $query);
    }

    function executeQuery($query, $echo = false){
        if ($echo)
            echo $query;
        
        $query = ibase_query($this->conexao, $query);

        $i = 0;
        $return = null;
        if($query){
            while($array = ibase_fetch_assoc($query)){
                $return[$i] = $array;
                $i++;
            }
        }
        ibase_free_result($query);
        return $return;
    }
    
    function executeQueryBlob($query, $blob, $echo = false){
        if ($echo)
            echo $query;

        return ibase_query($query, $blob);
        //$preparado = ibase_prepare($query);
        //return ibase_execute($preparado, $blob);
    }
    
    function proximoCodigo($unidade, $empresa, $codigo){
        $query = "UPDATE T_CTRLSEQUNIEMP                     "
                . "  SET ULTIMO       = ULTIMO + 1,          "
                . "      DATAULTMOVTO = CAST('NOW' AS DATE)  "
                . "WHERE CODIGO  = '".addslashes($codigo)."' "
                . "  AND EMPRESA = '".addslashes($empresa)."'"
                . "  AND UNIDADE = '".addslashes($unidade)."'";
        
        if (ibase_query($this->conexao, $query)){
            $query = "SELECT ULTIMO                              "
                    . " FROM T_CTRLSEQUNIEMP                     "
                    . "WHERE CODIGO  = '".addslashes($codigo)."' "
                    . "  AND EMPRESA = '".addslashes($empresa)."'"
                    . "  AND UNIDADE = '".addslashes($unidade)."'";
        
            $query = ibase_query($this->conexao, $query);
   
            if ($query){
                
                $array = ibase_fetch_assoc($query);
                return $array["ULTIMO"];
                
            }else
                return "";
        }else
            return "";
        
    }
    
    function proximoCodigoGeral($unidade, $codigo){
        $query = "UPDATE T_CTRLSEQEMP                     "
                . "  SET ULTIMO       = ULTIMO + 1,          "
                . "      DATAULTMOVTO = CAST('NOW' AS DATE)  "
                . "WHERE CODIGO  = '".addslashes($codigo)."' "
                . "  AND UNIDADE = '".addslashes($unidade)."'";
        
        if (ibase_query($this->conexao, $query)){
            $query = "SELECT ULTIMO                              "
                    . " FROM T_CTRLSEQEMP                     "
                    . "WHERE CODIGO  = '".addslashes($codigo)."' "
                    . "  AND UNIDADE = '".addslashes($unidade)."'";
            $query = ibase_query($this->conexao, $query);
            if ($query){
                $array = ibase_fetch_assoc($query);
                return $array["ULTIMO"];
                
            }else
                return "";
        }else
            return "";
        
    }
    function delete($where, $echo = false){
        $query = "DELETE FROM {$this->tabela}";
        
        if (is_array($where)){
            $keys = array_keys($where);
            $temp = array();
            for ($i = 0; $i < count($keys); $i++){
                if (is_array($where[$keys[$i]])){
                    $op = $where[$keys[$i]][1];
                    
                    $tp = addslashes($keys[$i]);
                    if ($op == "IN"){
                        $tp .= " ".addslashes($where[$keys[$i]][1])." ('";
                        $in = $where[$keys[$i]][0];
                        for ($j = 0; $j < count($in); $j++)
                            $in[$j] = addslashes($in[$j]);
                        
                        $tp .= implode("', '", $in)."')";
                    }else
                        if ($op == "IS")
                            $tp .= " ".addslashes($where[$keys[$i]][1])." ".addslashes($where[$keys[$i]][0]);
                        else
                            $tp .= " ".addslashes($where[$keys[$i]][1])." '".addslashes($where[$keys[$i]][0])."'";
                    
                    array_push($temp, $tp);
                }else
                    array_push($temp, addslashes($keys[$i])." = '".addslashes($where[$keys[$i]])."'");
            }
            
            $query .= " WHERE ".implode(" AND ", $temp);
        }else{
            return false;
            die;
        }    
        
        if ($echo)
            echo $query;
        
        return ibase_query($this->conexao, $query);
    }
    
}

?>
