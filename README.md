# lubri
[Next]

- Lubri [
    tools {
        git(mock git flow),
        db(mysql + sqlsrv),
        phpunit,
        phpspec,
        CT,
        scrutinizer-ci
    },

    to-do: {
        - 營養師資料綜合查詢會員資料外掛   
        - 套用 http://demo.thedevelovers.com/dashboard/kingadmin-v1.4/form-inplace-editing.html# layout
        - shopfi 套用
        - 銜接輔翼，CTI ，官網
        - 重構目前報表程式碼
    },

    memo: {
        目前 phpunit, phpspec 的使用透過一般 cmd, git bash 負責 git control 的部分
    }
]


Report Basic Component:
[
    App\Utility\Chinghwa\Report\NewExcel\SomeExport, 
    App\Utility\Chinghwa\Report\NewExcel\SomeExportHandler,
    App\Utility\Chinghwa\Report\NewExcel\DataHelper\SomeDataHelper,
    App\Utility\Chinghwa\Report\Some

    => 

    App\Utility\Chinghwa\Report\NewExcel\Some\SomeExport, 
    App\Utility\Chinghwa\Report\NewExcel\Some\SomeExportHandler,
    App\Utility\Chinghwa\Report\NewExcel\Some\DataHelper\SomeDataHelper,
    App\Utility\Chinghwa\Report\Some
]

Laravel Simple ACL
https://gist.github.com/drawmyattention/8cb599ee5dc0af5f4246

Laravel Blade Inject Service && Directive
https://mattstauffer.co/blog/custom-conditionals-with-laravels-blade-directives

# Material Guilde
https://github.com/FezVrasta/bootstrap-material-design
http://fezvrasta.github.io/bootstrap-material-design/bootstrap-elements.html

// 十一月
// return [
//     'S009' => [
//         'name' => '大直門市部',
//         'goal' => 500000,
//         'pl' => 216000 
//     ],
//     'S013' => [
//         'name' => '新光站前',
//         'goal' => 236000,
//         'pl' => 641520 
//     ],
//     'S049' => [
//         'name' => '新光A8館',
//         'goal' => 647000,//800000,
//         'pl' => 357243 
//     ],

//     'S008' => [
//         'name' => '高雄SOGO門市部',
//         'goal' => 1575000,
//         'pl' => 915000 
//     ],
//     'S014' => [
//         'name' => '新光台中',
//         'goal' => 567000,
//         'pl' => 329400 
//     ],
//     'S017' => [
//         'name' => '大統百貨',
//         'goal' => 572000,
//         'pl' => 446900 
//     ],
//     'S028' => [
//         'name' => '台南新天地',
//         'goal' => 2124000,
//         'pl' => 1254260 
//     ],
//     'S051' => [
//         'name' => '漢神巨蛋',
//         'goal' => 446000,
//         'pl' => 221000 
//     ]
// ];

// 十月
// return [
//     'S009' => [
//         'name' => '大直門市部',
//         'goal' => 1350000,
//         'pl' => 775000 
//     ],
//     'S013' => [
//         'name' => '新光站前',
//         'goal' => 271000,
//         'pl' => 185760 
//     ],
//     'S049' => [
//         'name' => '新光A8館',
//         'goal' => 2510000,
//         'pl' => 1858267 
//     ],

//     'S008' => [
//         'name' => '高雄SOGO門市部',
//         'goal' => 147000,
//         'pl' => 85400 
//     ],
//     'S014' => [
//         'name' => '新光台中',
//         'goal' => 2657000,
//         'pl' => 1543300 
//     ],
//     'S017' => [
//         'name' => '大統百貨',
//         'goal' => 1323000,
//         'pl' => 1033200 
//     ],
//     'S028' => [
//         'name' => '台南新天地',
//         'goal' => 215000,
//         'pl' => 127100 
//     ],
//     'S051' => [
//         'name' => '漢神巨蛋',
//         'goal' => 845000,
//         'pl' => 418600 
//     ]
// ];
// 九月
//   return [        
    // 'S009' => [
    //  'name' => '大直門市部',
    //  'goal' => 357000,
    //  'pl' => 170000
    // ],
    // 'S013' => [
    //  'name' => '新光站前',
    //  'goal' => 388000,
    //  'pl' => 266400
    // ],
    // 'S049' => [
    //  'name' => '新光A8館',
    //  'goal' => 510000,
    //  'pl' => 284761
    // ],

    // 'S008' => [
    //  'name' => '高雄SOGO門市部',
    //  'goal' => 168000,
    //  'pl' => 97600
    // ],
    // 'S014' => [
    //  'name' => '新光台中',
    //  'goal' => 415000,
    //  'pl' => 240950
    // ],
    // 'S017' => [
    //  'name' => '大統百貨',
    //  'goal' => 177000,
    //  'pl' => 137760
    // ],
    // 'S028' => [
    //  'name' => '台南新天地',
    //  'goal' => 323000,
    //  'pl' => 190960
    // ],
    // 'S051' => [
    //  'name' => '漢神巨蛋',
    //  'goal' => 320000,
    //  'pl' => 158600
    // ]
//   ];

// return [
        //     'S009' => [
        //         'name' => '大直門市部',
        //         'goal' => 641000,
        //         'pl' => 195000 
        //     ],
        //     'S013' => [
        //         'name' => '新光站前',
        //         'goal' => 2320000,
        //         'pl' => 1110960 
        //     ],
        //     'S049' => [
        //         'name' => '新光A8館',
        //         'goal' => 460000,
        //         'pl' => 337933 
        //     ],
        //     'S008' => [
        //         'name' => '高雄SOGO門市部',
        //         'goal' => 283000,
        //         'pl' => 164700 
        //     ],
        //     'S014' => [
        //         'name' => '新光台中',
        //         'goal' => 548000,
        //         'pl' => 318420 
        //     ],
        //     'S017' => [
        //         'name' => '大統百貨',
        //         'goal' => 130000,
        //         'pl' => 101680 
        //     ],
        //     'S028' => [
        //         'name' => '台南新天地',
        //         'goal' => 441000,
        //         'pl' => 260400 
        //     ],
        //     'S051' => [
        //         'name' => '漢神巨蛋',
        //         'goal' => 294000,
        //         'pl' => 145600
        //     ]
        // ];

C:\inetpub\wwwroot\C_lubri\lubri_dev\vendor\maatwebsite\excel\src\Maatwebsite\Excel\Readers\LaravelExcelReader.php

// if(!in_array('chunk', $this->filters['enabled']))
//     throw new \Exception("The chunk filter is not enabled, do so with ->filter('chunk')");

Laravel excel bug= =