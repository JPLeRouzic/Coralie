<?php

/**
 * Description of Category
 *
 * @author jplr
 */
class Category {

    public string $file ;
    public string $url ;
    public string $title ;
    public string $description ;

    public function __construct(string $category2) {
        $this->title = $category2;
    }

}
