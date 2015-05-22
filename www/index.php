<?
  $app_dir = "/var/www/profyma.se/application";

  include $app_dir.'/database/database.php';

  session_start();

  $query = $_SERVER['QUERY_STRING'];

   if (preg_match('/^om$/', $query, $matches))
     include $app_dir.'/views/about.php.html';
   else if (preg_match('/^vad$/', $query, $matches))
     include $app_dir.'/views/what.php.html';
   else if (preg_match('/^nytt$|^e\/.*$|^p\/.*$/', $query, $matches))
     include $app_dir.'/controllers/handle_project.php';
   else
     include $app_dir.'/views/latest.php.html';

?>