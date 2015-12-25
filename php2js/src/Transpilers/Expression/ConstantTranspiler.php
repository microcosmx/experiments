<?php
namespace Php2js\Transpilers\Expression;

use Php2js\Exceptions\NotImplementedException;
use Php2js\Transpilers\AbstractTranspiler;

class ConstantTranspiler extends AbstractTranspiler
{
    /**
     * @return string
     */
    public function transpile()
    {
        switch ($this->node->name->parts[0]) {
            case 'true': return 'true';
            case 'false': return 'false';
            default:
                //return "";
                print "22222222222222\n" ;
                print(serialize($this->node->name));
                print "\n" ;
                print(serialize($this->node->value));
                print "\n" ;
                print(serialize($this->node->expr));
                print "\n33333333333333333\n" ;
                //return serialize($this->node->expr);
                return "";
                //throw new NotImplementedException($this->node->name->parts[0] . ' not implemented');
        }
    }
}
