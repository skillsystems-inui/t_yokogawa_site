<?php
namespace Customize\Twig\Extension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EccubeExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('exececho', [$this, 'getExececho']),
        ];
    }
    
    public function getExececho($target){
        echo $target;
    }
    
}