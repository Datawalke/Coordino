<?php 
class P28nComponent extends Object {
    var $components = array('Session', 'Cookie');

    function startup() {
        if (!$this->Session->check('Config.language')) {
            $this->change(($this->Cookie->read('lang') ? $this->Cookie->read('lang') : DEFAULT_LANGUAGE));
        }
    }

    function change($lang = null) {
        if (!empty($lang)) {
            $this->Session->write('Config.language', $lang);
            $this->Cookie->write('lang', $lang, null, '+350 day'); 
        }
    }
} 