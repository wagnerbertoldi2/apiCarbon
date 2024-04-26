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
use Maatwebsite\Excel\Concerns\FromArray;

class ImportController extends Controller{
    private $key;

    public function __construct(){
        $this->key = "mlNJbhVGcfXDzsAP0O9I8U7Y6T5R4E3W2Q1";
    }

    public function importXlsx(Request $request){
        $headerPadrao= ["matricula", "logradouro", "numero", "complemento", "bairro", "cep", "tipo_unidade"];
        $dadosXlsx= $this->getXlsx($request, $headerPadrao);

        if(isset($dadosXlsx['nerror'])){
            return response()->json(['error' => 'Arquivo inválido'], 401);
        }

        $fileName = $request->file->getClientOriginalName();

        $importId= DB::table("importe")->insertGetId(["filename" => $fileName]);
        $db= $this->organizaXlsx($dadosXlsx, $importId);

        $i=0;
        $j=0;
        foreach($db as $linha){
            $insMun= DB::table('importe_dados')->where('matricula', $linha['matricula'])->first();

            if($insMun == null && $linha['matricula'] != null){
                if($idItem= DB::table("importe_dados")->insertGetId($linha)){
                    $link= $this->geraLink($linha, $importId, $idItem);
                    DB::table("importe_dados")->where("id", $idItem)->update(["link" => $link]);
                    $i++;
                } else {
                    $j++;
                }
            }
        }

        DB::table("importe")->where("id", $importId)->update(["total" => count($db), "success" => $i, "error" => $j]);

        return response()->json(["success" => $i, "error" => $j], 201);
    }

    private function organizaXlsx($dadosXlsx, $importId){
        $db= [];
        $i=0;
        foreach ($dadosXlsx as $key => $dado) {
            $db[$i]["importeId"]= $importId;
            $db[$i]["matricula"]= $dado["matricula"];
            $db[$i]["logradouro"]= $dado["logradouro"];
            $db[$i]["numero"]= $dado["numero"];
            $db[$i]["complemento"]= $dado["complemento"];
            $db[$i]["bairro"]= $dado["bairro"];
            $db[$i]["cep"]= $dado["cep"];
            $db[$i]["tipo_unidade"]= $dado["tipo_unidade"];
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
    private function getXlsx($request, $headerPadrao){
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

            return response()->json($header, 200);

            foreach ($headerPadrao as $key => $value) {
                if(!in_array($value, $header)){
                    return ['error' => 'Arquivo inválido', 'nerror'=> 1];
                }
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

    public function listaImportes(Request $request){
        $importes= DB::table("importe")->get();

        if($importes){
            return response()->json($importes, 200);
        } else {
            return response()->json(['error' => 'Nenhum importe encontrado'], 404);
        }
    }

    public function listDadosImportados(Request $request){
        $dados= "SELECT * FROM importe_dados WHERE importeId=$request->id";
        $dados= DB::select($dados);

        if($dados){
            return response()->json($dados, 200);
        } else {
            return response()->json(['error' => 'Nenhum dado encontrado'], 404);
        }
    }

    public function getDadosLinhaImporte(Request $request){
        $dados= DB::table("importe_dados")->where("id", $request->id)->first();
        return response()->json($dados, 201);
    }

    public function exportXlsx($id, $token){
        try {
            $user = JWTAuth::setToken($token)->authenticate();
            if ($user == false) {
                return response()->json(['error' => 'Token inválido'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        $dados= DB::table("importe_dados")->where("importeId", $id)->get();
        $dados= json_decode(json_encode($dados), true);

        $header= ["matricula", "logradouro", "numero", "complemento", "bairro", "cep", "tipo_unidade", "link"];
        $dadosXlsx= [];
        $dadosXlsx[]= $header;

        foreach ($dados as $key => $dado) {
            $linha= [];
            foreach ($header as $key => $value) {
                $linha[]= $dado[$value];
            }
            $dadosXlsx[]= $linha;
        }

        return Excel::download(new class($dadosXlsx) implements FromArray {
            private $dados;

            public function __construct($dados) {
                $this->dados = $dados;
            }

            public function array(): array {
                return $this->dados;
            }
        }, 'Importe.xlsx');
    }
}
