(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file CustomEvent.php and then reconvert to make any changes



/**
 * Simple class that defines
 */
function CustomEvent(){

	 

	 var privateValue = 12345;
	 

	  
	  
	  

	  

	  

	  

	  
	  
	  
	  
	  
	  
this.nullValue =   null;
this.classValue =   12345;
}


CustomEvent.stringValue =   "basereality.previousPage";
CustomEvent.intValue =   12345;
CustomEvent.hexValue =   0xa12345;
CustomEvent.noDefaultValue = null;
CustomEvent.valueAfterComment =   /* Seriously? */ 0xa12345;
CustomEvent.valueCommentNewLine =   // You're just trying to break it now.
		12345;
CustomEvent.previewContent =    'basereality.previewContent';
CustomEvent.closePreview =    'basereality.closePreview';
CustomEvent.nextPage =   	'basereality.nextPage';
CustomEvent.previousPage =    'basereality.previousPage';
CustomEvent.firstPage =    "basereality.firstPage";
CustomEvent.lastPage =    "basereality.lastPage";

assert(CustomEvent.nextPage, 'basereality.nextPage');

assert(CustomEvent.valueAfterComment, 10560325);


var test = function (){
    assert(CustomEvent.nextPage, 'basereality.nextPage');
    assert(CustomEvent.valueAfterComment, 10560325);

    return CustomEvent.valueCommentNewLine;
};

var result = test();

assert(12345, result);


testEnd();
})();
