<?php
  /*
   Plugin Name: Schnaeppchen Widget
   Plugin URI: http://www.reichweite.de
   Description: This Plugin provides a widget that allows the user to display actual bargains (Schnäppchen) in the sidebar. You can choose the number of articles to show and where you get them from (reichweite.de or schnaeppchenforum.com). Why should you use this plugin / widget? It allows you to have new, fresh and interesting content on your blog, which brings you more views. The RSS-Feeds are updated several times a day, up to 10 times/day! 
   Author: reichweite.de
   Version: 1.0.0
   Author URI: http://www.reichweite.de
   */
  function ojo_feed_init()
  {
      function oj_getFeed($args = array(), $displayComments = true, $interval = '')
      {
          $popularPosts = array();
          $postCount = $args['anz'];
          $ru = 0;
          if ($args['schn'] == "1" && $args['reich'] == "1") {
              //beide Feeds ausgewaehlt
              $postCount = ceil($postCount / 2);
          }
          
          if ($args['reich'] == "1") {
              $xml = simplexml_load_file('http://feeds.feedburner.com/Schnaeppchenticker');
              foreach ($xml->channel as $item) {
                  set_time_limit(120);
                  foreach ($item->item as $itema) {
                      if ($ru < $postCount) {
                          $popularPosts[$ru]['title'] = "" . $itema->title . "";
                          $popularPosts[$ru]['url'] = "" . $itema->link . "";
                          $popularPosts[$ru]['pubdate'] = "" . $itema->pubDate . "";
                          $ru++;
                      }
                  }
              }
          }
          $secundo = 0;
          if ($args['schn'] == "1") {
              $xml = simplexml_load_file('http://www.schnaeppchenforum.com/syndication.php');
              foreach ($xml->channel as $item) {
                  set_time_limit(120);
                  foreach ($item->item as $itema) {
                      if ($secundo < $postCount && $ru < $args['anz']) {
                          $popularPosts[$ru]['title'] = "" . $itema->title . "";
                          $popularPosts[$ru]['url'] = "" . $itema->link . "";
                          $popularPosts[$ru]['pubdate'] = "" . $itema->pubDate . "";
                          $ru++;
                          $secundo++;
                      }
                  }
              }
          }
          
          foreach ($popularPosts as $post) {
              echo "<p style=\"margin-bottom:2px;border-bottom: 1px solid #bcbcbc\">";
              echo "<a target='_blank' title='" . $post['title'] . "' class='ojoLink' href=\"" . $post['url'] . "\">" . $post['title'] . "</a>";
              echo "</p>";
          }
          
          if ($args['schn'] == "1" and $args['reich'] != "1")
              echo "<p><a target='_blank' href='http://www.schnaeppchenforum.com' title='Schnaeppchenforum.com'>&copy;  Schnaeppchenforum.com</a></p>";
          else
              echo "<p><a href='http://www.reichweite.de' target='_blank' title='Reichweite.de'>&copy; Reichweite.de</a></p>";
      }
      
      
      function ojo_feed($args)
      {
          $options = get_option('ojo_feed');
          $title = '<img src="wp-content/plugins/schnaeppchen-widget/mini-logo.png" align="top" /> Schn&auml;ppchen';
          extract($args);
?>
  <style type="text/css"> 
  a.ojoLink {
    text-decoration:none;
    background: url(wp-content/plugins/schnaeppchen-widget/star-cold.png) 0 -2px no-repeat;
    padding-left: 16px;
    
  }
  a.ojoLink:hover{
    text-decoration:none;
    background: url(wp-content/plugins/schnaeppchen-widget/star-hot.png) 0 -2px no-repeat;
        padding-left: 16px;
  
  }

</style> 
  <?php
          echo $before_widget;
          echo $before_title;
          echo $title;
          echo $after_title;
          oj_getFeed($options);
          
          echo $after_widget;
      }
      
      function ojo_feed_control()
      {
          $options = $newoptions = get_option('ojo_feed');
          if ($_POST['ojo_feed-submit']) {
              $newoptions['schn'] = strip_tags(stripslashes($_POST['ojo_feed-schn']));
              $newoptions['reich'] = strip_tags(stripslashes($_POST['ojo_feed-reich']));
              $newoptions['anz'] = strip_tags(stripslashes($_POST['ojo_feed-anz']));
          }
          
          if ($options != $newoptions) {
              $options = $newoptions;
              update_option('ojo_feed', $options);
          }
          
          
          $schn = attribute_escape($options['schn']);
          $reich = attribute_escape($options['reich']);
          $anz = attribute_escape($options['anz']);
          
          echo "<p>";
          echo "  <input class=\"checkbox\" type=\"checkbox\" value='1'";
          echo($schn == '1') ? " checked " : "";
          echo 'id="ojo_feed-schn" name="ojo_feed-schn" />';
          echo '<label for="ojo_feed-schn">&nbsp;Schnaeppchenforum.de</label>';
          echo "</p>";
          echo "<p>";
          echo "  <input class=\"checkbox\" type=\"checkbox\" value='1'";
          echo($reich == '1') ? " checked " : "";
          echo 'id="ojo_feed-reich"   name="ojo_feed-reich" />';
          echo '<label for="ojo_feed-reich">&nbsp;Reichweite.de</label>';
          echo "</p>";
          
          echo "<p>";
          echo '<label for="ojo_feed-anz">Anzahl:</label>';
          echo '<select id="ojo_feed-anz" name="ojo_feed-anz"  class="widefat" style="width:100%;">';
          for ($ia = 3; $ia <= 10; $ia++) {
              echo "<option";
              echo($anz == $ia) ? " selected " : "";
              echo "  >" . $ia . "</option>";
          }
          echo "</select>";
          echo "</p>";
          
          echo '<input type="hidden" id="ojo_feed-submit" name="ojo_feed-submit" value="1" />';
      }
      
      // This registers the widget.
      wp_register_sidebar_widget('st1', 'Schn&auml;ppchen Widget', 'ojo_feed', array('description' => __('immer aktuelle Schn&auml;ppchen in Deiner Sidebar')));
      // This registers the (optional!) widget control form.    
      wp_register_widget_control('st1', 'Schn&auml;ppchen Widget', 'ojo_feed_control');
  }
  
  add_action('plugins_loaded', 'ojo_feed_init');
?>