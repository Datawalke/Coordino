<?php 
class P28nController extends AppController {
    var $name = 'P28n';
    var $uses = null;
    var $components = array('P28n');

    function change($lang = null) {
        $this->P28n->change($lang);

        $this->redirect($this->referer(null, true));
    }

    function shuntRequest() {
        $this->P28n->change($this->params['lang']);

        $args = func_get_args();
        $this->redirect("/" . implode("/", $args));
    }
} 