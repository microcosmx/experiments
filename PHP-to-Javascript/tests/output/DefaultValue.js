(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file DefaultValue.php and then reconvert to make any changes



function getTotal (value1, value2){
		if(typeof value2 === "undefined"){
			value2 =   5;
		}

	return value1 + value2;
}

var mathTotal = getTotal(5);

assert(mathTotal, 10);

testEnd();
})();
