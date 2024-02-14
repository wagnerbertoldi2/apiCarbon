<?php

namespace App\Mail;

use App\Http\Controllers\RandomNumberController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Tymon\JWTAuth\Facades\JWTAuth;

class CodigoNumericoEmail extends Mailable{
    use Queueable, SerializesModels;
    private $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user){
        $this->user= $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        if(!empty($this->user)) {
            $obj = new RandomNumberController();
            $code = $obj->generateRandomNumber($this->user);
        } else {
            $code= "";
        }

        $titulo= !empty($this->user) ? "Alterar Senha" : "Faça seu cadastro!";
        $conteudo= !empty($this->user) ? "Código de acesso: $code" : "Você solicitou alterar sua senha, porém, você não tem cadastro em nosso sistema.";

        return $this->view('mails.reset',['dadosEmail' => ["titulo"=>$titulo,"conteudo"=>$conteudo]])->subject("Solicitação de reset de senha");
    }
}
