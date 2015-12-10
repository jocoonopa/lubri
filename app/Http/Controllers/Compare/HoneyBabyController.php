<?php

namespace App\Http\Controllers\Compare;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Compare\HoneyBaby;
use App\Utility\Chinghwa\Helper\Compare\Callback\Callback;
use App\Utility\Chinghwa\Helper\Temper;
use App\Utility\Chinghwa\Helper\Compare\FileHelper;
use App\Utility\Chinghwa\Helper\Compare\Handler\MixInfoHandler;
use Illuminate\Http\Request;

use Input;
use Response;
use Maatwebsite\Excel\Facades\Excel;

class HoneyBabyController extends Controller
{
    protected $temper;
    protected $callback;
    protected $fileHelper;
    protected $mixInfoHandler;
    protected $insertFile;
    protected $updateFile;

    public function index()
    {
        return view('compare.honeybaby.index', ['title' => HoneyBaby::TITLE, 'res' => '']);
    }

    public function process(Request $request)
    {           
        $this->initProcess()->genFlapFiles();

        $this->temper->setEndTime(microtime(true));

        return view('compare.honeybaby.index', [
            'title' => HoneyBaby::TITLE, 
            'res'   => view('compare.honeybaby.test', [
                'exectime' => floor($this->temper->getEndTime() - $this->temper->getStartTime()),
                'stamp' => $this->temper->getStamp()
            ]),
        ]);
    }

    protected function initProcess()
    {
        $this->temper = new Temper();
        $this->temper->setStartTime(microtime(true));
        $this->temper->setStamp(uniqid());

        $this->fileHelper = new FileHelper($this->temper->getStamp());
        $this->fileHelper->genFlapInsertFile();
        $this->fileHelper->genFlapUpdateFile();
        
        $this->callback = new Callback($this->moveUploadFile());
        
        $this->mixInfoHandler = new MixInfoHandler(Input::get('year') . Input::get('month'));

        return $this;
    }

    public function downloadInsertExample()
    {
        $filePath = __DIR__ . '/../../../../storage/excel/example/insertFormat.xls';
        $headers = ['Content-Type: application/excel'];

        return Response::download($filePath, 'FlapMemberInsertExample.xls', $headers);
    }

    public function downloadUpdateExample()
    {
        $filePath = __DIR__ . '/../../../../storage/excel/example/updateFormat.xls';
        $headers = ['Content-Type: application/excel'];

        return Response::download($filePath, 'FlapMemberUpdateExample.xls', $headers);
    }

    public function downloadInsert(Request $request)
    {
        $dateTime = date('YmdH');

        $filePath = __DIR__ . '/../../../../storage/excel/exports/' .  ExportExcel::HONEYBABY_FILENAME . Input::get('stamp') . '_Insert.xls';
        $headers = ['Content-Type: application/excel'];

        return Response::download($filePath, "FlapMemberInsert_{$dateTime}.xls", $headers);
    }

    public function downloadUpdate(Request $request)
    {
        $dateTime = date('YmdH');

        $filePath = __DIR__ . '/../../../../storage/excel/exports/' .  ExportExcel::HONEYBABY_FILENAME . Input::get('stamp') . '_Update.xls';
        $headers = ['Content-Type: application/excel'];

        return Response::download($filePath, "FlapMemberUpdate_{$dateTime}.xls", $headers);
    }

    /**
     * 移動上傳檔案至指定資料夾
     *
     * @return string $realPath
     */
    protected function moveUploadFile()
    {
        $fileName        = ExportExcel::HONEYBABY_FILENAME . $this->temper->getStamp();
        $destinationPath = __DIR__ . '/../../../../storage/excel/import/';
        
        Input::file('excel')->move($destinationPath, $fileName);

        return $this->fileHelper->getImportRealPath();
    }

    protected function genFlapFiles()
    {        
        $excel = \App::make('excel');
        $this->insertFile = $excel->load($this->fileHelper->getInsertFilePath());
        $this->updateFile = $excel->load($this->fileHelper->getUpdateFilePath());

        $this->injectInsertAndUpdateData();

        $this->insertFile->store('xls', storage_path('excel/exports'));
        $this->updateFile->store('xls', storage_path('excel/exports'));

        return $this;
    }

    protected function injectInsertAndUpdateData()
    {
        return Excel::selectSheetsByIndex(0)
            ->filter('chunk')
            ->load($this->callback->getData()['realpath'])
            ->skip(HoneyBaby::SKIP_LARAVEL_EXCEL_CHUNK_BUG_INDEX)
            ->chunk(HoneyBaby::CHUNK_SIZE, $this->getChunkProcess());
    }

    protected function getChunkProcess()
    {
        return function ($result) {
            $this->callback->mergeData($this->mixInfoHandler->extendData($result, $this->callback->getData()));

            $this->insertFile->sheet(HoneyBaby::SHEETNAME_MEMBER_PROFILE, $this->callback->getAppendInsertClosureMemberProfile());
            $this->insertFile->sheet(HoneyBaby::SHEETNAME_MEMBER_DISTFLAG, $this->callback->getAppendInsertClosureMemberDistFlag());

            $this->updateFile->sheet(HoneyBaby::SHEETNAME_MEMBER_PROFILE, $this->callback->getAppendUpdateClosureMemberProfile());
            $this->updateFile->sheet(HoneyBaby::SHEETNAME_MEMBER_DISTFLAG, $this->callback->getAppendUpdateClosureMemberDistFlag());
            
            return $this->callback->migrateData();          
        };
    }
}