<?php

/**
 * Description of Post
 *
 * @author jplr
 */
class Post {

    public Category $catg;
    public Tag $tag;
    public string $url; // as in https://pad....org/News/content/users/admin/blog/english/post/
                        // url field is optional at creation, in this case the system choose one
    public string $file; // The place where it is stored as in "/website/News/content/users/admin/blog/english/post"
    public string $md; // part of file name of description as "is"als_draft" in "als_draft.md"
    public Title $title; // Title of the post
    public Author $author;
    public Type $type; // post, draft, scientific rating, video, image, etc..
    public int $date; // int|false <p>Returns a timestamp on success, false otherwise
    public int $views;
    public Desc $description; // contains short description
    public string $body; // content of the post as in "Studies in arthropods have revealed the existence of ..."
                         // This is the content field in the form
    public Archive $archive;
    public string $ct ; // ???

    public function __construct() {       
        $this->description = new Desc('') ;       
    }
    
}
