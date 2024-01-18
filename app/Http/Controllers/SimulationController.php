<?php

namespace App\Http\Controllers;

use App\Models\EmissionFactorModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimulationController extends Controller{
    private $factors;
    private $dadosDB;
    private $k1= 180.675;
    private $k2= 0.891;
    private $k3= (0.737/13.6);

    public function __construct(){
        $obj= new EmissionFactorModel();
        $this->dadosDB= collect($obj->All());

        $this->factors= [
            "SAHS"=> ["id"=>1, "value"=> $this->dadosDB->firstWhere('id', 1)->factor],
            "SESF"=> ["id"=>2, "value"=> $this->dadosDB->firstWhere('id', 2)->factor],
            "UBSM"=> ["id"=>3, "value"=> $this->dadosDB->firstWhere('id', 3)->factor],
            "SITED"=> ["id"=>4, "value"=> $this->dadosDB->firstWhere('id', 4)->factor],
            "RV"=> ["id"=>5, "value"=> $this->dadosDB->firstWhere('id', 5)->factor],
            "RP"=> ["id"=>6, "value"=> $this->dadosDB->firstWhere('id', 6)->factor],
            "RA"=> ["id"=>7, "value"=> $this->dadosDB->firstWhere('id', 7)->factor],
            "ROC"=> ["id"=>8, "value"=> $this->dadosDB->firstWhere('id',8)->factor],
            "RRO"=> ["id"=>9, "value"=> $this->dadosDB->firstWhere('id', 9)->factor],
            "RPL"=> ["id"=>10, "value"=> $this->dadosDB->firstWhere('id', 10)->factor],
            "RCC"=> ["id"=>11, "value"=> $this->dadosDB->firstWhere('id', 11)->factor],
            "SCAC"=> ["id"=>12, "value"=> $this->dadosDB->firstWhere('id', 12)->factor],
            "PAIE"=> ["id"=>13, "value"=> $this->dadosDB->firstWhere('id', 13)->factor],
            "PMA"=> ["id"=>14, "value"=> $this->dadosDB->firstWhere('id', 14)->factor]
        ];

        dd($this->factors);
    }

    public function setSimulation($PropertyId, $emissionFactorID, $valueFactor, $ano, $mes, $semestre){
        $obj= new EmissionFactorModel();
        $this->dadosDB= collect($obj->All());
        return [$this->dadosDB];

        $dadosDB= DB::table('emissionsource AS E')
            ->leftJoin('emissionfactor AS F', 'F.id', '=', 'E.EmissionFactorId')
            ->leftJoin('period AS P', 'P.id', '=', 'E.PeriodId')
            ->leftJoin('property AS PP', 'PP.id', '=', 'E.PropertyId')
            ->select(
                'PP.latitude AS lat',
                'PP.longitude AS lon',
                'PP.regionId',
                'P.Name AS period',
                'F.NameCode',
                DB::raw('CONCAT("calc", F.NameCode) AS functionFactor')
            )
            ->where('E.PropertyId', '=', $PropertyId)
            ->where('E.EmissionFactorId', '=', $emissionFactorID)
            ->get();

        $lat= $dadosDB[0]->lat;
        $lon= $dadosDB[0]->lon;
        $period= strtolower($dadosDB[0]->period);
        $functionFactor= $dadosDB[0]->functionFactor;
        $regionID= $dadosDB[0]->regionId;

        return $this->getArraySimulation($functionFactor, $lat, $lon, $emissionFactorID, $regionID, $period, $valueFactor, $ano, $mes, $semestre);
    }

    public function getArraySimulation($functionFactor, $lat, $lon, $emissionFactorID, $regionID, $period, $valueFactor, $ano, $mes, $semestre){
        $factorCalculado= $this->$functionFactor($valueFactor);
        return $factorCalculado;
        $calcDario= $this->calcDiario($factorCalculado, $period, $ano, $mes, $semestre);

        $dias= $calcDario[1];
        $amount= $calcDario[0];

        $arraySimulation = [];

        $data = new \DateTime("$ano-$mes-01");

        for ($dia = 1; $dia <= $dias; $dia++) {
            $dataFormatada = $data->format('Y-m-d');

            $arraySimulation[] = [
                'period' => $dataFormatada,
                'amount' => $amount,
                'lat' => $lat,
                'lon' => $lon,
                'emissionFactorID' => $emissionFactorID,
                'regionID' => $regionID
            ];

            $data->modify('+1 day');
        }

        return $arraySimulation;
    }

    public function calcDiario($factorCalculado, $periodo, $ano, $mes="0", $semestre="0"){
        if($periodo == "mensal"){
            $dias= cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            $factorDiario= $factorCalculado / $dias;
        } elseif($periodo == "semestral"){
            $mesesDoSemestre = ($semestre == 1) ? range(1, 6) : range(7, 12);
            $dias = 0;
            foreach ($mesesDoSemestre as $mes) {
                $dias+= cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            }
            $factorDiario= $factorCalculado / $dias;
        } elseif($periodo == "anual"){
            if (($ano % 4 == 0 && $ano % 100 != 0) || ($ano % 400 == 0)) {
                $dias= 366;
            } else {
                $dias= 365;
            }
            $factorDiario= $factorCalculado / $dias;
        }

        return [$factorDiario, $dias];
    }

    /**
     * Sistema de captação de água da chuva e/ou de reúso de água
     * @param $value
     * @return float|int
     */
    public function calcSCAC($value){
        $factorName= "SCAC";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }

    /**
     * Sistema de aquecimento solar
     * @param $value
     * @return float|int
     */
    public function calcSAHS($value){
        $factorName= "SAHS";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor*$this->k1)/1000;
    }

    /**
     * Sistema de energia solar fotovoltaica
     * @param $value
     * @return float|int
     */
    public function calcSESF($value){
        $factorName= "SESF";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }

    /**
     * Utilização de bicicletas como meio de locomoção
     * @param $value
     * @return float|int
     */
    public function calcUBSM($value){
        $factorName= "UBSM";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor*$this->k3)/1000;
    }

    /**
     * Sistemas individuais de tratamento de esgotos domésticos
     * @param $value
     * @return float|int
     */
    public function calcSITED( $value){
        $factorName= "UBSM";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor);
    }


    /**
     * Reciclagem de vidro
     * @param $value
     * @return float|int
     */
    public function calcRV($value){
        $factorName= "SESF";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }

    /**
     * Reciclagem de plástico
     * @param $value
     * @return float|int
     */
    public function calcRP($value){
        $factorName= "RP";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }

    /**
     * Reciclagem de alumínio
     * @param $value
     * @return float|int
     */
    public function calcRA($value){
        $factorName= "RA";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }

    /**
     * Reciclagem de óleo de cozinha
     * @param $value
     * @return float|int
     */
    public function calcROC($value){
        $factorName= "ROC";
        $factor= $this->factors[$factorName]['value'];
        return ["ROC", $value];
        return ($value*$factor*$this->k2)/1000;
    }

    /**
     * Reciclagem de resíduos orgânicos
     * @param $value
     * @return float|int
     */
    public function calcRRO($value){
        $factorName= "RRO";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }

    /**
     * Reciclagem de papel
     * @param $value
     * @return float|int
     */
    public function calcRPL($value){
        $factorName= "RPL";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }

    /**
     * Utilização de (RCC) resíduos de construção civil
     * @param $value
     * @return float|int
     */
    public function calcRCC($value){
        $factorName= "RCC";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }

    /**
     * Preservação de área com importância ecológica
     * @param $value
     * @return float|int
     */
    public function calcPAIE($value){
        $factorName= "PAIE";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor);
    }

    /**
     * Plantio e manutenção de árvores
     * @param $value
     * @return float|int
     */
    public function calcPMA($value){
        $factorName= "PMA";
        $factor= $this->factors[$factorName]['value'];
        return ($value*$factor)/1000;
    }
}
