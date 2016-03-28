<?php

if ( function_exists('register_sidebar') )
    register_sidebar();

add_action( 'wp_enqueue_scripts', function(){ wp_enqueue_script( 'jquery' ); } );

function otat_get_tweet_style() {
    $title    = strlen( get_the_title() );
    $is_reply = ozh_ta_is_reply_or_not( false );
    if( $title > 100 ) {
        $style = 'long';
    } elseif ( $title > 50 ) {
        $style = 'medium';
    } else {
        $style = 'short';
    }
    return "$style $is_reply";
}

function otat_month_archives() {
    global $wpdb;
    $where = "WHERE post_type = 'post' AND post_status = 'publish'";
    $query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY YEAR DESC, MONTH ASC";
    $_archive = $wpdb->get_results( $query );

    $last_year  = (int) $_archive[0]->year;
    $first_year = (int) $_archive[ count( $_archive ) - 1 ]->year;

    $archive    = array();
    $max        = 0;
    $year_total = array();
    
    foreach( $_archive as $data ) {
        if( !isset( $year_total[ $data->year ] ) ) {
            $year_total[ $data->year ] = 0;
        }
        $archive[ $data->year ][ $data->month ] = $data->posts;
        $year_total[ $data->year ] += $data->posts;
        $max = max( $max, $data->posts );
    }
    unset( $_archive );

    for ( $year = $last_year; $year >= $first_year; $year-- ) {
        echo '<div class="archive_year">';
        echo '<span class="archive_year_label">' . $year;
        if( isset( $year_total[$year] ) ) {
            echo '<span class="archive_year_count">' . $year_total[$year] . ' tweets</span>';
        }
        echo '</span>';
        echo '<ol>';
        for ( $month = 1; $month <= 12; $month++ ) {
            $num = isset( $archive[ $year ][ $month ] ) ? $archive[ $year ][ $month ] : 0;
            $empty = $num ? 'not_empty' : 'empty';
            echo "<li class='$empty'>";
            $height = 100 - max( floor( $num / $max * 100 ), 20 );
            if( $num ) {
                $url = get_month_link( $year, $month );
                $m = str_pad( $month, 2, "0", STR_PAD_LEFT);
                echo "<a href='$url' title='$m/$year : $num tweets'><span class='bar_wrap'><span class='bar' style='height:$height%'></span></span>";
                echo "<span class='label'>" . $m . "</span>";
                echo "</a>";
            }
            echo '</li>';
        }
        echo '</ol>';
        echo "</div>";
    }
}

/**
 * Aggiunge la favicon.
 * 
 * Favicon e codice HTML generato da http://realfavicongenerator.net/
 */
function tweets_favicon() {
?>

<link rel="apple-touch-icon" sizes="57x57" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="http://tweets.aldolat.it/pub/favicons/apple-touch-icon-180x180.png">
<link rel="icon" type="image/png" href="http://tweets.aldolat.it/pub/favicons/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="http://tweets.aldolat.it/pub/favicons/favicon-194x194.png" sizes="194x194">
<link rel="icon" type="image/png" href="http://tweets.aldolat.it/pub/favicons/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="http://tweets.aldolat.it/pub/favicons/android-chrome-192x192.png" sizes="192x192">
<link rel="icon" type="image/png" href="http://tweets.aldolat.it/pub/favicons/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="http://tweets.aldolat.it/pub/favicons/manifest.json">
<link rel="shortcut icon" href="http://tweets.aldolat.it/pub/favicons/favicon.ico">
<meta name="msapplication-TileColor" content="#ffc40d">
<meta name="msapplication-TileImage" content="http://tweets.aldolat.it/pub/favicons/mstile-144x144.png">
<meta name="msapplication-config" content="http://tweets.aldolat.it/pub/favicons/browserconfig.xml">
<meta name="theme-color" content="#08a5e1">

<?php }
add_action( 'wp_head', 'tweets_favicon' );

/**
 * Aggiunge Google Analytics
 */
function aldolat_analytics() { ?>

<!-- Theme's function.php | Function: aldolat_analytics() -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-396229-10', 'auto');
  ga('set', 'anonymizeIp', true);
  ga('send', 'pageview');

</script>
<!-- / Theme's function.php -->

<?php }
//add_action( 'wp_footer', 'aldolat_analytics' );

/**
 * Aggiunge lo script per la Cookie Law nel footer.
 * @see https://cookie-script.com
 */
function aldolat_cookie_consent() {
    $output = "\n" . '<!--Start Cookie Script-->
    <script type="text/javascript" charset="UTF-8" src="http://chs02.cookie-script.com/s/403a65b8e7139b91b2d4d0877eb5eef7.js"></script>
    <!--End Cookie Script-->' . "\n\n";
    echo $output;
}
//add_action( 'wp_footer', 'aldolat_cookie_consent' );
