<?php  echo '<?xml version="1.0" encoding="utf-8"?>' . "\n"; ?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/"  xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <atom:link href="<?php echo $feed_url; ?>" rel="self" type="application/rss+xml" />
        <?php
            if( !empty($feed_title))
                echo '<title>' . $feed_title . '</title>' . "\n";
            if( !empty($feed_url))
                echo '<link>' . $feed_url . '</link>' . "\n";
            if( !empty($description))
                echo '<description>' . $description . '</description>' . "\n";

            $now = time();
            $gmt = local_to_gmt($now);
            echo '<pubDate>' . standard_date('DATE_RSS', $gmt) . '</pubDate>' . "\n";
            echo '<generator>Web CMS4</generator>' . "\n";

            foreach ($data as $d) {
                foreach ($d['content']['data'] as $c) {
                    $url = content_url($d['sct']->name, $d['sct']->content_type , $c->id, $c->slug);
                    echo '<item>' . "\n"
                        .'  <title>' . $c->title . '</title>' . "\n"
                        .'  <pubDate>' . standard_date('DATE_RSS', strtotime($c->pub_date)) . '</pubDate>' . "\n"
                        .'  <link>' . $url . '</link>' . "\n"
                        .'  <description>' . iconv('UTF-8', 'UTF-8', mb_convert_encoding(html_entity_decode(strip_tags($c->description)), 'UTF-8')) . '</description>' . "\n"
                        .'  <content:encoded><![CDATA[' . mb_convert_encoding(html_entity_decode($c->body), 'UTF-8') . ']]></content:encoded>' . "\n"
                        .'  <guid>' . $url . '</guid>' . "\n";

                    if ( ! empty($c->foto) && @file_exists(ROOT_PATH . $c->foto)) {
                        $fsize = filesize(ROOT_PATH . $c->foto);
                        $imsize = getimagesize(ROOT_PATH . $c->foto);
                        echo '  <enclosure url="' . base_url($c->foto) . '" length="' . $fsize . '" type="' . $imsize['mime'] . '" />' . "\n";
                    }
                    echo '</item>' . "\n";
                }
            }
        ?>
    </channel>
</rss>
