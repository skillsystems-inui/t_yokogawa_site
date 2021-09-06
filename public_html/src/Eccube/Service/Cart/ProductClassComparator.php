<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\Cart;

use Eccube\Entity\CartItem;

/**
 * 商品規格で明細を比較するCartItemComparator
 */
class ProductClassComparator implements CartItemComparator
{
    /**
     * @param CartItem $Item1 明細1
     * @param CartItem $Item2 明細2
     *
     * @return boolean 同じ明細になる場合はtrue
     */
    public function compare(CartItem $Item1, CartItem $Item2)
    {
        $ProductClass1 = $Item1->getProductClass();
        $product_class_id1 = $ProductClass1 ? (string) $ProductClass1->getId() : null;
        //オプション
        // opt1
        $ProductOpt1_01 = $Item1->getClassCategory1();
        $product_option_id1_01 = $ProductOpt1_01 ? (string) $ProductOpt1_01->getId() : null;
        // opt2
        $ProductOpt1_02 = $Item1->getClassCategory2();
        $product_option_id1_02 = $ProductOpt1_02 ? (string) $ProductOpt1_02->getId() : null;
        // opt3
        $ProductOpt1_03 = $Item1->getClassCategory3();
        $product_option_id1_03 = $ProductOpt1_03 ? (string) $ProductOpt1_03->getId() : null;
        // opt4
        $ProductOpt1_04 = $Item1->getClassCategory4();
        $product_option_id1_04 = $ProductOpt1_04 ? (string) $ProductOpt1_04->getId() : null;
        // opt5
        $ProductOpt1_05 = $Item1->getClassCategory5();
        $product_option_id1_05 = $ProductOpt1_05 ? (string) $ProductOpt1_05->getId() : null;
        // opt6
        $ProductOpt1_06 = $Item1->getClassCategory6();
        $product_option_id1_06 = $ProductOpt1_06 ? (string) $ProductOpt1_06->getId() : null;
        // opt7
        $ProductOpt1_07 = $Item1->getClassCategory7();
        $product_option_id1_07 = $ProductOpt1_07 ? (string) $ProductOpt1_07->getId() : null;
        // opt8
        $ProductOpt1_08 = $Item1->getClassCategory8();
        $product_option_id1_08 = $ProductOpt1_08 ? (string) $ProductOpt1_08->getId() : null;
        // opt9
        $ProductOpt1_09 = $Item1->getClassCategory9();
        $product_option_id1_09 = $ProductOpt1_09 ? (string) $ProductOpt1_09->getId() : null;
        // opt10
        $ProductOpt1_10 = $Item1->getClassCategory10();
        $product_option_id1_10 = $ProductOpt1_10 ? (string) $ProductOpt1_10->getId() : null;
        // プレートメッセ―ジ
        $ProductMsg1_plate = $Item1->getPrintnamePlate();
        $product_msg_id1_plate = trim($ProductMsg1_plate);
        // のしメッセ―ジ
        $ProductMsg1_noshi = $Item1->getPrintnameNoshi();
        $product_msg_id1_noshi = trim($ProductMsg1_noshi);
        
        $ProductClass2 = $Item2->getProductClass();
        $product_class_id2 = $ProductClass2 ? (string) $ProductClass2->getId() : null;
        //オプション
        // opt1
        $ProductOpt2_01 = $Item2->getClassCategory1();
        $product_option_id2_01 = $ProductOpt2_01 ? (string) $ProductOpt2_01->getId() : null;
        // opt2
        $ProductOpt2_02 = $Item2->getClassCategory2();
        $product_option_id2_02 = $ProductOpt2_02 ? (string) $ProductOpt2_02->getId() : null;
        // opt3
        $ProductOpt2_03 = $Item2->getClassCategory3();
        $product_option_id2_03 = $ProductOpt2_03 ? (string) $ProductOpt2_03->getId() : null;
        // opt4
        $ProductOpt2_04 = $Item2->getClassCategory4();
        $product_option_id2_04 = $ProductOpt2_04 ? (string) $ProductOpt2_04->getId() : null;
        // opt5
        $ProductOpt2_05 = $Item2->getClassCategory5();
        $product_option_id2_05 = $ProductOpt2_05 ? (string) $ProductOpt2_05->getId() : null;
        // opt6
        $ProductOpt2_06 = $Item2->getClassCategory6();
        $product_option_id2_06 = $ProductOpt2_06 ? (string) $ProductOpt2_06->getId() : null;
        // opt7
        $ProductOpt2_07 = $Item2->getClassCategory7();
        $product_option_id2_07 = $ProductOpt2_07 ? (string) $ProductOpt2_07->getId() : null;
        // opt8
        $ProductOpt2_08 = $Item2->getClassCategory8();
        $product_option_id2_08 = $ProductOpt2_08 ? (string) $ProductOpt2_08->getId() : null;
        // opt9
        $ProductOpt2_09 = $Item2->getClassCategory9();
        $product_option_id2_09 = $ProductOpt2_09 ? (string) $ProductOpt2_09->getId() : null;
        // opt10
        $ProductOpt2_10 = $Item2->getClassCategory10();
        $product_option_id2_10 = $ProductOpt2_10 ? (string) $ProductOpt2_10->getId() : null;
        // プレートメッセ―ジ
        $ProductMsg2_plate = $Item2->getPrintnamePlate();
        $product_msg_id2_plate = trim($ProductMsg2_plate);
        // のしメッセ―ジ
        $ProductMsg2_noshi = $Item2->getPrintnameNoshi();
        $product_msg_id2_noshi = trim($ProductMsg2_noshi);
        
        //規格とオプション1～10すべて同じならtrue、一致しないならfalse
        if(($product_class_id1 == $product_class_id2) &&
           ($product_option_id1_01 == $product_option_id2_01) &&
           ($product_option_id1_02 == $product_option_id2_02) &&
           ($product_option_id1_03 == $product_option_id2_03) &&
           ($product_option_id1_04 == $product_option_id2_04) &&
           ($product_option_id1_05 == $product_option_id2_05) &&
           ($product_option_id1_06 == $product_option_id2_06) &&
           ($product_option_id1_07 == $product_option_id2_07) &&
           ($product_option_id1_08 == $product_option_id2_08) &&
           ($product_option_id1_09 == $product_option_id2_09) &&
           ($product_option_id1_10 == $product_option_id2_10) &&
           ($product_msg_id1_plate == $product_msg_id2_plate) &&
           ($product_msg_id1_noshi == $product_msg_id2_noshi) 
        ){
        	return true;
        }
        
        return false;
    }
}
