<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CoordenadasController extends Controller{

    private $logradouro;
    private $bairro;
    private $localidade;
    private $uf;
    private $cep;
    private $num;
    private $latitude;
    private $longitude;

    public function setCep($cep){
        $this->cep= preg_replace('/[^0-9]/', '', $cep);
    }

    public function setNum($num){
        $this->num= $num;
    }
    private function setEndereco(){
        $cep= $this->cep;
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $resposta = file_get_contents($url);
        $dados = json_decode($resposta, true);

        $this->logradouro= $dados['logradouro'];
        $this->bairro= $dados['bairro'];
        $this->localidade= $dados['localidade'];
        $this->uf= $dados['uf'];
    }

    public function obterCoordenadasNominatim() {
        $this->setEndereco();
        $endereco = $this->logradouro.", ".$this->num.", ".$this->localidade.", ".$this->uf;

        $enderecoFormatado = htmlspecialchars_decode("https://nominatim.openstreetmap.org/search?q=".urlencode("$endereco").'&format=json');

        $client = new Client();
        $response = $client->get($enderecoFormatado);
        $dados = json_decode($response->getBody(), true);

        if ($dados && !empty($dados)) {
            $resultado = $dados[0];
            $latitude = $resultado['lat'];
            $longitude = $resultado['lon'];

            $this->latitude= $latitude;
            $this->longitude= $longitude;

            $resp= [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];

            $status= 201;
        } else {
            $resp= null;
            $status= 401;
        }

        return $resp;
    }
}
