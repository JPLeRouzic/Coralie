<?php

/**
 * Description of Author
 *
 * @author jplr
 */
class Author {

    public $url;
    public $pseudo;
    public $name;
    public $about;
    public $description;

    public function __construct($val) {
        $this->name = $val;
    }

}
