<?php

namespace App\Http\Controllers;

use Exception;

use Illuminate\Http\Request;
use DVDoug\BoxPacker\Packer;
use DVDoug\BoxPacker\ItemList;
use DVDoug\BoxPacker\VolumePacker;
use DVDoug\BoxPacker\Test\TestBox;  // use your own `Box` implementation
use DVDoug\BoxPacker\Test\TestItem; // use your own `Item` implementation

class PalletController extends Controller
{
    //
    private  $basic_data;

    function set_basic_data () {
        $this->basic_data = [
            'max_box' => [1000, 1000, 1000, 1000],
            'items' => [
                ['items_1',900, 800, 27, 67],
                ['items_2',100, 93, 65, 105],
                ['items_3',210, 93, 65, 105],
                ['items_4',140, 140, 65, 95],
                ['items_5',40, 48, 65, 95],
                ['items_6',40, 48, 65, 95],
                ['items_7',40, 48, 65, 95],
                ['items_8',40, 48, 65, 95],
                ['items_9',40, 48, 65, 95],
                ['items_10',40, 48, 65, 95],
                ['items_11',40, 48, 65, 95],
                ['items_12',26, 22, 55, 95],
                ['items_13',26, 22, 55, 95],
                ['items_14',150, 75, 27, 30]
            ]
        ];
    }

    function get_required_boxes ($max_box, $items) {
        $array = array();
        $packer = new Packer();

        $packer->addBox(new TestBox('max_box', $max_box[0], $max_box[1], $max_box[2], 0, $max_box[0], $max_box[1], $max_box[2], $max_box[3]));
        foreach ($items as $item) {
            $packer->addItem(new TestItem($item[0], $item[1], $item[2], $item[3], $item[4], true), 1);
        }

        
        try {
            $packed_pallets = $packer->pack();
            foreach ($packed_pallets as $packed_pallet) {
                $packed_box = $packed_pallet->getBox();
                array_push($array, array(
                "maxbox" => array(
                    "name" => $packed_box->getReference(),
                    "width" => $packed_box->getOuterWidth(),
                    "length" => $packed_box->getOuterLength(),
                    "depth" => $packed_box->getOuterDepth(),
                    "weight" => $packed_box->getMaxWeight()
                )));
                echo "This box is a {$packed_box->getReference()}, it is {$packed_box->getOuterWidth()}mm wide, {$packed_box->getOuterLength()}mm long and {$packed_box->getOuterDepth()}mm high" . PHP_EOL;
                echo "The combined weight of this box and the items inside it is {$packed_box->getMaxWeight()}g" . PHP_EOL;
                echo "The items in this box are:" . PHP_EOL;
                $packed_items = $packed_pallet->getItems();
                $arr = array();
                foreach ($packed_items as $packed_item) {
                    array_push($arr, array(
                        "name" => $packed_item->getItem()->getDescription(),
                        "width" => $packed_item->getWidth(),
                        "length" => $packed_item->getLength(),
                        "depth" => $packed_item->getDepth(),
                        "weight" => $packed_item->getItem()->getWeight(),
                        "X" => $packed_item->getX(),
                        "Y" => $packed_item->getY(),
                        "Z" => $packed_item->getZ(),
                    ));
                    echo  $packed_item->getItem()->getDescription() . PHP_EOL;
                    echo '(' . $packed_item->getX() . ', ' . $packed_item->getY() . ', ' . $packed_item->getZ() . ') with ';
                    echo 'l' . $packed_item->getLength() . ', w' . $packed_item->getWidth() . ', d' . $packed_item->getDepth();
                    echo PHP_EOL;
                }
                array_push($array, array("items" => $arr));
                dd($array);
            }
            return $packed_pallets;
        } catch (Exception $e   ) {
            dd($e);
        }

    }

    function index() {
        $this->set_basic_data();
        $this -> get_required_boxes($this->basic_data['max_box'], $this->basic_data['items']);
        return view('pallet');
    }
}
