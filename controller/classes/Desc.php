<?php

/**
 * Description of Post
 * but also of a category??? FIXME
 *
 * @author jplr
 */
class Desc {

    public string $value; // The short text intended to search engines that describes the body

    public function __construct(string $valueS) {
        $this->value = $valueS;    }

}
