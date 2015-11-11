<?php

namespace App\Utility\Chinghwa\Compare;

abstract class HoneyBaby
{
	const CHUNK_SIZE                                   = 200;

	const TITLE 									   = '寵兒比對'; 
	const SHEETNAME_MEMBER_PROFILE 					   = '會員資料';
	const SHEETNAME_MEMBER_DISTFLAG 				   = '會員旗標';
    
    const MEMBER_CATEGORY_CODE                         = '126';
    const MEMBER_LEVEL_CODE                            = '00-01';
    const MEMBER_EMP_CODE                              = '20090568';
    const DEFAULT_AREACODE 							   = '000';
    
    const IMPORT_NAME_INDEX                            = 1;
    const IMPORT_BIRTHDAY_INDEX                        = 2;
    const IMPORT_AREACODE_INDEX                        = 3;
    const IMPORT_STATE_INDEX                           = 4;
    const IMPORT_CITY_INDEX                            = 5;
    const IMPORT_ADDRESS_INDEX                         = 6;
    const IMPORT_HOMETEL_INDEX                         = 7;
    const IMPORT_COMPANYTEL_INDEX                      = 8;
    const IMPORT_MOBILE_INDEX                          = 9;
    const IMPORT_EMAIL_INDEX                           = 10;
    const IMPORT_FLAG23_INDEX                          = 11;
    const IMPORT_OLDMEMO_INDEX                         = 17;
    const IMPORT_NEWMEMO_INDEX                         = 14;
    
    const EXPORT_INSERT_MEMBERINFO_SERNO_INDEX         = 0;
    const EXPORT_INSERT_MEMBERINFO_CREATEDATE_INDEX    = 1;
    const EXPORT_INSERT_MEMBERINFO_NAME_INDEX          = 2;
    const EXPORT_INSERT_MEMBERINFO_SEX_INDEX           = 3;
    const EXPORT_INSERT_MEMBERINFO_BIRTHDAY_INDEX      = 4;
    const EXPORT_INSERT_MEMBERINFO_CATEGORYCODE_INDEX  = 6;
    const EXPORT_INSERT_MEMBERINFO_DISCODE_INDEX       = 7;
    const EXPORT_INSERT_MEMBERINFO_LEVEL_INDEX         = 8;
    const EXPORT_INSERT_MEMBERINFO_HOMETEL_INDEX       = 12;
    const EXPORT_INSERT_MEMBERINFO_COMPANYTEL_INDEX    = 14;
    const EXPORT_INSERT_MEMBERINFO_MOBILE_INDEX        = 16;
    const EXPORT_INSERT_MEMBERINFO_AREACODE_PREV_INDEX = 17;
    const EXPORT_INSERT_MEMBERINFO_AREACODE_NEXT_INDEX = 18;
    const EXPORT_INSERT_MEMBERINFO_ADDRESS_INDEX       = 19;
    const EXPORT_INSERT_MEMBERINFO_EMAIL_INDEX         = 23;
    const EXPORT_INSERT_MEMBERINFO_EMPCODE_INDEX       = 24;
    const EXPORT_INSERT_MEMBERINFO_MEMO_INDEX          = 40;

    const TW_DATE_LENGTH                               = 6;
    const BASE_YEAR                                    = 1911;
    const DEFAULT_3738FLAG_VALUE                       = 'N';
    const DEFAULT_0405FLAG_VALUE                       = 'N';
    const DEFAULT_08FLAG_VALUE                         = 'Y';	
}