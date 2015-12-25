(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 07:41:42 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file cosmo_dev.php and then reconvert to make any changes

//if(!empty($this->prefix)){
//}

// Lookup page in URL
var stmt = thisvar this.pdo.prepare('SELECT * FROM ' + "" + thisthis.prefix + "" + 'content WHERE url=?');
var data = {0 : url};
stmt.execute(data);
stmt.setFetchMode(PDO.FETCH_ASSOC);
var row = stmt.fetch();

// Make sure page exists and is published, or user is an administrator
if(row && (row['published'] === 'Y' || var admin))
{
    
} else if(row['published'] === 'N'){
    return false;
} else {
    
}
})();
