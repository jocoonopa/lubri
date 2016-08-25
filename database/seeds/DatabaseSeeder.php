<?php

use App\Model\Log\FVSyncType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FVQueTypeTableSeeder::class);
    }
}

class FVQueTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id'           => FVSyncType::ID_PRODUCT,
                'name'         => 'product',
                'hname'        => '商品',
                'depend_on_id' => FVSyncType::ID_PRODUCT,
                'viga_type'    => FVSyncType::VIGATYPE_PRODUCT
            ],
            [
                'id'           => FVSyncType::ID_MEMBER,
                'name'         => 'member',
                'hname'        => '會員',
                'depend_on_id' => FVSyncType::ID_MEMBER,
                'viga_type'    => FVSyncType::VIGATYPE_MEMBER
            ],
            [
                'id'           => FVSyncType::ID_ORDER,
                'name'         => 'order',
                'hname'        => '訂單',
                'depend_on_id' => FVSyncType::ID_MEMBER,
                'viga_type'    => FVSyncType::VIGATYPE_ORDER
            ],
            [
                'id'           => FVSyncType::ID_CAMPAIGN,
                'name'         => 'campaign',
                'hname'        => '活動',
                'depend_on_id' => FVSyncType::ID_CAMPAIGN,
                'viga_type'    => 'CHCampaignSync'
            ],
            [
                'id'           => FVSyncType::ID_LIST,
                'name'         => 'list',
                'hname'        => '瑛聲名單',
                'depend_on_id' => FVSyncType::ID_MEMBER,
                'viga_type'    => FVSyncType::VIGATYPE_LIST
            ],
            [
                'id'           => FVSyncType::ID_CALLLOG,
                'name'         => 'calllog',
                'hname'        => '通話紀錄',
                'depend_on_id' => FVSyncType::ID_LIST,
                'viga_type'    => FVSyncType::VIGATYPE_CALLLOG
            ],
        ];

        foreach ($data as $config) {
            $id = array_get($config, 'id');
            $type = FVSyncType::find($id);
            
            if (null !== $type) {    
                unset($config['id']);           
                
                DB::table('fvsynctype')->where('id', '=', $id)->update($config);
            } else {
                DB::table('fvsynctype')->insert([$config]);
            }
        }   
    }
}