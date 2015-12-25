(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file PublicPrivate.php and then reconvert to make any changes


function SimplePrivate() {
     var foo = 4;

     this.getFoo = function (){
        return foo;
    };


}




var f = new SimplePrivate();

var value = f.getFoo();

assert(value, 4);

testEnd();

})();
