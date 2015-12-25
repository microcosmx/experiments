(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file AssigningThis.php and then reconvert to make any changes


function paramTest (object, otherVar){
	return object;
}


function TestClass(){

	 

	this.getThis = function (){
		return this;
	};



	this.getThis2 = function (){
		return paramTest(this, 'ignored var');
	};



	this.getThis3 = function (){
		var returnValue = this;
		return returnValue;
	};



	this.getClassName = function (){
		var className = get_class(this);
		return className;
	};



	this.getValue = function (){
		return this.five;
	};




this.five =   5;
}








var testClass = new TestClass();

assert(testClass.getThis(), testClass);
assert(testClass.getThis2(), testClass);
assert(testClass.getThis3(), testClass);
assert(testClass.getClassName(), 'TestClass');
assert(testClass.getValue(), 5);

testEnd();

})();
