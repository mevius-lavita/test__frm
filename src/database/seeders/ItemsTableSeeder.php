<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        $items = [
            [
                'item_img' => 'Armani+Mens+Clock.jpg',
                'item_name' => '腕時計',
                'brand_name' => 'Rolex',
                'item_detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => '15000',
                'category_id' => 1,
                'condition' => $conditions[0],
            ],
            [
                'item_img' => 'HDD+Hard+Disk.jpg',
                'item_name' => 'HDD',
                'brand_name' => '西芝',
                'item_detail' => '高速で信頼性の高いハードディスク',
                'price' => '5000',
                'category_id' => 2,
                'condition' => $conditions[1],
            ],
            [
                'item_img' => 'iLoveIMG+d.jpg',
                'item_name' => '玉ねぎ3束',
                'brand_name' => 'なし',
                'item_detail' => '新鮮な玉ねぎ3束のセット',
                'price' => '300',
                'category_id' => 4,
                'condition' => $conditions[2],
            ],
            [
                'item_img' => 'Leather+Shoes+Product+Photo.jpg',
                'item_name' => '革靴',
                'brand_name' => '',
                'item_detail' => 'クラシックなデザインの革靴',
                'price' => '4000',
                'category_id' => 1,
                'condition' => $conditions[3],
            ],
            [
                'item_img' => 'Living+Room+Laptop.jpg',
                'item_name' => 'ノートPC',
                'brand_name' => '',
                'item_detail' => '高性能なノートパソコン',
                'price' => '45000',
                'category_id' => 2,
                'condition' => $conditions[0],
            ],
            [
                'item_img' => 'Music+Mic+4632231.jpg',
                'item_name' => 'マイク',
                'brand_name' => 'なし',
                'item_detail' => '高音質のレコーディング用マイク',
                'price' => '8000',
                'category_id' => 2,
                'condition' => $conditions[1],
            ],
            [
                'item_img' => 'Purse+fashion+pocket.jpg',
                'item_name' => 'ショルダーバッグ',
                'brand_name' => '',
                'item_detail' => 'おしゃれなショルダーバッグ',
                'price' => '3500',
                'category_id' => 1,
                'condition' => $conditions[2],
            ],
            [
                'item_img' => 'Tumbler+souvenir.jpg',
                'item_name' => 'タンブラー',
                'brand_name' => 'なし',
                'item_detail' => '使いやすいタンブラー',
                'price' => '3500',
                'category_id' => 3,
                'condition' => $conditions[3],
            ],
            [
                'item_img' => 'Waitress+with+Coffee+Grinder.jpg',
                'item_name' => 'コーヒーミル',
                'brand_name' => 'Starbacks',
                'item_detail' => '手動のコーヒーミル',
                'price' => '4000',
                'category_id' => 3,
                'condition' => $conditions[0],
            ],
            [
                'item_img' => '外出メイクアップセット.jpg',
                'item_name' => 'メイクセット',
                'brand_name' => '',
                'item_detail' => '便利なメイクアップセット',
                'price' => '2500',
                'category_id' => 1,
                'condition' => $conditions[1],
            ],
        ];

        DB::table('items')->insert($items);
    }
}
