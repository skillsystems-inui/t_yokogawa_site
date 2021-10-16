<?php
namespace Customize\Twig\Extension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Picqer\Barcode\BarcodeGeneratorHTML;

class EccubeExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getbarcode', [$this, 'getBarcode']),
        ];
    }
    
    public function getBarcode($code){
        //会員バーコード
		$generator = new BarcodeGeneratorHTML();
		$target_barcode = strval($code);
		$customer_barcode = $generator->getBarcode($target_barcode, $generator::TYPE_CODE_128, 2, 50, 'black');
		
        echo $customer_barcode;
    }
    
}