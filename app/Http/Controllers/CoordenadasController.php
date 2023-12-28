<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    private function getEndereco(){
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
        $endereco = $this->logradouro.", ".$this->num.", ".$this->localidade.", ".$this->uf;
        dd($endereco);
        $enderecoFormatado = urlencode($endereco);
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$enderecoFormatado}";

        $resposta = file_get_contents($url);
        $dados = json_decode($resposta, true);

        if ($dados && !empty($dados)) {
            $resultado = $dados[0];
            $latitude = $resultado['lat'];
            $longitude = $resultado['lon'];

            $this->latitude= $latitude;
            $this->longitude= $longitude;

            return [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        } else {
            return null;
        }
    }
}
