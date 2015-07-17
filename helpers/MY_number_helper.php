<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('convert_number')) {
    function convert_number($number)
    {

        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
        $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");
        $res = "";
        $cr = '';
        $lakh = '';
        $ths = '';
        $Dn = '';
        $n = '';
        $kn = '';
        $Hn = '';
        if (($number < 0) || ($number > 999999999)) {
            throw new Exception("Number is out of range");
        }
        if ($number < 999999999 && $number > 9999999) {
            $cr = ($number / 10000000);;
        }
        if ($number <= 9999999 && $number > 99999) {
            $lakh = $number / 100000;
        }
        if ($number <= 99999 && $number > 0) {
            $Gn = floor($number / 1000000);
            /* Millions (giga) */
            $number -= $Gn * 1000000;
            $kn = floor($number / 1000);
            $number -= $kn * 1000;
            $Hn = floor($number / 100);
            /* Hundreds (hecto) */
            $number -= $Hn * 100;

            $Dn = floor($number / 10);
            /* Tens (deca) */
            $n = $number % 10;
            /* Ones */
        }


        if ($cr) {
            $res = number_format($cr, 2) . " Cr.";
        }
        if ($lakh) {
            $res = number_format($lakh, 2) . " Lacs";
        }
        if ($kn) {
            $res .= (empty($res) ? "" : " ") . convert_number($kn) . " Thous";
        }
        if ($Hn) {
            $res .= (empty($res) ? "" : " ") . convert_number($Hn) . " Hundred";
        }
        if ($Dn || $n) {
            if (!empty($res)) {
                $res .= " and ";
            }
            if ($Dn < 2) {
                $res .= $ones[$Dn * 10 + $n];
            } else {
                $res .= $tens[$Dn];

                if ($n) {
                    $res .= "-" . $ones[$n];
                }
            }
        }
        if (empty($res)) {
            $res = "Zero";
        }
        return $res;
    }

}
