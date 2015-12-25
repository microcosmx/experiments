(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file TypeHinting.php and then reconvert to make any changes



function sumArray (intArray){

	var total = 0;

	for (var valueKey in intArray) {		
                 var value = intArray[valueKey];
		total += value;
	}

	return total;
}

function test1 (/*Form*/ form){
	return 1;
}


function test2 (/*array*/ array){
	return sumArray(array);
}


var test1 = test1(null);
assert(test1, 1);



var testArray = {0 : 1, 1 : 2, 2 : 3};
var test2 = test2(testArray);
assert(test2, 6);

testEnd();

})();
