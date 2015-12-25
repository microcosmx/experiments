(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:39:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file continue.php and then reconvert to make any changes



var total = 0;

for(var i=0;i<10;i++){

    if ((i%2) == 0) {
        continue;
    }

    total++;
}


assert(total, 5);

testEnd();
})();
