<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmissionModel;
use Illuminate\Support\Facades\DB;
class EmissionController extends Controller{
    public function set(Request $request){
        $file = $request->file('attachment');

        if(!empty($file)) {
            $path = $file->store('public/attachments');
        }

        $emission = new EmissionModel();

        if(!empty($file)) {
            $emission->Attachment = basename($path);
        }

        $emission->Amount = $request->amount;
        $emission->EmissionSourceId = $request->EmissionSourceId;
        $emission->Month = $request->month;
        $emission->Year = $request->year;
        $emission->Semester = ($request->month * 1) <= 6 ? 1 : 2;
        $emission->save();

        return response()->json($emission, 201);
    }

    public function get(Request $request){
        if($request->has('EmissionSourceId') && $request->has('propertyId')){
            $emission = DB::table('emission AS E')
                ->select('E.id', 'E.Amount', 'E.Attachment as file', DB::raw('concat("'.url('/').'/files/", E.Attachment) as urlDoComprovante'), 'P.Name as period', 'PP.Name as property', 'ES.Name as factor', DB::raw('if(P.`Name`="Anual",E.Year,if(P.`Name`="Mensal",concat(E.`Month`,"/",E.Year),concat(E.Semester,"/",E.Year))) as periodRef'), 'E.created_at')
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
                ->select('E.id', 'E.Amount', 'E.Attachment as file', DB::raw('concat("'.url('/').'/files/", E.Attachment) as urlDoComprovante'), 'P.Name as period', 'PP.Name as property', 'S.Name as factor', DB::raw('if(P.`Name`="Anual",E.Year,if(P.`Name`="Mensal",concat(E.`Month`,"/",E.Year),concat(E.Semester,"/",E.Year))) as periodRef'), 'E.created_at')
                ->leftJoin('emissionsource as S', 'S.id', '=', 'E.EmissionSourceId')
                ->leftJoin('period as P', 'P.id', '=', 'S.PeriodId')
                ->leftJoin('property as PP', 'PP.id', '=', 'S.PropertyId')
                ->leftJoin('emissionfactor as F', 'F.id', '=', 'S.EmissionFactorId')
                ->where('emission.EmissionSourceId', '=', $request->EmissionSourceId)
                ->get();
            return response()->json($emission, 200);
        } else {
            $emission = DB::table('emission as E')
                ->select('E.id', 'E.Amount', 'E.Attachment as file', 'P.Name as period', DB::raw('concat("'.url('/').'/files/", E.Attachment) as urlDoComprovante'), 'PP.Name as property', 'S.Name as factor', DB::raw('if(P.`Name`="Anual",E.Year,if(P.`Name`="Mensal",concat(E.`Month`,"/",E.Year),concat(E.Semester,"/",E.Year))) as periodRef'), 'E.created_at')
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
        $results= [];
        $currentYear = date('Y');
        $years = range(2022, $currentYear);
        $meses= [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];

        $result = DB::table('emission as E')
            ->select('E.Year', 'E.Month', 'E.Semester', DB::raw('(SELECT name FROM period WHERE id=S.PeriodId LIMIT 1) as period'))
            ->leftJoin('emissionsource as S', 'S.id', '=', 'E.EmissionSourceId')
            ->where('S.PropertyId', '=', $idProperty)
            ->where('S.id', '=', $idEmissionSource)
            ->orderBy('Year')
            ->orderBy('Month')
            ->get();

        if($result->isNotEmpty()) {
            $res = collect($result);
            $periodo = strtolower($res->first()->period);

            if ($periodo == "mensal") {
                foreach ($years as $y) {
                    $filteredResult[$y] = $res->filter(function ($item) use ($y) {
                        return $item->Year == $y;
                    });

                    if ($filteredResult[$y]->isNotEmpty()) {
                        $months[$y] = $filteredResult[$y]->pluck('Month')->unique()->sort()->all();
                        $missingMonths[$y] = array_values(array_diff(range(1, 12), $months[$y]));
                    } else {
                        $months[$y] = [];
                        $missingMonths[$y] = array_values(array_diff(range(1, 12), $months[$y]));
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

            }

            return response()->json(["anos" => $results], 200);
        } else {
            return response()->json([], 200);
        }
    }
    public function getList2(Request $request){
        $idProperty= $request->idproperty;
        $year= $request->year;
        $period= "#";

        $meses= [1,2,3,4,5,6,7,8,9,10,11,12];
        $semestres= [1,2];
        $anos= [];
        $resp= [];

        for($a=2022; $a<=(date("Y")*1); $a++){
            $anos[]= $a;
        }

        $result = DB::table('emission as E')
            ->select('E.Year', 'E.Month', 'E.Semester', DB::raw('(SELECT name FROM period WHERE id=S.PeriodId LIMIT 1) as period'))
            ->leftJoin('emissionsource as S', 'S.id', '=', 'E.EmissionSourceId')
            ->where('S.PropertyId', '=', $idProperty)
            ->where('E.Year', '=', $year)
            ->get();

        if(count($result)>=1){
            $period= $result[0]->period;

            if($period == "Mensal") {
                $in= 0;
                foreach ($result as $r) {
                    $i = ($r->Month * 1) - 1;
                    unset($meses[$i]);
                    $in++;
                }

                $resultYears = DB::table('emission as E')
                    ->select('E.Year', DB::raw('count(E.Year) as nYear'))
                    ->leftJoin('emissionsource as S', 'S.id', '=', 'E.EmissionSourceId')
                    ->where('S.PropertyId', '=', $idProperty)
                    ->groupBy('E.Year')
                    ->havingRaw('count(E.Year) <= 11')
                    ->get();

                foreach ($resultYears as $ry){
                    $resultYear[]= $ry->Year;
                }

                $resp = ["months" => $meses, "year"=>$resultYear];
            } elseif($period == "Semestral") {
                foreach ($result as $r) {
                    $i = ($r->Semester * 1) - 1;
                    unset($semestres[$i]);
                }
                $resp = ["semesters" => $semestres, "year"=>$anos];
            } elseif($period == "Anual") {
                foreach ($result as $r) {
                    $i = array_search($r->Year, $anos);
                    unset($anos[$i]);
                }
                $resp = ["year"=>$anos];
            }
        }

        return response()->json([$resp,$result], 201);
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
}
