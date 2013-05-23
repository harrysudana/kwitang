<!DOCTYPE html>
<html>
<head>
    <title>RSS</title>
    <style>
    body {
        width: 80%;
        margin: 0 auto;
        background: #F5F5F5;
    }
    ol, ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    ol li, ul li {
        margin: 4px 0;
        padding-left: 24px;
        background: transparent url('<?php echo base_url('assets/img/feed.png'); ?>') 0 0 no-repeat;
    }
    </style>
</head>
<body>
    <div class="all">
        <h1>RSS <?php echo kconfig ('system', 'site_name', 'Feed'); ?></h1>
        <hr>
        <?php
        function print_sct($sct, $st)
        {
            echo '<ol>';
            foreach ($sct as $s) {
                echo '<li><a href="' . base_url('rss/' . $st . '/' . $s->name) . '">' . var_lang($s->title) . '</a></li>';
            }
            echo '</ol>';
        }

        function print_childs($childs)
        {
            echo '<ul>';
            foreach ($childs as $c) {
                echo '<li>';
                echo '<a href="' . base_url('rss/' . $c->name) . '">' . var_lang($c->title) . '</a>';
                if ( ! empty($c->sct)) {
                    print_sct($c->sct, $c->name);
                }
                if ( ! empty($c->childs)) {
                    print_childs($c->childs);
                }
                echo '</li>';
            }
            echo '</ul>';
        }

        echo '<ul>';
        foreach ($st12 as $s) {
            echo '<li>';
            echo '<a href="' . base_url('rss/' . $s->name) . '">' . var_lang($s->title) . '</a>';
            if ( ! empty($s->sct)) {
                print_sct($s->sct, $s->name);
            }
            if ( ! empty($s->childs)) {
                print_childs($s->childs);
            }
            echo '</li>';
        }
        echo '<ul>';

        ?>
        <hr>
        &copy; <?php echo kconfig ('system', 'site_name', 'webmaster');?>
    </div>

</body>
</html>
