<?php

namespace App\Http\Controllers;

use App\Models\PropertyModel;
use http\Env\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ImportController extends Controller{
    private $key;

    public function __construct(){
        $this->key = "mlNJbhVGcfXDzsAP0O9I8U7Y6T5R4E3W2Q1";
    }

    public function importXlsx(Request $request){
        $dadosXlsx= $this->getXlsx($request);
        $fileName = $request->file->getClientOriginalName();

        $importId= DB::table("importe")->insertGetId(["filename" => $fileName]);
        $db= $this->organizaXlsx($dadosXlsx, $importId);

        $i=0;
        $j=0;
        foreach($db as $linha){
            if($idItem= DB::table("importe_dados")->insertGetId($linha)){
                $link= $this->geraLink($linha, $importId, $idItem);
                DB::table("importe_dados")->where("id", $idItem)->update(["link" => $link]);
                $i++;
            } else {
                $j++;
            }
        }

        DB::table("importe")->where("id", $importId)->update(["total" => count($db), "success" => $i, "error" => $j]);

        return response()->json(["success" => $i, "error" => $j]);
    }

    private function organizaXlsx($dadosXlsx, $importId){
        $db= [];
        $i=0;
        foreach ($dadosXlsx as $key => $dado) {
            $db[$i]["importeId"]= $importId;
            $db[$i]["bairro"]= $dado["bairro"];
            $db[$i]["cep"]= $dado["cep"];
            $db[$i]["cidade"]= $dado["cidade"];
            $db[$i]["cnpj"]= $dado["cnpj"];
            $db[$i]["contato"]= $dado["contato"];
            $db[$i]["cpf"]= $dado["cpf"];
            $db[$i]["codigopostal"]= $dado["códigopostal"];
            $db[$i]["celular"]= $dado["celular"];
            $db[$i]["complemento"]= $dado["complemento"];

            if(!empty($dataNascimento)) {
                $dataNascimento = explode("/", $dado["datanascimento"]);
                $db[$i]["datanascimento"] = $dataNascimento[2] . "-" . $dataNascimento[1] . "-" . $dataNascimento[0];
            } else {
                $db[$i]["datanascimento"] = null;
            }

            $db[$i]["email"]= $dado["e-mail"];
            $db[$i]["endereco"]= $dado["endereço"];
            $db[$i]["estadocivil"]= $dado["estadocivil"];
            $db[$i]["estado"]= $dado["estado"];
            $db[$i]["escolaridade"]= $dado["escolaridade"];
            $db[$i]["genero"]= $dado["gênero"];
            $db[$i]["identidade"]= $dado["identidade"];
            $db[$i]["nomedamae"]= $dado["nomedamãe"];
            $db[$i]["nome"]= $dado["nome"];
            $db[$i]["observacao"]= $dado["observação"];
            $db[$i]["pais"]= $dado["país"];
            $db[$i]["sexo"]= $dado["sexo"];
            $db[$i]["telefone"]= $dado["telefone"];
            $db[$i]["tituloeleitor"]= $dado["títulodeeleitor"];
            $i++;
        }

        return $db;
    }

    public function dadosUnidade(Request $request){
        $LinkCriptografado = $request->input("key");
        $LinkCriptografado = base64_decode($LinkCriptografado);
        $decryptedData = openssl_decrypt(base64_decode(explode("^", $LinkCriptografado)[1]), 'AES-128-ECB', $this->key);
        $ImportId = explode("^id=", $decryptedData)[1];

        $dados= DB::table("importe_dados")->where("id", $ImportId)->first();
        return $dados;
    }

    public function openLink(Request $request){
        $token = $request->input('token');

        try {
            $user= JWTAuth::setToken($token)->authenticate();

            if (!$user && !empty($user->id)) {
                return response()->json(['error' => 'Token inválido'], 401);
            } else {
                $LinkCriptografado = $request->input("key");
                $LinkCriptografado = base64_decode($LinkCriptografado);
                $decryptedData = openssl_decrypt(base64_decode(explode("^", $LinkCriptografado)[1]), 'AES-128-ECB', $this->key);
                $ImportId = explode("^id=", $decryptedData)[1];
                $dado= DB::table("importe_dados")->where("id", $ImportId)->first();

                if($dado->status == 0) {
                    if (!empty($dado->cep)) {
                        $obj = new CoordenadasController();
                        $obj->setCep($dado->cep);
                        $obj->setNum(1);
                        $res = $obj->obterCoordenadasNominatim();
                    } else {
                        $res = ["latitude" => null, "longitude" => null, "request" => null, "response" => null];
                    }

                    $property = new PropertyModel();
                    $property->Name = $dado->endereco;
                    $property->Registration = "";
                    $property->CEP = $dado->cep;
                    $property->City = $dado->cidade;
                    $property->Number = 0;
                    $property->Complement = $dado->complemento;
                    $property->NumberOfPeoples = 0;
                    $property->Address = $dado->endereco;
                    $property->UF = $dado->estado;
                    $property->UserId = $user->id;
                    $property->CategoryId = empty($dado->cnpj) ? 1 : 2;
                    $property->latitude = $res['latitude'];
                    $property->longitude = $res['longitude'];
                    $property->regionId = $this->pesquisaRegion($dado->bairro);
                    $property->requestCoordinates = $res['request'];
                    $property->responseCoordinates = $res['response'];
                    $property->save();

                    DB::table("importe_dados")->where("id", $ImportId)->update(["status" => 1, "propertyId" => $property->id]);

                    return response()->json(['msg' => 'Propriedade vinculada com sucesso!'], 201);
                } else {
                    return response()->json(['error' => 'Acesso Negado'], 401);
                }
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível autenticar com o token'], 500);
        }
    }

    private function pesquisaRegion($bairro){
        $res= DB::table("region")->where("name", "like", "%".$bairro."%")->first();
        return $res != null ? $res->id : 5;
    }
    private function geraLink($dados,$importId,$idItem){
        $tudo= "";
        foreach ($dados as $key => $dado) {
            $tudo.= $dado;
        }
        $tudo.= date("d-m-Y H:i:s");
        $tudo.= $importId;
        $tudo.= "^id=".$idItem;

        $LinkCriptografado= base64_encode(md5($tudo)."^".base64_encode(openssl_encrypt("^id=".$idItem, 'AES-128-ECB',$this->key)));
        return $LinkCriptografado;
    }
    private function getXlsx($request){
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $data = Excel::toArray(new class implements ToCollection {
                public function collection($collection) {
                    return $collection;
                }
            }, $file);

            $header= $data[0][0];

            foreach ($header as $key => $value) {
                $header[$key]= str_replace(" ","",strtolower(str_replace(")","", explode("(", $value)[0])));
            }

            unset($data[0][0]);
            $dados= $data[0];

            foreach ($dados as $key => $value) {
                $dados[$key]= array_combine($header, $value);
            }

            return $dados;
        } else {
            return false;
        }
    }
}
