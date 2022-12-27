<?php 

require_once 'controller/plugins/php-rss-writer/Feed.php';
require_once 'controller/plugins/php-rss-writer/Channel.php';
require_once 'controller/plugins/php-rss-writer/Item.php';

// Turn an array of posts into an RSS feed
function generate_rss(array $posts)
{
    $feed = new Feed();
    $channel = new Channel();
    $rssLength = config('rss.char');

    $channel
        ->title(blog_title())
        ->description(blog_description())
        ->url(site_url())
        ->appendTo($feed);

    foreach ($posts as $post) {

        if (!empty($rssLength)) {
            if (strlen(strip_tags($post->description->value)) < config('rss.char')) {
                $string = preg_replace('/\s\s+/', ' ', strip_tags($post->description->value));
                $body = $string . '...';
            } else {
                $string2 = preg_replace('/\s\s+/', ' ', strip_tags($post->description->value));
                $string1 = substr($string2, 0, config('rss.char'));
                $string = substr($string1, 0, strrpos($string1, ' '));
                $body = $string . '...';
            }
        } else {
            $body = $post->description->value;
        }

        $item = new Item();
        $cats = explode(',', str_replace(' ', '', strip_tags($post->catg->title)));
        foreach ($cats as $cat) {
            $item
                ->category($cat, site_url() . 'category/' . strtolower($cat));
        }
        $item
            ->title($post->title->value)
            ->pubDate($post->date)
            ->description($body)
            ->url($post->url)
            ->appendTo($channel);
    }

    echo $feed;
}

// Function to generate OPML file
function generate_opml()
{
    $opml_data = array(
        'head' => array(
            'title' => blog_title() . ' OPML File',
            'ownerName' => blog_title(),
            'ownerId' => site_url()
        ),
        'body' => array(
            array(
                'text' => blog_title(),
                'description' => blog_description(),
                'htmlUrl' => site_url(),
                'language' => 'unknown',
                'title' => blog_title(),
                'type' => 'rss',
                'version' => 'RSS2',
                'xmlUrl' => site_url() . 'feed/rss'
            )
        )
    );

    $opml = new opml($opml_data);
    echo $opml->opmlRender();
}

