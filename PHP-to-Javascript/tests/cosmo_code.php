<?php
if($url)
{
    // Remove the prefix from the URL
    if(!empty($this->prefix)){
        $prefix = substr($this->prefix, 0, strlen($this->prefix)-1); // Remove trailing slash '/'
        $url = str_replace ($prefix, '', $url);
    }

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
        $extras = self::contentExtrasRead($row['id']);
        $tags = self::contentTagsRead($row['id']);
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
            'author' => self::usersRead($row['author']),
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
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($row = $stmt->fetch())
        {
            // Grab new URL
            $stmt = $this->pdo->prepare('SELECT * FROM '.$this->prefix.'content WHERE id=?');
            $data = array($row['content_id']);
            $stmt->execute($data);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($row = $stmt->fetch()) // Updated the URL
                return array('redirect' => $row['url']);
            else // Deleted the page
                return FALSE;
        } else
            return FALSE;
    }
} else // List all pages except the home page and new page
{
    $stmt = $this->pdo->prepare('SELECT id, title, description, header, subheader, featured, url, type, published, published_date, author, timestamp FROM '.$this->prefix.'content WHERE url!=? AND url!=? ORDER BY published_date DESC');
    $data = array('/', '/new');
    $stmt->execute($data);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $i = 0;
    while($row = $stmt->fetch()){
        $results[$i] = $row;
        if($row['url'] === '/')
            $url = '/';
        else
            $url = substr($row['url'], 1); // Remove first slash '/' to make the URL relative for sites in subfolders

        $results[$i]['url'] = $url;
        $results[$i]['tags'] = self::contentTagsRead($row['id']);
        $results[$i]['author'] = self::usersRead($row['author']);
        $i++;
    }

    return $results;
}



testEnd();
?>