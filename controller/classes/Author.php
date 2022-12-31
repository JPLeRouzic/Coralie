<?php

/**
 * Description of Author
 *
 * @author jplr
 */
class Author {

    public string $url;
    public string $pseudo;
    public string $name;
    public string $about;
    public string $description;

    public function __construct(string $val) {
        $this->name = $val;
    }

}
