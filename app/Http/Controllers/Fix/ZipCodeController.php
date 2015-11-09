<?php

namespace App\Http\Controllers\Fix;

use App\Http\Controllers\Controller;

class ZipCodeController extends Controller
{
    public function birth()
    {
        $codes = ['N039215','N039216','N039217','N039218','N039219','N039221','N039223','N039224','N039225','N039227','N039228','N039229','N039230','N039232','N039233','N039234','N039235','N039237','N039238','N039239','N039240','N039241','N039242','N039243','N039244','N039245','N039246','N039247','N039248','N039249','N039250','N039251','N039252','N039253','N039254','N039255','N039256','N039257','N039258','N039259','N039260','N039261','N039262','N039263','N039264','N039265','N039266','N039267','N039268','N039269','N039270','N039271','N039272','N039273','N039274','N039275','N039276','N039277','N039278','N039279','N039280','N039281','N039282','N039283','N039284','N039285','N039286','N039287','N039288','N039289','N039290','N039291','N039292','N039293','N039294','N039295','N039296','N039297','N039298','N039299','N039300','N039301','N039303','N039304','N039306','N039307','N039308','N039309','N039310','N039311','N039312','N039313','N039314','N039315','N039316','N039317','N039318','N039319','N039320','N039321','N039322','N039323','N039324','N039325','N039326','N039327','N039328','N039329','N039330','N039331','N039332','N039333','N039305','N039335','N039337','N039338','N039339','N039340','N039341','N039342','N039343','N039344','N039345','N039346','N039347','N039348','N039349','N039350','N039351','N039352','N039353','N039354','N039355','N039356','N039358','N039359','N039360','N039361','N039362','N039363','N039364','N039365'];
        $births = ['19531115','19500910','','19570624','19800823','','19561102','19490925','19510716','19551104','19640311','19840209','19700203','19841123','19360615','19640730','19481011','19701229','19600331','','19521007','19520129','','','19681014','19530102','19510118','19640110','','19440924','','19560811','','','19500111','19630407','19690625','19561228','19680810','19690505','19750718','19570517','19460403','19470320','19510120','19570614','19531113','','19630326','19840222','','','','19831002','','','','19541208','19601006','','19460325','19310915','','19780211','','19490604','19720119','','19750507','','','19510329','19700511','','19700319','','19580225','','19610930','19390620','19511228','','19600904','19590330','','','19521007','','19661013','19681124','','19640511','19661026','','19691220','','','19550911','19550221','19660801','','19640717','19611029','19731126','19630208','19631005','19411129','19330105','19470822','','19601025','19530120','19600809','19741209','19630226','','','19491226','','','','','19431204','','19540919','','19580425','19570528','19551118','19520308','19541220','19581221','19600906','19880131','19490820','19560528','19460908','19521201','19560301','19461013','19690321', ''];
    
        $sqlbase = 'UPDATE POS_Member SET ';

        foreach ($codes as $key => $code) {
            $execSql = $sqlbase . 'Birthday=\'' . $births[$key] . '\' WHERE Code=\''. $code .'\'';

            echo $execSql . "<br/>";
        }

        return NULL;
    }

	public function index()
	{
		$startTime = microtime(true);

		$i = 0;
	    $codeIndexArray = $this->convertToCodeIndexArray();

	    $queryBIG5 = $this->cb5($this->genFetchQuery());
	    $res = odbc_exec($this->connectToErp(), $queryBIG5);
	    
	    while ($member = odbc_fetch_array($res)) {
	    	$cityAndStateArr = $this->getArrayVal($codeIndexArray, $member['HomeAddress_ZipCode'], ['city' => '', 'state' => '']);

		    $member['HomeAddress'] = $cityAndStateArr['state'] . '$' . $cityAndStateArr['city'] . '$' . $this->c8($member['HomeAddress_Address']);
		    $member['HomeAddress_Address'] = $this->c8($member['HomeAddress_Address']);
		    $member['HomeAddress_City'] = $cityAndStateArr['city'];
		    $member['HomeAddress_State'] = $cityAndStateArr['state'];

		    echo "<pre>"; echo $this->genUpdateQuery($member); echo "</pre>";
		    //odbc_exec($this->connectToErp(), $this->cb5($this->genUpdateQuery($member)));

		    $i ++;
	    }

	    $endTime = microtime(true);

	    $exectime = floor($endTime - $startTime);

	    return view('basic.simple', [
	    	'title' => '會員地址修正', 
	    	'des' => '修正台北縣，空白的縣市和區，統一地址格式',
	    	'res' => '共修正了' . $i . '筆，耗時' . $exectime . '秒' 
	    ]);
	}

	protected function genFetchQuery()
	{
		$sql = "SELECT * FROM POS_Member AS m ";
		$sql.= " WHERE ";
		$sql.= " LEN(m.HomeAddress_ZipCode) > 0 AND ";
		$sql.= " m.CRT_TIME >= '20150901' ";

		return $sql;
	}

	protected function genUpdateQuery(array $member)
	{
		$sql = 'UPDATE POS_Member SET ';
		$sql.= " HomeAddress='" . $member['HomeAddress'] . "',";
		$sql.= " HomeAddress_City='" . $member['HomeAddress_City'] . "',";
		$sql.= " HomeAddress_State='" . $member['HomeAddress_State'] . "',";
		$sql.= " HomeAddress_Address='" . str_replace([$member['HomeAddress_City'], $member['HomeAddress_State']], ['', ''], $member['HomeAddress_Address']) . "'";
		$sql.= " WHERE SerNo='" . $member['SerNo'] . "'";

		return $sql;
	}

	protected function convertToCodeIndexArray()
	{
		$json = $this->getThreeCodeJSON();

		$states = json_decode($json, true);

		$codeIndexArray = array();

		foreach ($states as $state => $citys) {
			foreach ($citys as $cityName => $code) {
				$codeIndexArray[$code]['state'] = $state;
				$codeIndexArray[$code]['city'] = $cityName;
			}
		}

		return $codeIndexArray;
	}
}