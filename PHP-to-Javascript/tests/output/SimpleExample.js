(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file SimpleExample.php and then reconvert to make any changes


var target = "world";

var greeting = "Hello " + target + "!";

assert(greeting, "Hello world!");

//****************************************

var total = 0;

for (var i=1 ; i<=5 ; i++){
	total += i;
}

assert(total, 15);

//****************************************

var value = '123';
var delta = 123;
value = +value + delta;
assert(value, 246);


var result = str_pad(value, 6, '0', 'STR_PAD_LEFT');
assert(result, '000246');

//****************************************

var testArray = {
	0 : 1, 1 : 2, 2 : 3
};

function testFunction (testArray){
	result = 0;

	for (var testKey in testArray) {		
                 var test = testArray[testKey];
		result += test;
	}

	return result;
}

value = testFunction(testArray);
assert(value, 6);

//****************************************
var globalVar1 = 1;

function testGlobal (){
    
    var localVar = 2;
    return globalVar1 + localVar;
};

assert(testGlobal(), 3);

testEnd();

})();
