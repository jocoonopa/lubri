<?php

namespace App\Console\Commands\FV\Import;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Console\Command;

class Product extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importproduct:fv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!file_exists(storage_path('excel/exports/fvimport/'))) {
            mkdir(storage_path('excel/exports/fvimport/'), 0777, true);
        }
        
        $fname = storage_path('excel/exports/fvimport/') . 'product_export_' . time() . '.csv';

        $this->comment("\r\n||||||||||||||||||||||||||  FVImport Product Export ||||||||||||||||||||||||||\r\n");  

        $file = fopen($fname, 'w');
        
        fwrite($file, bomstr());

        foreach ($this->getData() as $product) {
            fwrite($file, implode(',', $this->genMouldProduct($product)) . "\r\n");
        }

        fclose($file);

        $this->comment("\r\n--------------------------------------------------------\r\n{$fname}");        
    }

    protected function getData()
    {
        $sql = Processor::getStorageSql('FV/Import/product.sql');

        return Processor::getArrayResult($sql);
    }

    /**
     * GoodsSource 商品來源 (0 : 國內採購 1 : 國外採購 2 : 自製 3 : 委外加工 4 : 其他)
     * GoodsType 商品性質 (0 : 成品 1 : 半成品 2 : 原料 3 : 物料/零件 4 : 其他 5 : 樣本 6 : 特販)
     */
    protected function genMouldProduct(array $product)
    {
        $soureceMap = ['國內採購', '國外採購', '自製', '委外加工', '其他'];
        $typeMap = ['成品', '半成品', '原料', '物料/零件', '其他', '樣本', '特販'];
        $product['商品來源'] = array_get($soureceMap, $product['商品來源']);
        $product['商品性質'] = array_get($typeMap, $product['商品性質']);
        $product['庫存'] = (int) $product['庫存'];

        return $product;
    }
}
