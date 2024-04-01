<?php

namespace App\Http\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\User;

class XlsxImports implements ToModel{
    public function model(array $row){
        return new User([
            'bairro' => $row[0],
            'cep' => $row[1],
            'cidade' => $row[2],
            'cnpj' => $row[3],
            'contato' => $row[4],
            'cpf' => $row[5],
            'codigopostal' => $row[6],
            'celular' => $row[7],
            'complemento' => $row[8],
            'datanascimento' => $row[9],
            'email' => $row[10],
            'endereco' => $row[11],
            'estadocivil' => $row[12],
            'estado' => $row[13],
            'genero' => $row[14],
            'identidade' => $row[15],
            'nomedamae' => $row[16],
            'nome' => $row[17],
            'observacao' => $row[18],
            'pais' => $row[19],
            'sexo' => $row[20],
            'telefone' => $row[21],
            'tituloeleitor' => $row[22],
        ]);
    }
}
