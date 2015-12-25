(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file Closure.php and then reconvert to make any changes



var closureFunction = function (){
    return 5;
};
    
assert(closureFunction(), 5);

//********************

var closureFunctionWithParam = function (x){
    return x + 5;
};


assert(closureFunctionWithParam(3), 8);

//********************

var closureFunctionWithMultipleParam = function (x, y, z){
    return x + y + z;
};

assert(closureFunctionWithParam(1, 2, 3), 6);


//********************


function testLocalClosure (y) {
 
    var localFunction = function (x) {
        
        return x + 2;
    };
    
    return localFunction(y);
}


assert(testLocalClosure(2), 4);


testEnd();



})();
