<?php

namespace App\Http\Controllers;

use App\Models\EmissionFactorModel;
use Illuminate\Http\Request;
use App\Models\EmissionModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SimulationController;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class EmissionController extends Controller{
    public function set(Request $request){
        $file = $request->file('attachment');

        if (!empty($file)) {
            $originalExtension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $tempPath = $file->getRealPath();

            if (!file_exists($tempPath)) {
                return response()->json(["msg" => 'Arquivo não encontrado: ' . $tempPath], 401);
            }

            // Gera um nome único para o arquivo
            $nameFile = uniqid() . '_' . time() . '.' . $originalExtension;

            if ($originalExtension == 'pdf') {
                try {
                    // Salva PDF diretamente
                    $path = Storage::disk('local')->putFileAs(
                        'attachments',
                        $file,
                        $nameFile
                    );
                } catch (\Exception $e) {
                    return response()->json(["msg" => 'Erro ao salvar PDF: ' . $e->getMessage()], 500);
                }
            } else {
                try {
                    // Processa e salva imagem
                    $img = Image::make($tempPath)->resize(1000, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    // Salva a imagem processada
                    Storage::disk('local')->put(
                        'attachments/' . $nameFile,
                        $img->encode()
                    );
                } catch (\Exception $e) {
                    return response()->json(["msg" => 'Erro ao processar imagem: ' . $e->getMessage()], 500);
                }
            }
        }

        $emission = new EmissionModel();

        if(!empty($file)) {
            $emission->Attachment = $nameFile;
        }

        $dados= $this->getList2($request->idProperty, $request->EmissionSourceId, 'array');

        if(!empty($dados)) {
            if ($dados['periodo'] == 'semanal') {
                if (array_key_exists($request->year, $dados['anos']) === true) {
                    if (array_search($request->week, array_column($dados['anos'][$request->year], 'week')) == false) {
                        return response()->json(["msg" => "Esta semana e ano já estão registrados ou não tem permissão para registra-los."], 401);
                    }
                }
            } elseif ($dados['periodo'] == 'mensal') {
                if (array_key_exists($request->year, $dados['anos']) === true) {
                    if (array_search($request->month, array_column($dados['anos'][$request->year], 'value')) !== false) {
                        return response()->json(["msg" => "Este mês e ano já estão registrados ou não tem permissão para registra-los."], 401);
                    }
                }
            } elseif ($dados['periodo'] == 'anual') {
                if (array_search($request->year, $dados['anos']) === false) {
                    return response()->json(["msg" => "Este ano já esta registrado ou não tem permissão para registra-lo."], 401);
                }
            } elseif ($dados['periodo'] == 'semestral') {
                if (array_key_exists($request->year, $dados['anos']) === true) {
                    if (array_search($request->semester, array_column($dados['anos'][$request->year], 'value')) !== false) {
                        return response()->json(["msg" => "Este ano e semestre já estão registrados ou não tem permissão para registra-lo."], 401);
                    }
                }
            }
        }

        $semester = empty($request->semester) ? (($request->month * 1) <= 6 ? 1 : 2) : $request->semester;

        $emission->Amount = $request->amount;
        $emission->EmissionSourceId = $request->EmissionSourceId;
        $emission->Month = $request->month;
        $emission->Year = $request->year;
        $emission->week = $request->week;
        $emission->Semester = $semester;
        $emission->save();

        $EmissionId = $emission->id;

        $EmissionFactorId= DB::table("emissionsource")->select('EmissionFactorId')->where('id', $request->EmissionSourceId)->limit(1)->get();

        $obj = new SimulationController();
        $resp = $obj->setSimulation($request->idProperty, $request->EmissionSourceId, $EmissionFactorId[0]->EmissionFactorId, $request->amount, $request->year, $request->month, $semester, $EmissionId);

        return response()->json(true, 201);

    }

    public function get(Request $request){
        if($request->has('EmissionSourceId') && $request->has('propertyId')){
            $emission = DB::table('emission AS E')
                ->select('E.id', 'E.Amount', 'E.Attachment as file', DB::raw('concat("'.url('/').'/files/", E.Attachment) as urlDoComprovante'), 'P.Name as period', 'PP.Name as property', 'ES.Name as factor', DB::raw('if(P.`Name`="Anual",E.Year,if(P.`Name`="Mensal",concat(E.`Month`,"/",E.Year),concat(E.Semester,"/",E.Year))) as periodRef'), DB::raw('DATE_FORMAT(E.created_at, "%d/%m/%Y %H:%i:%s") as created_at'))
                ->leftJoin('emissionsource AS ES', 'ES.id', '=', 'E.EmissionSourceId')
                ->leftJoin('period AS P', 'P.id', '=', 'ES.PeriodId')
                ->leftJoin('property AS PP', 'PP.id', '=', 'ES.PropertyId')
                ->where('ES.PropertyId', '=', $request->propertyId)
                ->where('E.EmissionSourceId', '=', $request->EmissionSourceId)
                ->orderBy('E.created_at', 'desc')
                ->get();
            return response()->json($emission, 200);
        } elseif($request->has('EmissionSourceId')){
            $emission = DB::table('emission as E')
                ->select('E.id', 'E.Amount', 'E.Attachment as file', DB::raw('concat("'.url('/').'/files/", E.Attachment) as urlDoComprovante'), 'P.Name as period', 'PP.Name as property', 'S.Name as factor', DB::raw('if(P.`Name`="Anual",E.Year,if(P.`Name`="Mensal",concat(E.`Month`,"/",E.Year),concat(E.Semester,"/",E.Year))) as periodRef'), DB::raw('DATE_FORMAT(E.created_at, "%d/%m/%Y %H:%i:%s") as created_at'))
                ->leftJoin('emissionsource as S', 'S.id', '=', 'E.EmissionSourceId')
                ->leftJoin('period as P', 'P.id', '=', 'S.PeriodId')
                ->leftJoin('property as PP', 'PP.id', '=', 'S.PropertyId')
                ->leftJoin('emissionfactor as F', 'F.id', '=', 'S.EmissionFactorId')
                ->where('emission.EmissionSourceId', '=', $request->EmissionSourceId)
                ->get();
            return response()->json($emission, 200);
        } else {
            $emission = DB::table('emission as E')
                ->select('E.id', 'E.Amount', 'E.Attachment as file', 'P.Name as period', DB::raw('concat("'.url('/').'/files/", E.Attachment) as urlDoComprovante'), 'PP.Name as property', 'S.Name as factor', DB::raw('if(P.`Name`="Anual",E.Year,if(P.`Name`="Mensal",concat(E.`Month`,"/",E.Year),concat(E.Semester,"/",E.Year))) as periodRef'), DB::raw('DATE_FORMAT(E.created_at, "%d/%m/%Y %H:%i:%s") as created_at'))
                ->leftJoin('emissionsource as S', 'S.id', '=', 'E.EmissionSourceId')
                ->leftJoin('period as P', 'P.id', '=', 'S.PeriodId')
                ->leftJoin('property as PP', 'PP.id', '=', 'S.PropertyId')
                ->leftJoin('emissionfactor as F', 'F.id', '=', 'S.EmissionFactorId')
                ->get();

            return response()->json($emission, 200);
        }
    }

    public function getList(Request $request){
        $idProperty = $request->idproperty;
        $idEmissionSource= $request->idemissionsource;

        return response()->json($this->getList2($idProperty, $idEmissionSource, 'json'), 200);
    }

    public function getList2($idProperty, $idEmissionSource, $tipo="json"){
        $results= [];
        $currentYear = date('Y');
        $years = range(2022, $currentYear);
        $meses= [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
        $semesters= [1=>"Primeiro", 2=>"Segundo"];
        $periodo= "";

        $result = DB::table('emission as E')
            ->select('E.Year', 'E.Month', 'E.week', 'E.Semester', DB::raw('(SELECT name FROM period WHERE id=S.PeriodId LIMIT 1) as period'))
            ->leftJoin('emissionsource as S', 'S.id', '=', 'E.EmissionSourceId')
            ->where('S.PropertyId', '=', $idProperty)
            ->where('S.id', '=', $idEmissionSource)
            ->orderBy('Year')
            ->orderBy('Month')
            ->get();

        if($result->isNotEmpty()) {
            $res = collect($result);
            $periodo = strtolower($res->first()->period);

            if($periodo == "semanal"){
                foreach ($years as $y) {
                    $allWeeks = range(1, 53);
                    $usedWeeks = $res->where('Year', $y)->pluck('week')->unique()->all();
                    $missingWeeks = array_diff($allWeeks, $usedWeeks);
                    foreach ($missingWeeks as $w) {
                        $results[$y][$w] = ["year" => $y, "week" => $w];
                    }
                }
            } elseif ($periodo == "mensal") {
                foreach ($years as $y) {
                    $filteredResult[$y] = $res->filter(function ($item) use ($y) {
                        return $item->Year == $y;
                    });

                    if ($filteredResult[$y]->isNotEmpty()) {
                        $months[$y] = $filteredResult[$y]->pluck('Month')->unique()->sort()->all();
                        if($tipo == 'json') {
                            $missingMonths[$y] = array_values(array_diff(range(1, 12), $months[$y]));
                        } else {
                            $missingMonths[$y] = $months[$y];
                        }
                    } else {
                        $months[$y] = [];
                        if($tipo == 'json') {
                            $missingMonths[$y] = array_values(array_diff(range(1, 12), $months[$y]));
                        } else {
                            $missingMonths[$y] = $months[$y];
                        }
                    }
                }

                foreach ($missingMonths as $ano => $ms) {
                    if (count($ms) >= 1) {
                        foreach ($ms as $m) {
                            $results[$ano][$m] = ["value" => $m, "month" => $meses[$m]];
                        }
                    }
                }
            } elseif ($periodo == "anual") {
                $yearsRes =collect($result)->pluck('Year')->toArray();
                $results= array_diff($years, $yearsRes);
            } elseif ($periodo == "semestral") {
                foreach ($years as $y) {
                    $filteredResult[$y] = $res->filter(function ($item) use ($y) {
                        return $item->Year == $y;
                    });

                    if ($filteredResult[$y]->isNotEmpty()) {
                        $semester[$y] = $filteredResult[$y]->pluck('Semester')->unique()->sort()->all();
                        if($tipo == 'json') {
                            $missingMonths[$y] = array_values(array_diff(range(1, 2), $semester[$y]));
                        } else {
                            $missingMonths[$y] = $semester[$y];
                        }
                    } else {
                        $semester[$y] = [];
                        if($tipo == 'json') {
                            $missingMonths[$y] = array_values(array_diff(range(1, 2), $semester[$y]));
                        } else {
                            $missingMonths[$y] = $semester[$y];
                        }
                    }
                }

                foreach ($missingMonths as $ano => $ms) {
                    if (count($ms) >= 1) {
                        foreach ($ms as $m) {
                            $results[$ano][$m] = ["value" => $m, "semester" => $semesters[$m]];
                        }
                    }
                }
            }

            return ["anos" => $results, "periodo" => $periodo];
        } else {
            return [];
        }
    }

    public function update(Request $request){
        $emission = EmissionModel::find($request->id);

        if (!is_null($request->Attachment)) {
            $emission->Attachment = $request->Attachment;
        }

        if (!is_null($request->Amount)) {
            $emission->Amount = $request->Amount;
        }

        if (!is_null($request->InitialPeriod)) {
            $emission->InitialPeriod = $request->InitialPeriod;
        }

        if (!is_null($request->FinalPeriod)) {
            $emission->FinalPeriod = $request->FinalPeriod;
        }

        if (!is_null($request->EmissionSourceId)) {
            $emission->EmissionSourceId = $request->EmissionSourceId;
        }

        $emission->save();
        return response()->json($emission, 200);
    }

    public function deleteFonteEmissao(Request $request){
        $emission = EmissionModel::find($request->id);
        //dd($emission->created_at, $emission->created_at->diffInMinutes(), $emission->created_at->diffInMinutes() < 15);
        return response()->json([$emission], 200);
        if($emission->created_at->diffInMinutes() > 15){
            return response()->json(["msg" => "Não é permitido excluir o registro após 15 minutos de sua criação."], 201);
        } else {
            $emission->delete();
            DB::connection("mysqlSimulation")->table("simulation")->where("EmissionId", $request->id)->delete();
            return response()->json(true, 200);
        }
    }
}
