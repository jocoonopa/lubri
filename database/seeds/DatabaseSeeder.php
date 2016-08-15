<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

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
                'id'           => 1,
                'name'         => 'product',
                'hname'        => '商品',
                'depend_on_id' => 1
            ],
            [
                'id'           => 2,
                'name'         => 'member',
                'hname'        => '會員',
                'depend_on_id' => 2
            ],
            [
                'id'           => 3,
                'name'         => 'order',
                'hname'        => '訂單',
                'depend_on_id' => 2
            ],
            [
                'id'           => 4,
                'name'         => 'campaign',
                'hname'        => '活動',
                'depend_on_id' => 4
            ],
            [
                'id'           => 5,
                'name'         => 'list',
                'hname'        => '瑛聲名單',
                'depend_on_id' => 2
            ],
            [
                'id'           => 6,
                'name'         => 'calllog',
                'hname'        => '通話紀錄',
                'depend_on_id' => 5
            ],
        ];

        foreach ($data as $config) {
            $id = array_get($config, 'id');
            $type = App\Model\Log\FVSyncType::find($id);
            
            if (null !== $type) {    
                unset($config['id']);           
                
                DB::table('fvsynctype')->where('id', '=', $id)->update($config);
            } else {
                DB::table('fvsynctype')->insert([$config]);
            }
        }   
    }
}