<?php

namespace App\Http\Controllers\Compare;

use Validator;
use Input;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Magazine64Controller extends Controller
{
    const COLUMN_NAME_INDEX = 3;
    const COLUMN_TEL_INDEX = 4;

    public function index(Request $request)
    {
    	return view('compare.m64.index', ['title' => '64期刊比對', 'res' => '']);
    }

    public function process(Request $request)
    {
        set_time_limit(0);

        $validator = Validator::make($request->all(),  ['excel' => 'required|mimes:xlsx']);

        if ($validator->fails()) {
            return view('compare.m64.index', ['title' => '64期刊比對', 'res' => '檔案驗證不合法']);
        }

        $file = Input::file('excel');
        $fileName = ExportExcel::M64_IMPORT_FILENAME;
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/import/';
        $realPath = $destinationPath . $fileName;
        $file->move($destinationPath, $fileName);
        
        $data = array('name' => [], 'tel' => []);
       
        Excel::load($realPath, function ($reader) use (&$data) {
            // 不要把第一行當作作屬性名稱(因為中文會報錯)
            $reader->noHeading(); 

            // 略過第一行(第一行只是標頭)
            $reader->skip(1);

            $reader->each(function ($readSheet) use (&$data) {
                $readSheet->each(function ($row) use (&$data)  {
                    if (!empty($row[self::COLUMN_NAME_INDEX])) {
                        $data['name'][] = srp($row[self::COLUMN_NAME_INDEX]);
                    }
                    
                    if (!empty($row[self::COLUMN_TEL_INDEX])) {
                        $data['tel'][] = srp($row[self::COLUMN_TEL_INDEX]);
                    }
                });
            });
        });

        return view('compare.m64.index', ['title' => '64期刊比對', 'res' => $this->genRepeatGroupQuery($data)]);
    }

    /**
     * 產生搜尋的Query
     * 
     * @param  $data
     * @return string [serch query]
     */
    protected function genRepeatGroupQuery($data)
    {       
        $replaceNameStatement = $this->genQueryNestReplace($this->queryReplaceWordsArray, array(), 'm.Name');
        $replaceMobilStatement = $this->genQueryNestReplace($this->queryReplaceWordsArray, array(), 'm.CellPhone');
        $replaceHomeTelStatement = $this->genQueryNestReplace($this->queryReplaceWordsArray, array(), 'm.HomeTel');
        $replaceOfficeTelStatement = $this->genQueryNestReplace($this->queryReplaceWordsArray, array(), 'm.OfficeTel');
        
        $findRepeatQuery = 'SELECT m.code 會員代號, m.Name 會員姓名, c.Name 開發單位,e.Name 開發人員, m.HomeTel 住宅電話, m.OfficeTel 公司電話, m.CellPhone 行動電話, m.HomeAddress_State+m.HomeAddress_City+m.HomeAddress_Address 住宅地址 ';
        $findRepeatQuery.= 'FROM dbo.POS_Member m ';
        $findRepeatQuery.= 'LEFT JOIN dbo.CCS_CRMFields crm ON m.SerNo=crm.MemberSerNoStr ';
        $findRepeatQuery.= 'LEFT JOIN dbo.HRS_Employee e ON crm.ExploitSerNoStr=e.SerNo ';
        $findRepeatQuery.= 'LEFT JOIN FAS_Corp c ON e.CorpSerNo=c.SerNo ';
        $findRepeatQuery.= 'LEFT JOIN CCS_MemberFlags f ON m.SerNo=f.MemberSerNoStr ';
        // $findRepeatQuery.= 'LEFT JOIN FAS_Corp c ON e.CorpSerNo=c.SerNo '; 營養師部門
        $findRepeatQuery.= 'WHERE f.Distflags_26=\'K\' AND (' . $replaceMobilStatement . ' IN' . $this->genInQuery($data['tel']) .' AND LEN(' . $replaceMobilStatement . ')>0) ';
        $findRepeatQuery.= ' OR (' . $replaceNameStatement . ' IN' . $this->genInQuery($data['name']);
        $findRepeatQuery.= ' OR (' . $replaceHomeTelStatement . ' IN' . $this->genInQuery($data['tel']) .' AND LEN(' . $replaceHomeTelStatement . ')>0) ';
        $findRepeatQuery.= ' OR (' . $replaceOfficeTelStatement . ' IN' . $this->genInQuery($data['tel']) .' AND LEN(' . $replaceOfficeTelStatement . ')>0)) ';
        $findRepeatQuery.= ' ORDER BY m.Name, m.Code';

        return $findRepeatQuery;
    }
}