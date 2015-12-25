(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file TryCatch.php and then reconvert to make any changes


var exceptionCaught = false;


function testFunction (value1){
	adddlert("Welcome guest " + value1 + " !");
}

var exceptionMessage = false;

try{
	var result = testFunction(5);
	alert( "Result is " + result + "");
}
catch(/*Error*/ e){
	exceptionCaught = true;
	//echo "Exception caught ".$e->getMessage();
	exceptionMessage = e.message;
}

assert(exceptionCaught, true);
assertGreater(strlen(exceptionMessage), 5);


var thrownExceptionCaught = false;
try{
	throw new Error("What is this?");
}
catch(/*Error*/ e){
	//echo "Exception caught ".$e->getMessage();
	thrownExceptionCaught = true;
}

assert(thrownExceptionCaught, true);

testEnd();

})();
