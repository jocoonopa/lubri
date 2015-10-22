<?php

namespace App\Utility\Chinghwa;

abstract class ExportExcel 
{
	const TOKEN = 'Jocoonopa0622';
	const CREATOR = 'mis@chinghwa.com.tw';
	const COMPANY = 'chinghwa';

	const FONT_DEFAULT = '細明體';

	const BOLDER_DEFAULT = 'thin';

	/**
	 * M64 = 64期刊
	 */
	const M64_FILENAME = 'Result_m64';
	const M64_IMPORT_FILENAME = 'm64';
	const M64_TITLE = '64期刊';

	/**
	 * DCC = Daily Credit Card
	 */
	const DCC_FILENAME = 'Daily_Credit_Record_';

	const DCCU_FILENAME = 'Daily_Up_Brush_';

	/**
	 * 寵兒
	 */
	const HONEYBABY_FILENAME = 'Honey_Baby_';
	const HONEYBABY_EXIST_TITLE = 'Honey_Baby_Compare_Exist';
	const HONEYBABY_NEW_TITLE = 'Honey_Baby_Compare_New';

	/**
	 * 門市營業額分析 Retail_Store
	 */
	const RS_FILENAME = 'Retail_Store_Ana';
	const RS_TITLE = 'Retail_Store_Ana';

	/**
	 * 員購銷貨單
	 */
	const EMPP_FILENAME = 'Emp_Purchase';

	/**
	 * 康思特銷退貨
	 */
	const CONCE_FILENAME = 'Conce_Monthly';

	/**
	 * 每月進銷退
	 */
	const SPB_FILENAME = 'SPB_Monthly';

	/**
	 * 銷售模組成效
	 */
	const PROMOGRADE_FILENAME = 'PromoGrade_Monthly';

	/**
	 * 偉特CTI Import Layout
	 */
	const WAYTER_IMPORTLAYOUT_FILENAME = 'CTI_Import_Layout';
}