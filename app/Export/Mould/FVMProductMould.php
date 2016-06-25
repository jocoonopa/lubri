<?php

namespace App\Export\Mould;

class FVMProductMould extends FVMould
{
    protected $head =  [
        '品名',
        '規格',
        '商品代碼',
        '品牌名稱',
        '品牌主鍵',
        '中品牌名稱',
        '中品牌主鍵',
        '單位名稱',
        '單位主鍵',
        '中單位名稱',
        '中單位主鍵',
        '中單位數量',
        '大分類名稱',
        '大分類主鍵',
        '中分類名稱',
        '中分類連接大分類外鍵',
        '中分類主鍵',
        '小分類名稱',
        '小分類連接中分類外鍵',
        '小分類主鍵',
        '類別代碼',
        '類別名稱',
        '含稅售價',
        '不含稅售價',
        '商品來源',
        '商品性質',
        '是否停用',
        '庫存'
    ];

    public function getRow(array $product)
    {
        $product = $this->convertAttrVal($product);

        foreach ($this->head as $columnName) {
            $product[$columnName] = $this->transfer(array_get($product, $columnName));
        }

        return $product;
    }

    /**
     * GoodsSource 商品來源 (0 : 國內採購 1 : 國外採購 2 : 自製 3 : 委外加工 4 : 其他)
     * GoodsType 商品性質 (0 : 成品 1 : 半成品 2 : 原料 3 : 物料/零件 4 : 其他 5 : 樣本 6 : 特販)
     */
    protected function convertAttrVal(array $product)
    {
        $soureceMap = ['國內採購', '國外採購', '自製', '委外加工', '其他'];
        $typeMap = ['成品', '半成品', '原料', '物料/零件', '其他', '樣本', '特販'];
        $product['商品來源'] = array_get($soureceMap, $product['商品來源']);
        $product['商品性質'] = array_get($typeMap, $product['商品性質']);
        $product['庫存'] = (int) $product['庫存'];

        return $product;
    }
}