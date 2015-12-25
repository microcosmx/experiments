(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file Inheritance.php and then reconvert to make any changes


function Adult() {

			

	 

	 

	 

	this.adultValue = function (){
		this.adultClassVar = 123;
		return this.adultClassVar;
	};


this.endOffset =   false;
this.adultClassVar = null;
this.isExtended =   false;
}


Adult.adultStaticVar = null;


function Child() {

	 

	 

	

	this.childValue = function (){

	//	self::$childStaticVar = 123;

		this.childInstanceVar = 123;

		// TODO - Adult static variable needs to be accessed via Adult.adultStaticVar not
		// Child.adultStaticVar in the generated javascript. Which may be tricky.

	//	echo "This is a child method. \n" + $this->childInstanceVar + " " + self::$childStaticVar + " " +  + $this->adultClassVar + " " + self::$adultStaticVar;

		return 12345;
	};


this.childInstanceVar = null;
Adult.call(this);

		this.isExtended = true;
	}



// inherit Adult
Child.prototype = new Adult();
// correct the constructor pointer because it points to Adult
Child.prototype.constructor = Child;
//Need to copy the static functions across and replace the parent class name with the child class name.
$.extend(Child, Adult);


Child.childStaticVar = null;



var adultOnly = new Adult();

assert(adultOnly.adultValue(), 123);
assert(adultOnly.isExtended, false);

var child = new Child();

assert(child.childValue(), 12345);
assert(child.adultValue(), 123);
assert(child.isExtended, true);


testEnd();

})();
