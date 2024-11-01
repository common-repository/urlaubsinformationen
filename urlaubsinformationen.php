<?php
/*
Plugin Name: Urlaubsinformationen
Plugin URI: http://wordpress.org/extend/plugins/urlaubsinformationen/
Description: Adds a customizeable widget which displays the latest news by http://www.urlaub.org/
Version: 1.0
Author: Andreas Pfaff
Author URI: http://www.urlaub.org/
License: GPL3
*/

function urlaubsinformationen()
{
  $options = get_option("widget_urlaubsinformationen");
  if (!is_array($options)){
    $options = array(
      'title' => 'Urlaubsinformationen',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://news.google.de/news?pz=1&cf=all&ned=de&hl=de&q=urlaub&cf=all&output=rss'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_urlaubsinformationen($args)
{
  extract($args);
  
  $options = get_option("widget_urlaubsinformationen");
  if (!is_array($options)){
    $options = array(
      'title' => 'Urlaubsinformationen',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  urlaubsinformationen();
  echo $after_widget;
}

function urlaubsinformationen_control()
{
  $options = get_option("widget_urlaubsinformationen");
  if (!is_array($options)){
    $options = array(
      'title' => 'Urlaubsinformationen',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['urlaubsinformationen-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['urlaubsinformationen-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['urlaubsinformationen-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['urlaubsinformationen-CharCount']);
    update_option("widget_urlaubsinformationen", $options);
  }
?> 
  <p>
    <label for="urlaubsinformationen-WidgetTitle">Widget Title: </label>
    <input type="text" id="urlaubsinformationen-WidgetTitle" name="urlaubsinformationen-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="urlaubsinformationen-NewsCount">Max. News: </label>
    <input type="text" id="urlaubsinformationen-NewsCount" name="urlaubsinformationen-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="urlaubsinformationen-CharCount">Max. Characters: </label>
    <input type="text" id="urlaubsinformationen-CharCount" name="urlaubsinformationen-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="urlaubsinformationen-Submit"  name="urlaubsinformationen-Submit" value="1" />
  </p>
  
<?php
}

function urlaubsinformationen_init()
{
  register_sidebar_widget(__('Urlaubsinformationen'), 'widget_urlaubsinformationen');    
  register_widget_control('Urlaubsinformationen', 'urlaubsinformationen_control', 300, 200);
}
add_action("plugins_loaded", "urlaubsinformationen_init");
?>