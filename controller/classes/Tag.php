<?php

/**
 * Description of Tag
 *
 * @author jplr
 */
class Tag {
    
    public $value; // the title of the tag
    public string $tagb; // breadcrumb (user's logical path)
    public Tag $related; // Tag of related posts
    public string $url ;
    public string $description ;
    
        public function __construct($value) {
        $this->value = $value;
    }

}
