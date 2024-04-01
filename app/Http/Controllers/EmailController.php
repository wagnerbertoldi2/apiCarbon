<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExemploEmail;

class EmailController extends Controller
{
    public function enviarEmail()
    {
        // Dados para o e-mail
        $dadosEmail = [
            'titulo' => 'Recuperação de Senha',
            'conteudo' => 'Conteúdo do e-mail.',
        ];

        // Endereço de e-mail do destinatário
        $destinatario = 'wagner.bertoldi@gmail.com';

        // Envie o e-mail usando a classe Mail e a Mailable
        Mail::to($destinatario)->send(new ExemploEmail($dadosEmail));

        return "E-mail enviado com sucesso!";
    }
}
