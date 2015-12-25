(function(){
//Auto-generated file by PHP-To-Javascript at Fri, 25 Dec 15 08:15:25 +0000
//
//DO NOT EDIT - all changes will be lost.
//
//Please edit the file cosmo_dev.php and then reconvert to make any changes

//if(!empty($this->prefix)){
//}

function Cosmo() {

     var pdo;
     var prefix;
     var salt;

	 this.contentRead = function (url, admin){
		if(typeof url === "undefined"){
			url = null;
		}

		if(typeof admin === "undefined"){
			admin = null;
		}

        // Lookup page in URL
        var stmt = pdo.prepare('SELECT * FROM ' + "" + prefix + "" + 'content WHERE url=?');
        var data = {0 : url};
        stmt.execute(data);
        stmt.setFetchMode(PDO.FETCH_ASSOC);
        var row = stmt.fetch();

        // Make sure page exists and is published, or user is an administrator
        if(row && (row['published'] === 'Y' || admin))
        {
            // Get extras
            //$extras = self::contentExtrasRead($row['id']);
            //$tags = self::contentTagsRead($row['id']);
            if(row['url'] === '/')
                url = '/';
            else
                url = substr(row['url'], 1); // Remove first slash '/' to make the URL relative for sites in subfolders

            return {
                'id' : row['id'],
                'title' : row['title'],
                'description' : row['description'],
                'header' : row['header'],
                'subheader' : row['subheader'],
                'featured' : row['featured'],
                'body' : row['body'],
                'url' : url,
                'published' : row['published'],
                'published_date' : row['published_date'],
                'tags' : tags,
                'type' : row['type'],
                //'author' => self::usersRead($row['author']),
                'timestamp' : row['timestamp'],
                'extras' : extras
            };
        } else if(row['published'] === 'N'){
            return false;
        } else {
            // See if URL changed, if so, redirect the user to the new page
            stmt = pdo.prepare('SELECT * FROM ' + "" + prefix + "" + 'revisions WHERE url=? LIMIT 1');
            data = {0 : url};
            stmt.execute(data);
            if(row = stmt.fetch())
            {
                // Grab new URL
                stmt = pdo.prepare('SELECT * FROM ' + "" + prefix + "" + 'content WHERE id=?');
                data = {0 : row['content_id']};
                stmt.execute(data);
                if(row = stmt.fetch()) // Updated the URL
                    return {'redirect' : row['url']};
                else // Deleted the page
                    return false;
            } else
                return false;
        }
    };



     this.contentExtrasRead = function (contentID){
        var stmt = pdo.prepare('SELECT * FROM ' + "" + prefix + "" + 'content_extras WHERE content_id=?');
        var data = {0 : contentID};
        stmt.execute(data);
        stmt.setFetchMode(PDO.FETCH_ASSOC);
        var extras = {};

        while(var row = stmt.fetch())
            extras[row['name']] = row['extra'];

        return extras;
    };



     this.contentTagsRead = function (contentID, tagStartsWith){
		if(typeof tagStartsWith === "undefined"){
			tagStartsWith =   '';
		}

        var tags = {};
        if(contentID)
        {
            var stmt = pdo.prepare('SELECT * FROM ' + "" + prefix + "" + 'content_tags WHERE content_id=?');
            var data = {0 : contentID};
            stmt.execute(data);
            stmt.setFetchMode(PDO.FETCH_ASSOC);
            while(var row = stmt.fetch())
                tags[] = row['tag'];
        } else
        {
            stmt = pdo.prepare('SELECT DISTINCT tag FROM ' + "" + prefix + "" + 'content_tags WHERE tag LIKE ?');
            data = {0 : tagStartsWith  + "" +  '%'};
            stmt.execute(data);
            stmt.setFetchMode(PDO.FETCH_ASSOC);
            while(row = stmt.fetch())
                tags[] = row['tag'];
        }

        return tags;
    };




     this.usersRead = function (usersID, keyword)
    {
		if(typeof usersID === "undefined"){
			usersID = null;
		}

		if(typeof keyword === "undefined"){
			keyword = null;
		}

        if(usersID) // Get a specific user's info
        {
            var stmt = pdo.prepare('SELECT * FROM ' + "" + prefix + "" + 'users WHERE id=?');
            stmt.execute({0 : usersID});
            var row = stmt.fetch();
            var users = {
                'id' : row['id'],
                'username':row['username'],
                'name':row['name'],
                'photo':row['photo'],
                'bio':row['bio'],
                'facebook':row['facebook'],
                'twitter':row['twitter'],
                'role':row['role'],
                'email':row['email']
            };
        } else if(keyword) // Get users similar to a keyword
        {
            stmt = pdo.prepare('SELECT * FROM ' + "" + prefix + "" + 'users WHERE username LIKE ? OR email LIKE ? LIMIT 250');
            var data = {0 : '%'  + "" +  keyword  + "" +  '%', 1 : '%'  + "" +  keyword  + "" +  '%'};
            stmt.execute(data);
            users = {};
            while(row = stmt.fetch())
                users[] = {
                    'id':row['id'],
                    'username':row['username'],
                    'name':row['name'],
                    'email':row['email'],
                    'role':row['role']
                };
        } else // Get all users
        {
            stmt = pdo.prepare('SELECT * FROM ' + "" + prefix + "" + 'users');
            stmt.execute();
            users = {};
            while(row = stmt.fetch())
                users[] = {
                    'id' : row['id'],
                    'username':row['username'],
                    'name':row['name'],
                    'photo':row['photo'],
                    'facebook':row['facebook'],
                    'twitter':row['twitter'],
                    'role':row['role'],
                    'email':row['email']
                };
        }

        return users;
    };





}







})();
