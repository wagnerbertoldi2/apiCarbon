<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class cpfController extends Controller{
    function validarCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $digito1 = ($soma % 11 < 2) ? 0 : 11 - ($soma % 11);

        // Calcula o segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $digito2 = ($soma % 11 < 2) ? 0 : 11 - ($soma % 11);

        // Verifica se os dígitos verificadores calculados são iguais aos fornecidos
        if ($digito1 == intval($cpf[9]) && $digito2 == intval($cpf[10])) {
            return true;
        } else {
            return false;
        }
    }
}
