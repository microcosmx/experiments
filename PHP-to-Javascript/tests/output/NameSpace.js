(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file NameSpace.php and then reconvert to make any changes


/*namespace BaseReality*/

/*use Intahwebz\TestNamespace*/

function SomeTrait(){
	 
this.someVariable = null;
}



function TestClass(){

	

SomeTrait.call(this);
}



// inherit SomeTrait
TestClass.prototype = new SomeTrait();
// correct the constructor pointer because it points to SomeTrait
TestClass.prototype.constructor = TestClass;
//Need to copy the static functions across and replace the parent class name with the child class name.
$.extend(TestClass, SomeTrait);



function OtherClass() {


TestClass.call(this);
}



// inherit TestClass
OtherClass.prototype = new TestClass();
// correct the constructor pointer because it points to TestClass
OtherClass.prototype.constructor = OtherClass;
//Need to copy the static functions across and replace the parent class name with the child class name.
$.extend(OtherClass, TestClass);



//echo "Hello, This is in a namspace.";

//Todo - need some code that actually uses namespaces to test.
assert(1, 1);


testEnd();
})();
