<?php

// Return posts list on profile.
function get_profile_posts(string $name, int $page, int $perpage): array {
    $posts = get_posts_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('/', $v['dirname']);
        $author = $str[count($str) - 4];
        if (strtolower($name) === strtolower($author)) {
            $tmp[] = $v;
        }
    }

    return get_posts($tmp, $page, $perpage);
}

// Return author info.
function get_author(string $name): Author|null {
    $username = 'config/users/' . $name . '.ini';

    if (!file_exists($username)) {
        not_found('get_author 25');

        return null;
    }

    $author = new Author($name);

    $names = get_author_name();

    if (!empty($names)) {

        foreach ($names as $index => $v) {

            // Replaced string
            $replaced = substr($v, 0, strrpos($v, '/')) . '/';

            // Author string
            $str = explode('/', $replaced);
            $profile = $str[count($str) - 2];

            if ($name === $profile) {
                // Profile URL
                $author->url = site_url() . 'author/' . $profile;

                // Get the contents and convert it to HTML
                $content = file_get_contents($v);

                // Extract the name
                $author->pseudo = $profile ;
                $author->name = get_content_tag('t', $content); // FIXME should be real name, not pseudo
                // Get the contents and convert it to HTML
                $author->about = MarkdownExtra::defaultTransform(remove_html_comments($content));

                return $author;
            }
        }
    }
    
    not_found('get_author 63');
    return null;
}

// Return default profile
// FIXME make profile parametrable from config file
function default_profile(string $name): Author {
    $author = new Author($name);

    $author->name = $name;
    $author->about = '<p>Just another HTMLy user.</p>';

    $author->description = 'I am an author';

    return $author;
}
