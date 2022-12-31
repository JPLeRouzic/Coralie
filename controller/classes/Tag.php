<?php

/**
 * Description of Tag
 *
 * @author jplr
 */
class Tag {
    
    public string $value; // the title of the tag
    public string $tagb; // breadcrumb (user's logical path)
    public Tag $related; // Tag of related posts
    public string $url ;
    public string $description ;
    
        public function __construct(string $value) {
        $this->value = $value;
    }

}
