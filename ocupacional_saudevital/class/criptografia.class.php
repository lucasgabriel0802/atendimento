<?php

class criptografia{
    static function deCriptografia($string){
        $iPosic = 3;
        $iCaract = 1;
        $sTemp = "";
        $sResult = "";
        while ($iPosic < strlen($string)) {
            try{
                $sTemp = substr($string, ($iPosic - 1), 3);
            }catch(exception $e){
                $sTemp = substr($string, ($iPosic - 1), (strlen($string) - $iPosic));
            }

            if (($iCaract % 2) == 0)
                $sResult .= chr($sTemp - 17);
            else
                $sResult .= chr($sTemp + 17);

            $iPosic += 5;
            $iCaract++;
        }

        return $sResult;
    }

    static function criptografar($string){
        $iPosic = 1;
        $sTemp = "";
        $sResult = "";
        $sParte = "";

        while (($iPosic - 1) < strlen($string)) {
            $rand1 = rand(00, 99);
            $rand2 = rand(00, 99);

            if ($rand1 > 60) {
                $rand1 = $rand1 + $rand2;
                if ($rand1 > 99)
                    $rand1 = 90;
            }else{
                $rand1 = $rand1 - $rand2;
                if ($rand1 < 0)
                    $rand1 = 3;
            }

            $sParte = substr($string, ($iPosic - 1), 1);

            if (($iPosic % 2) == 0) {
                $sTemp = ord($sParte);
                $somado = $sTemp + 17;
                $sTemp = str_pad($rand1, 2, "0", STR_PAD_LEFT).str_pad($somado, 3, "0", STR_PAD_LEFT);
            }else{
                $sTemp = ord($sParte);
                $somado = $sTemp - 17;
                $sTemp = str_pad($rand1, 2, "0", STR_PAD_LEFT).str_pad($somado, 3, "0", STR_PAD_LEFT);
            }
            $sResult .= $sTemp;
            $iPosic++;
        }
        return $sResult;
    }
}
?>