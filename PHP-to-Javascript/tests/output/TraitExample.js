(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file TraitExample.php  and then reconvert to make any changes



function JSONFactory(){

	 

	this.toJSON = function (){

		var className = get_class(this);

		return json_encode_object(this, className);
	};


}



JSONFactory.factory = function (jsonString){
		var data = json_decode(jsonString);

		var object = new this.prototype.constructor();

		for (var key in data) {
       var value = data[key];
			object[key] = value;
		}

		return object;
	}

testEnd();


// Opening the require'TraitInclude.php';

//require_once('functions.php');



function ExampleJSON(objectID, name, value){

	

	 		
	 		
	 		

	

	this.test = function (){
		return "name = " + "" + this.name + "" + " value = " + "" + this.value;
	};


this.objectID = null;
this.name = null;
this.value = null;
JSONFactory.call(this);

		if(typeof objectID === "undefined"){
			objectID =   false;
		}

		if(typeof name === "undefined"){
			name =   'UnknownName';
		}

		if(typeof value === "undefined"){
			value =   "UnknownValue";
		}

		this.objectID = objectID;
		this.name 	= name;
		this.value 	= value;
	}



// inherit JSONFactory
ExampleJSON.prototype = new JSONFactory();
// correct the constructor pointer because it points to JSONFactory
ExampleJSON.prototype.constructor = ExampleJSON;
//Need to copy the static functions across and replace the parent class name with the child class name.
$.extend(ExampleJSON, JSONFactory);





var testObject = new ExampleJSON(1, "First", "Testing");

var json = testObject.toJSON();

var duplicate = ExampleJSON.factory(json);

assert(duplicate.name == "First", true);
assert(duplicate.value == "Testing", true);

testEnd();

})();
