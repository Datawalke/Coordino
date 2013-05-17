<?php 
App::import('Vendor', 'phpThumb', array('file' => 'phpThumb'.DS.'phpthumb.class.php'));

class ThumbnailHelper extends Helper    {
    
    private $php_thumb;
    private $options;
    private $tag_options;
    private $file_extension;
    private $cache_filename;
    private $error;
    
    private function init($options = array(), $tag_options = array())    {
        $this->options = $options;
        $this->tag_options = $tag_options;
        $this->set_file_extension();
        $this->set_cache_filename();
        $this->error = '';
    }
    
    private function set_file_extension()    {
        $this->file_extension = substr($this->options['src'], strrpos($this->options['src'], '.'), strlen($this->options['src']));
    }
    
    private function set_cache_filename()    {
        ksort($this->options);
        $filename_parts = array();
        $cacheable_properties = array('src', 'new', 'w', 'h', 'wp', 'hp', 'wl', 'hl', 'ws', 'hs', 'f', 'q', 'sx', 'sy', 'sw', 'sh', 'zc', 'bc', 'bg', 'fltr');
        foreach($this->options as $key => $value)    {
            if(in_array($key, $cacheable_properties))    {
                $filename_parts[$key] = $value;
            }
        }
        
        $this->cache_filename = '';
        foreach($filename_parts as $key => $value)    {
            $this->cache_filename .= $key . $value;
        }
        $this->cache_filename = $this->options['save_path'] . DS . md5($this->cache_filename) . $this->file_extension;
    }
    
    private function image_is_cached()    {
        if(is_file($this->cache_filename))    {
            return true;
        } else    {
            return false;
        }
    }
    
    private function create_thumb()    {
        $this->php_thumb = new phpThumb();
        foreach($this->php_thumb as $var => $value) {
            if(isset($this->options[$var]))    {
                $this->php_thumb->setParameter($var, $this->options[$var]);
            }
        }
        if($this->php_thumb->GenerateThumbnail()) {
            $this->php_thumb->RenderToFile($this->cache_filename);
        } else {
            $this->error = ereg_replace("[^A-Za-z0-9\/: .]", "", $this->php_thumb->fatalerror);
            $this->error = str_replace('phpThumb v1.7.8200709161750', '', $this->error);
        }
    }
    
    /**
     * Print image tag for thumbnail. If the thumbnail doesn't exist, it will be created.
     * @param array $options
     * @param array $tag_options
     */
    public function show(array $options = array(),array $tag_options = array())    {
        echo $this->get($options, $tag_options);
    }
    
    /**
     * Return image tag for thumbnail. If the thumbnail doesn't exist, il will be created
     * @param array $options
     * @param array $tag_options
     */
    public function get(array $options = array(), array $tag_options = array()) {
        $this->init($options, $tag_options);
        if(!$this->image_is_cached())    {
            $this->create_thumb();
        }
        return $this->get_image_tag();
    }
    
    /**
     * Create image tag based on the current conf.
     */
    private function get_image_tag() {
        if($this->error != '')    {
            $src = $this->options['error_image_path'];
            //$this->tag_options['alt'] = $this->error;
        } else    {
            $src = $this->options['display_path'] . '/' . substr($this->cache_filename, strrpos($this->cache_filename, DS) + 1, strlen($this->cache_filename));
        }
        $img_tag = '<img src="' . $src . '"';
        if(isset($this->options['w']))    {
            $img_tag .= ' width="' . $this->options['w'] . '"';
        }
        if(isset($this->options['h']))    {
            $img_tag .= ' height="' .  $this->options['h'] . '"';
        }
        if(isset($this->options['alt']))    {
            $img_tag .= ' alt="' .  $this->options['alt'] . '"';
        }
        foreach($this->tag_options as $key => $value)    {
            $img_tag .= ' ' . $key . '="' . $value . '"';
        }
        $img_tag .=  ' />';
        return $img_tag;
    }
    
}
?>