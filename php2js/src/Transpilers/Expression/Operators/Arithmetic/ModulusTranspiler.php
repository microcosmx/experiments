<?php
namespace Php2js\Transpilers\Expression\Operators\Arithmetic;

class ModulusTranspiler extends AbstractHighPrecedenceArithmeticTranspiler
{
    /**
     * @return string
     */
    public function join($left, $right)
    {
        return "$left % $right";
    }
}
