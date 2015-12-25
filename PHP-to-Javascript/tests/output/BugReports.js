(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file BugReports.php and then reconvert to make any changes






//Sent via email
var test = {0 : 0};
var test2 = {0 : test[0]};
var var3 = {'name' : 'gordon'};




var x =  {0 : var3['name'  ]};


//Sent via email
// define( 1);
// define( 5);
// define( 0.5);
// define( );

assert(true, true);
assert(5 * 2, 10);
assert(0.5 + 1, 1.5);
assert(null, null);




//Sent via email
var boolTrue = true;
var boolFalse = !boolTrue;
assert(boolFalse, false);



function InstanceTest(){}



var instanceTest = new InstanceTest();

var correctInstance = false;

if (instanceTest instanceof InstanceTest){
    correctInstance = true;
}

assert(correctInstance, true);





//https://github.com/Danack/PHP-to-Javascript/issues/33

function ClassWithPrivate() {
     var foo = 4;

    

     this.accessPrivate = function (){
        return foo;
    };



        //$this->foo += 2;
    }





var classWithPrivate = new ClassWithPrivate();
classWithPrivate.accessPrivate();


assert(classWithPrivate.accessPrivate(), 4);



//https://github.com/Danack/PHP-to-Javascript/issues/39

function ClassWithPrivateProperty(){

     var foo = 0;

    
    this.setFoo = function (newFoo){
        foo = newFoo;
    };



    this.getFoo = function (){
        return foo;
    };



        this.setFoo(5);
    }






var classWithPrivateProperty = new ClassWithPrivateProperty();

assert(classWithPrivateProperty.getFoo(), 5);



//https://github.com/Danack/PHP-to-Javascript/issues/35

function Person() {

     

    this.addSkill = function (skillLevel) {
        this.skillLevel += skillLevel;
    };


this.skillLevel =   0;
}




var person1 = new Person();
person1.addSkill(5);

var person2 = new Person();
person2.addSkill(6);

assert(person1.skillLevel, 5);
assert(person2.skillLevel, 6);










// https://github.com/Danack/PHP-to-Javascript/issues/31

function Class1(){
      
     
this.foo =   "foo";
}


Class1.instance = null;

function Class2(){
    

        Class1.instance.foo = 'foo';
    }




//https://github.com/Danack/PHP-to-Javascript/issues/45


//class ParentClass {
//    public $wth = "foo";
//
//
//    public function foo() {
//        return "parent";
//    }
//}
//
//class ChildClass extends ParentClass {
//    
//    static public $wth2 = "hmm";
//    
//    function __construct() {
//        echo parent::foo();
//        echo self::$wth2;
//    }
//}


//$test = new ChildClass();


function TestClass(message) {
 
     
    
    
    
    this.windowCloseFunction = function () {
        alert( "Goodbye " + "" + this.message);
    };



//    //If you have jQuery 
//     window.onresize = $.proxy(this, 'windowCloseFunction');
    
    //Or with standard Javascript
//     this.makeWindowCloseFunction = function($context, $functionName) {
//        return function() {
//            $functionName.call($context);
//        }
//    }
//    
//     window.onresize = this.makeWindowCloseFunction(this, this.windowCloseFunction);
this.message = null;

        this.message = message;
    }





test = new TestClass("cruel world!");






//https://github.com/Danack/PHP-to-Javascript/issues/44
//Embedded variables

var value1 = 5;

var value2 = "Hello "  + "" +  value1  + "" +  " there";
assert(value2, "Hello 5 there");

var value3 = "Hello " + value1 + " there";
assert(value3, "Hello 5 there");

function greet (name) {
    return "Hello " + name + "!";
}

assert(greet("Bob"), "Hello Bob!");


//https://github.com/Danack/PHP-to-Javascript/issues/14
//Modulus doesn't work

var p2 = 8;
var step = 6;

p2 -= (p2 % step);

//assert($p2, 6);

//*************************************************************
//*************************************************************

//https://github.com/Danack/PHP-to-Javascript/issues/15
var countValue = 4;
countValue--;

//assert($countValue, 3);

countValue--;

//assert($countValue, 2);


//https://github.com/Danack/PHP-to-Javascript/issues/48



function add (value1, value2) {
		if(typeof value2 === "undefined"){
			value2 =   -1;
		}

    return value1 + value2;
}


//assert(add(5, 5), 10);

//assert(add(5), 4);



testEnd();



})();
