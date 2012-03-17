<?php
 $homeUrl = $html->url($this->webroot, true);
 $currentDate = new DateTime();
 
 $feedLink = $html->url(array( 'controller' => 'rss',    
            'action' => 'feeds', 'ext' => 'rss'), true);
 
 ?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title type="text">Recent Questions</title>
    <link rel="self" href="<?php echo $feedLink; ?>" type="application/atom+xml" />
    <updated><?php echo $currentDate->format(DateTime::ATOM);?></updated>
    <id><?php echo $homeUrl; ?></id>
<?php

    foreach ($questions as $question) {
        $questionLink = $html->url(array( 'controller' => 'posts',    
            'action' => 'view', 'public_key' => $question['Post']['public_key'], 'title' => Inflector::slug($question['Post']['title'])), true);
        
        $userLink = $html->url(array( 'controller' => 'users',    
            'action' => 'view', 'public_key' => $question['User']['public_key'], 'title' => Inflector::slug($question['User']['username'])), true);
 
        $dateCreated = new DateTime();
        $dateCreated->setTimestamp($question['Post']['timestamp']);
        
        $dateModified = new DateTime();

        
        if( $question['Post']['last_edited_timestamp'] == 0 ) {
            $dateModified = $dateCreated;
        } else {
            $dateModified->setTimestamp($question['Post']['last_edited_timestamp']);
        }
        
        ?>
            <entry>
                <id><?php echo $questionLink; ?></id>
                <title type="text"><?php echo $question['Post']['title']; ?></title>
                <author>
                    <name><?php echo $question['User']['username']; ?></name>
                    <uri><?php echo $userLink; ?></uri>
                </author>
                <link rel="alternate" href="<?php echo $questionLink; ?>" />
                <published><?php echo $dateCreated->format(DateTime::ATOM);?></published>
                <updated><?php echo $dateModified->format(DateTime::ATOM);?></updated>
                <summary type="html">
                    <?php echo htmlspecialchars($question['Post']['content']); ?>
                </summary>
            </entry>
        <?php 
    }
?>
</feed>