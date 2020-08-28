<!DOCTYPE html>
<html lang="en">
<head>
  <title>NASA Feed Viewer</title>
  <link rel="stylesheet" type="text/css" href="css/styles.css"/>
</head>
<body>

  <!-- Beginning of PHP scripting for the page. -->
  <?php


    /**
     * @file
     * Feed viewer build for multiple streams of content
     */

    //Creation of the multi-dimensional array that houses the information for the selected feeds.
    $feedList = array (
      array(
        "feedName" => "news",
        "feedURL" => "https://www.nasa.gov/rss/dyn/breaking_news.rss"
      ),
      array(
        "feedName" => "station",
        "feedURL" => "http://blogs.nasa.gov/stationreport/feed/"
      ),
      array(
        "feedName" => "solar",
        "feedURL" => "https://www.nasa.gov/rss/dyn/solar_system.rss"
      ),
      array(
        "feedName" => "earth",
        "feedURL" => "https://www.nasa.gov/rss/dyn/earth.rss"
      ),
      array(
        "feedName" => "hurricane",
        "feedURL" => "https://www.nasa.gov/rss/dyn/hurricaneupdate.rss"
      ),
    );

     /**
      * Creates the feed selection buttons that change the feed display.
      *
      * @param array $tab
      *  array that contains endpoint information
      */
    function feedButtonBuild(&$tab)
    {
        if (@simplexml_load_file($tab[feedURL])) {
                $tabClick = "onclick=\"buttonClick('" . $tab[feedName] . "')\"";
        }
            $tabInfo = simplexml_load_file($tab[feedURL], null, LIBXML_NOCDATA);
            echo "<button class='tabList'" . $tabClick . ">";
            echo "<h2>" . $tabInfo->channel->title . "</h2>";
            echo "</button>";

    };

    /**
     * Builds the framework divs that each feed is encapsulated in.
     *
     * @param array $tab
     * array that contains endpoint information
     */
    function feedBodyFrameBuild(&$tab)
    {
        $invalidurl = false;
        if (@simplexml_load_file($tab[feedURL])) {
            $i=0;
            $rss = simplexml_load_file($tab[feedURL], null, LIBXML_NOCDATA);
            $itemCount = count($rss->channel->item);
            $feedType = $tab[feedName];
            echo "<div class='feedColumn' id = '" . $tab[feedName] . "'>";
            echo "<div class = 'newsTab'>";
        } else {
            $invalidurl = true;
            echo "<h2>Invalid RSS feed URL.</h2>";
            echo "<span>" . $tab . "</span>";
        }

        if (!empty($rss)) {
            feedContentBuild($rss, $feedType, $itemCount);
        } else {
            if (!$invalidurl) {
                echo "<h2>No item found</h2>";
            }
        }
        echo "</div>";
        echo "</div>";
    };


    /**
     * Pulls content from each feed and converts it for display within the viewer.
     *
     * @param object $rss
     *  RSS feed
     * @param string $feedType
     *  Defines the type of feed being parsed
     * @param int    $itemCount
     *  Total number of items in each feed
     */
    function feedContentBuild($rss, $feedType, $itemCount)
    {
        $i=0;
        //parse each feed item
        foreach ($rss->channel->item as $item) {
            $title = $item->title;
            $link = $item->link;
            $description = $item->description;
            $postDate = $item->pubDate;
            $pubDate = date('D, M d, Y', strtotime($postDate));
            //$subItem = $item->children('media', true)->content;
            //$vidThumb = $item->children('media', true)->content->children('media', true)->thumbnail->attributes();
            $media = $item->enclosure->attributes()->url;
            //echo $media . "</br>";

            if ($i>=$itemCount) {
                break;
            }

            echo "<div class='post'>";
            echo "<div class='postHead'>";
            echo "<h3><a class='feedTitle' target='_blank' href='" . $link . "'>" . $title . "</a></h3>";
            echo "</div>";
            echo "<div class='postContent'>";

            //builds out the news feed item HTML
            $mediaVal = '<img src="' . $media . '"/>';
            echo "<div class='postImg'>";
            if ($media !== NULL){
              echo "<a class='articleLink' target='_blank' href=" . $link . ">" . $mediaVal . "</a>";
            }
            echo "</div>";
            echo implode(' ', array_slice(explode(' ', $description), 0, 80)) . "..." . "<br/>";
            echo "<div class='articleDate'>" . $pubDate . "</div>";
            echo "<div class='artLinkWrap'>";
            echo "<a class='articleLink' target='_blank' rel='noopener noreferrer' href=" . $link . ">Read more</a>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            $i++;
        }
    };

    ?>
  <!-- End of PHP scripting for the page. -->
  <div class="content">
    <div class='feedHead'>
    <h1>NASA News Feeds</h1>
    </div>
    <div class='feeds'>
      <div class='buttonWrap'>
        <!-- Build the header buttons -->
        <?php

        foreach ($feedList as $tab) {
            feedButtonBuild($tab);
        };

        ?>
        <!-- End header button build -->
    </div>
    <div>
      <!-- Build the body section for the feed output -->
      <?php

        foreach ($feedList as $tab) {
            feedBodyFrameBuild($tab);
        };

        ?>
      <!-- End feed output body section build -->
    </div>
  </div>
<script>
  // Script that builds button interaction

  function buttonClick(x) {
    if (x !== 'undefined') {
      var a = document.getElementsByClassName("feedColumn");
      var y = document.getElementById(x);
      var i;
      for(i = 0; i < a.length; i++) {
        a[i].style.display = "none";
        y.style.display = "block";
      }

    }

  }
</script>
</body>
</html>