<?php

namespace App\Helpers;

class NumeroALetras
{
    private static $unidades = ['', 'UN ', 'DOS ', 'TRES ', 'CUATRO ', 'CINCO ', 'SEIS ', 'SIETE ', 'OCHO ', 'NUEVE '];
    private static $decenas = ['DIEZ ', 'ONCE ', 'DOCE ', 'TRECE ', 'CATORCE ', 'QUINCE ', 'DIECISEIS ', 'DIECISIETE ', 'DIECIOCHO ', 'DIECINUEVE '];
    private static $decenas_propias = ['', '', 'VEINTE ', 'TREINTA ', 'CUARENTA ', 'CINCUENTA ', 'SESENTA ', 'SETENTA ', 'OCHENTA ', 'NOVENTA '];
    private static $centenas = ['', 'CIENTO ', 'DOSCIENTOS ', 'TRESCIENTOS ', 'CUATROCIENTOS ', 'QUINIENTOS ', 'SEISCIENTOS ', 'SETECIENTOS ', 'OCHOCIENTOS ', 'NOVECIENTOS '];

    public static function convertir($number)
    {
        $number = number_format($number, 0, '.', '');
        if ($number == 0) return 'CERO';

        return self::convertirGrupo($number);
    }

    private static function convertirGrupo($n) {
        if ($n == '1000000') return 'UN MILLON ';
        if ($n > 1000000) {
            $millones = floor($n / 1000000);
            $resto = $n % 1000000;
            $txt = ($millones == 1) ? 'UN MILLON ' : self::convertirGrupo($millones) . 'MILLONES ';
            return $txt . self::convertirGrupo($resto);
        }
        if ($n >= 1000) {
            $miles = floor($n / 1000);
            $resto = $n % 1000;
            $txt = ($miles == 1) ? 'MIL ' : self::convertirGrupo($miles) . 'MIL ';
            return $txt . self::convertirGrupo($resto);
        }
        if ($n >= 100) {
            if ($n == 100) return 'CIEN ';
            $centenas = floor($n / 100);
            $resto = $n % 100;
            return self::$centenas[$centenas] . self::convertirGrupo($resto);
        }
        if ($n >= 20) {
            $decenas = floor($n / 10);
            $resto = $n % 10;
            if ($n == 20) return 'VEINTE ';
            if ($n > 20 && $n < 30) return 'VEINTI' . self::$unidades[$resto];
            return self::$decenas_propias[$decenas] . ($resto > 0 ? 'Y ' . self::$unidades[$resto] : '');
        }
        if ($n >= 10) return self::$decenas[$n - 10];
        return self::$unidades[$n];
    }
}
