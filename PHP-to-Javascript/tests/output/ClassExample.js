(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file ClassExample.php and then reconvert to make any changes


function ClassExample(initialValue) {


			
	 	

	  	

	


	this.testStatic = function (){
		var currentValue = ClassExample.staticVar;
		ClassExample.staticVar++;
		return currentValue;
	};



	this.addValue = function (value){
		this.value += value;
	};



	this.getArrayValue = function (){
		var result = 0;

		for (var testValueKey in this.testArray) {		
                 var testValue = this.testArray[testValueKey];
			result += testValue;
		}

		return result;
	};



	this.getArrayValueWithIndex = function (){
		var result = 0;

		for (var key in this.testArray) {
       var testValue = this.testArray[key];
			result += testValue;
		}

		return result;
	};



	 function privateFunction (){
		return 5;
	}

	 this.publicAccess = function (){
		return privateFunction();
	};



	 this.testFunctionStatic = function (){
		 if (typeof this.testFunctionStatic.countUnique == 'undefined')
 this.testFunctionStatic.countUnique = 0;
		this.testFunctionStatic.countUnique++;

		return this.testFunctionStatic.countUnique;
	};




this.value =   null;
this.testArray =   null;

		this.value = initialValue;

		this.testArray = {};
		this.testArray[0] = 1;
		this.testArray[1] = 2;
		this.testArray[2] = 3;
	}


ClassExample.staticVar =   0;









var classExample = new ClassExample(5);

classExample.addValue(5);

assert(classExample.value, 10);

assert(classExample.getArrayValue(), 6);

assert(classExample.getArrayValueWithIndex(), 6);


classExample.testStatic();
classExample.testStatic();
var result = classExample.testStatic();


//Called two times, but value is only incremented twice
assert(result, 2);




var privateAccessed = false;
var exceptionCaught = false;
try{
	//Yes IDE - I know this isn't allowed.
	///** @noinspection PhpIllegalArrayKeyTypeInspection */

	// @SuppressWarnings
	classExample.privateFunction();
	privateAccessed = true;
}
catch(/*Error*/ error){
	//This correct - the private function should not be callable.
	exceptionCaught = true;
}

assert(privateAccessed, false);
assert(exceptionCaught, true);

var value = classExample.publicAccess();
assert(value, 5);


classExample.testFunctionStatic();
classExample.testFunctionStatic();
classExample.testFunctionStatic();
value = classExample.testFunctionStatic();

assert(value, 4);

testEnd();

})();
