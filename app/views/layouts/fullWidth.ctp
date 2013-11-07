<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title_for_layout; ?> | Coordino</title>
        <?php echo $html->css('screen.css'); ?>
        <!--[if IE]>
        <style type="text/css">
          .wrapper {
            zoom: 1;     /* triggers hasLayout */
            }  /* Only IE can see inside the conditional comment
            and read this CSS rule. Don't ever use a normal HTML
            comment inside the CC or it will close prematurely. */
        </style>
        <![endif]-->
        <link rel="stylesheet" href="stylesheets/print.css" type="text/css" media="print" charset="utf-8">
        <!--[if lte IE 6]><link rel="stylesheet" href="stylesheets/lib/ie.css" type="text/css" media="screen" charset="utf-8"><![endif]-->
    </head>

    <body onload="prettyPrint()">

        <div id="page">

            <div class="wrapper" id="header">

                <div class="wrapper">
                    <div id="top_actions" class="top_actions">
                        <?php
                        echo $form->create('Post', array('action' => 'display'));
                        echo $form->text('needle', array('value' => 'search', 'onclick' => 'this.value=""'));
                        echo $form->end();
                        ?>
                        <ul class="tabs">
                            <?php if ($session->check('Auth.User.id')) { ?>
                                <li>
                                    <?php
                                    echo $html->link(
                                            $session->read('Auth.User.username'), '/users/' . $session->read('Auth.User.public_key') . '/' . $session->read('Auth.User.username')
                                    );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if (!$session->check('Auth.User.id') || $session->read('Auth.User.registered') == 0) { ?>
                                <li>
                                    <?php
                                    echo $html->link(
                                            'register', array('controller' => 'users', 'action' => 'register')
                                    );
                                    ?>
                                </li>
                            <?php } ?>
                            <li>
                                <?php
                                echo $html->link(
                                        'about', array('controller' => 'pages', 'action' => 'display', 'about')
                                );
                                ?>
                            </li>
                            <li>
                                <?php
                                echo $html->link(
                                        'help', array('controller' => 'pages', 'action' => 'display', 'help')
                                );
                                ?>
                            </li>
                            <?php if ($session->check('Auth.User.id') && $session->read('Auth.User.registered') == 1) { ?>
                                <li>
                                    <?php
                                    echo $html->link(
                                            'logout', array('controller' => 'users', 'action' => 'logout')
                                    );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <div class="wrapper">
                    <?php
                    echo $html->link(
                            $html->image('logo.png', array('alt' => 'Logo', 'id' => 'logo')), '/', null, null, false
                    );
                    ?>

                    <ul class="tabs">
                        <li>
                            <?php
                            echo $html->link(
                                    'Questions', '/'
                            );
                            ?>
                        </li>
                        <li><a href="/tags">Tags</a></li>
                        <li><a href="/questions/unanswered">Unsolved</a></li>
                    </ul>
                    <ul class="tabs" style="float: right;">
                        <li>
                            <?php
                            echo $html->link(
                                    'Ask a Question', array('controller' => 'posts', 'action' => 'ask')
                            );
                            ?>
                        </li>
                    </ul>
                </div>

            </div>
            <div id="body" class="wrapper">
                <div id="fullWidth">
                    <?php echo $content_for_layout; ?>
                </div>
            </div><!-- end #body -->

            <div id="footer">
                <ul class="tabs">
                    <li><a href="/">home</a></li>
                    <li><a href="/questions/ask">ask a question</a></li>
                    <li><a href="/about">about</a></li>
                    <li><a href="/help">help</a></li>
                </ul>

                <p class="quiet"><small>&copy; 2009 Meezik Inc.</small></p>
            </div>
        </div>
        <script type="text/javascript" src="/js/jquery/jquery.js"></script>
        <script type="text/javascript" src="/js/jquery/jquery.tabs.js"></script>
        <script type="text/javascript" src="/js/jquery/jquery.ui-1.7.2.js"></script>
        <script type="text/javascript" src="/js/jquery/ui.core.js"></script>
        <script type="text/javascript">
            $(function() {
                $("#tabs").tabs();
            });
        </script>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,$
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-18708987-13', 'colldev.com');
            ga('send', 'pageview');
        </script>
        <?php echo $this->element('google_analytics'); ?>
    </body>
</html>
