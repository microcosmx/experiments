<?php
//if(!empty($this->prefix)){
//}

class Cosmo {

    private $pdo;
    private $prefix;
    private $salt;

	public function contentRead($url=NULL, $admin=NULL){
        // Lookup page in URL
        $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'content WHERE url=?');
        $data = array($url);
        $stmt->execute($data);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();

        // Make sure page exists and is published, or user is an administrator
        if($row && ($row['published'] === 'Y' || $admin))
        {
            // Get extras
            //$extras = self::contentExtrasRead($row['id']);
            //$tags = self::contentTagsRead($row['id']);
            if($row['url'] === '/')
                $url = '/';
            else
                $url = substr($row['url'], 1); // Remove first slash '/' to make the URL relative for sites in subfolders

            return array(
                'id' => $row['id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'header' => $row['header'],
                'subheader' => $row['subheader'],
                'featured' => $row['featured'],
                'body' => $row['body'],
                'url' => $url,
                'published' => $row['published'],
                'published_date' => $row['published_date'],
                'tags' => $tags,
                'type' => $row['type'],
                //'author' => self::usersRead($row['author']),
                'timestamp' => $row['timestamp'],
                'extras' => $extras
            );
        } else if($row['published'] === 'N'){
            return FALSE;
        } else {
            // See if URL changed, if so, redirect the user to the new page
            $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'revisions WHERE url=? LIMIT 1');
            $data = array($url);
            $stmt->execute($data);
            if($row = $stmt->fetch())
            {
                // Grab new URL
                $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'content WHERE id=?');
                $data = array($row['content_id']);
                $stmt->execute($data);
                if($row = $stmt->fetch()) // Updated the URL
                    return array('redirect' => $row['url']);
                else // Deleted the page
                    return FALSE;
            } else
                return FALSE;
        }
    }

    public function contentExtrasRead($contentID){
        $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'content_extras WHERE content_id=?');
        $data = array($contentID);
        $stmt->execute($data);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $extras = array();

        while($row = $stmt->fetch())
            $extras[$row['name']] = $row['extra'];

        return $extras;
    }

    public function contentTagsRead($contentID, $tagStartsWith = ''){
        $tags = array();
        if($contentID)
        {
            $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'content_tags WHERE content_id=?');
            $data = array($contentID);
            $stmt->execute($data);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($row = $stmt->fetch())
                $tags[] = $row['tag'];
        } else
        {
            $stmt = $this->pdo->prepare('SELECT DISTINCT tag FROM '.$this->prefix.'content_tags WHERE tag LIKE ?');
            $data = array($tagStartsWith . '%');
            $stmt->execute($data);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($row = $stmt->fetch())
                $tags[] = $row['tag'];
        }

        return $tags;
    }


    public function usersRead($usersID=NULL, $keyword=NULL)
    {
        if($usersID) // Get a specific user's info
        {
            $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'users WHERE id=?');
            $stmt->execute(array($usersID));
            $row = $stmt->fetch();
            $users = array(
                'id' => $row['id'],
                'username'=>$row['username'],
                'name'=>$row['name'],
                'photo'=>$row['photo'],
                'bio'=>$row['bio'],
                'facebook'=>$row['facebook'],
                'twitter'=>$row['twitter'],
                'role'=>$row['role'],
                'email'=>$row['email']
            );
        } else if($keyword) // Get users similar to a keyword
        {
            $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'users WHERE username LIKE ? OR email LIKE ? LIMIT 250');
            $data = array('%' . $keyword . '%', '%' . $keyword . '%');
            $stmt->execute($data);
            $users = array();
            while($row = $stmt->fetch())
                $users[] = array(
                    'id'=>$row['id'],
                    'username'=>$row['username'],
                    'name'=>$row['name'],
                    'email'=>$row['email'],
                    'role'=>$row['role']
                );
        } else // Get all users
        {
            $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'users');
            $stmt->execute();
            $users = array();
            while($row = $stmt->fetch())
                $users[] = array(
                    'id' => $row['id'],
                    'username'=>$row['username'],
                    'name'=>$row['name'],
                    'photo'=>$row['photo'],
                    'facebook'=>$row['facebook'],
                    'twitter'=>$row['twitter'],
                    'role'=>$row['role'],
                    'email'=>$row['email']
                );
        }

        return $users;
    }



}

?>
