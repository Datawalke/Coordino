<?php
/**
 * Controller to generate RSS feeds containing the recent questions.
 *
 * @author Paul de Raaij <paul@paulderaaij.nl>
 */
class RssController extends AppController {
    var $name = 'Rss';
    var $uses = array('Post', 'User');
    
    var $components = array('RequestHandler');
    var $helpers = array('Text'); 
    
    /**
     * By default return an rss feed with the latest 15 questions 
     */
    public function feeds() {
        
        if( !$this->RequestHandler->isRss() ) {
            $this->redirect('/');
        }
        $questions = $this->Post->find('all', array('conditions' => array('Post.type' => 'question',), 
                                                    'order' => 'Post.timestamp DESC',
                                                    'limit' => 15));
  
        return $this->set(compact('questions'));
    }
}

?>
