<?php
namespace Php2js\Transpilers\Expression\Operators\Comparison;

use Php2js\Transpilers\Expression\AbstractLeftRightTranspiler;

class EqualTranspiler extends AbstractLeftRightTranspiler
{
    /**
     * @return string
     */
    public function join($left, $right)
    {
        return "$left == $right";
    }
}
