<?

if (preg_match('/^nytt$/', $query, $matches))
{
  // create a project
  $privateID = create_project();
  header('Location: /e/'. $privateID);
  return;
}
else if (preg_match('/^e\/([^\/]*)$/', $query, $matches))
{ 
  // edit a projekt
  $ID = $matches[1];

  if ($projectdata = get_project_by_privateID($ID))
  {
    include $app_dir.'/views/edit_project.php.html';
    return;
  }
}
else if (preg_match('/^e\/([^\/]*)\/toggleedit$/', $query, $matches))
{
  // show or hide edit-buttons, AJAX
  if (isset($_SESSION['edit']))
  {
    if ($_SESSION['edit'] != 'true')
      $_SESSION['edit'] = 'true';
    else
      $_SESSION['edit'] = 'false';
  }
  else
    $_SESSION['edit'] = 'true';

  return;
}
else if (preg_match('/^e\/([^\/]*)\/togglearrows$/', $query, $matches))
{ 
  // show or hide arrows, AJAX
  if (isset($_SESSION['arrows']))
  {
    if ($_SESSION['arrows'] != 'true')
      $_SESSION['arrows'] = 'true';
    else
      $_SESSION['arrows'] = 'false';
  }
  else
    $_SESSION['arrows'] = 'true';

  return;
}
else if (preg_match('/^e\/([^\/]*)\/addtab$/', $query, $matches))
{
  // add a tab to a project
  $ID = $matches[1];

  if ($projectdata = get_project_by_privateID($ID))
  {
    $project_uncoded = json_decode($projectdata['data'], true);
    $last_index = count($project_uncoded);
    $project_uncoded[$last_index] = array("title" => "Ny flik", "text" => "Brödtext", "code" => array());
    $project_uncoded[$last_index]['code'][0] = array("title" => "Kodruta", "code" => "//Massa kod");

    update_project_data($ID, json_encode($project_uncoded));

    header('Location: /e/'. $ID);
    return;
  }
}
else if (preg_match('/^e\/([^\/]*)\/addcodetab\/(\d+)$/', $query, $matches))
{
  // add a code tab to a project
  $ID = $matches[1];
  $tab_ID = $matches[2];

  if ($projectdata = get_project_by_privateID($ID))
  {
    $project_uncoded = json_decode($projectdata['data'], true);
    $last_index = count($project_uncoded);

    if ($tab_ID < $last_index)
    {
      $last_code_index = count($project_uncoded[$tab_ID]['code']);
      $project_uncoded[$tab_ID]['code'][$last_code_index] = array("title" => "Kodruta", "code" => "//Massa kod");

      update_project_data($ID, json_encode($project_uncoded));
    }

    header('Location: /e/'. $ID);
    return;
  }
}
else if (preg_match('/^e\/(.*)\/updatetab\/(\d+)$/', $query, $matches))
{
  // update tab title and text, AJAX
  if (isset($_REQUEST['title']) && isset($_REQUEST['text']))
  {
    $ID = $matches[1];
    $tab_ID = $matches[2];
    if ($projectdata = get_project_by_privateID($ID))
    {
      $project_uncoded = json_decode($projectdata['data'], true);
      $project_uncoded[$tab_ID]['title'] = $_REQUEST['title'];
      $project_uncoded[$tab_ID]['text'] = $_REQUEST['text'];

      update_project_data($ID, json_encode($project_uncoded));
    }
  }
  return;
}
else if (preg_match('/^e\/(.*)\/updatemetadata$/', $query, $matches))
{
  // update metadata, AJAX
  if (isset($_REQUEST['title']))
  {
    $ID = $matches[1];
    if ($projectdata = get_project_by_privateID($ID))
    {
      $metadata_uncoded = json_decode($projectdata['metadata'], true);

      $metadata_uncoded['title']      = $_REQUEST['title'];
      $metadata_uncoded['author']     = isset($_REQUEST['title'])      ? $_REQUEST['author']     : "";
      $metadata_uncoded['public']     = isset($_REQUEST['public'])     ? $_REQUEST['public']     : "false";
      $metadata_uncoded['categories'] = isset($_REQUEST['categories']) ? $_REQUEST['categories'] : array();

      file_put_contents("/tmp/profyma",print_r($_REQUEST, true));

      update_project_metadata($ID, json_encode($metadata_uncoded));
    }
  }
  return;
}
else if (preg_match('/^e\/(.*)\/updatecode\/(\d+)\/(\d+)$/', $query, $matches))
{
  // update code, AJAX
  if (isset($_REQUEST['code']))
  {
    $ID = $matches[1];
    $tab_ID = $matches[2];
    $code_tab_ID = $matches[3];
    if ($projectdata = get_project_by_privateID($ID))
    {

      $project_uncoded = json_decode($projectdata['data'], true);
      $project_uncoded[$tab_ID]['code'][$code_tab_ID]['code'] = $_REQUEST['code'];

      update_project_data($ID, json_encode($project_uncoded));
    }
  }
  return;
}
else if (preg_match('/^e\/(.*)\/deletecodetab\/(\d+)\/(\d+)$/', $query, $matches))
{
  // delete codetab
  $ID = $matches[1];
  $tab_ID = $matches[2];
  $code_tab_ID = $matches[3];
  if ($projectdata = get_project_by_privateID($ID))
  {
    $project_uncoded = json_decode($projectdata['data'], true);
    $new_array = array();

    for ($i = 0; $i < count($project_uncoded[$tab_ID]['code']); $i++)
      if ($i != $code_tab_ID)
          $new_array[] = $project_uncoded[$tab_ID]['code'][$i];

    $project_uncoded[$tab_ID]['code'] = $new_array;
    update_project_data($ID, json_encode($project_uncoded));
  }
  header('Location: /e/'. $ID);
  return;
}
else if (preg_match('/^e\/(.*)\/deletetab\/(\d+)$/', $query, $matches))
{
  // delete tab
  $ID = $matches[1];
  $tab_ID = $matches[2];
  if ($projectdata = get_project_by_privateID($ID))
  {
    $project_uncoded = json_decode($projectdata['data'], true);
    $new_array = array();

    for ($i = 0; $i < count($project_uncoded); $i++)
      if ($i != $tab_ID)
        $new_array[] = $project_uncoded[$i];

    update_project_data($ID, json_encode($new_array));
  }
  header('Location: /e/'. $ID);
  return;
}
else if (preg_match('/^e\/(.*)\/movetableft\/(\d+)$/', $query, $matches))
{
  // move tab left
  $ID = $matches[1];
  $tab_ID = $matches[2];
  if ($projectdata = get_project_by_privateID($ID))
  {
    $project_uncoded = json_decode($projectdata['data'], true);

    if (($tab_ID < count($project_uncoded)) && ($tab_ID > 0))
    {
      $temp = $project_uncoded[$tab_ID - 1];
      $project_uncoded[$tab_ID - 1] = $project_uncoded[$tab_ID];
      $project_uncoded[$tab_ID] = $temp;

      update_project_data($ID, json_encode($project_uncoded));
    }
  }
  header('Location: /e/'. $ID);
  return;
}
else if (preg_match('/^e\/(.*)\/updatecodetitle\/(\d+)\/(\d+)$/', $query, $matches))
{
  // update code tab title, AJAX
  if (isset($_REQUEST['title']))
  {
    $ID = $matches[1];
    $tab_ID = $matches[2];
    $code_tab_ID = $matches[3];
    if ($projectdata = get_project_by_privateID($ID))
    {
      $project_uncoded = json_decode($projectdata['data'], true);
      $project_uncoded[$tab_ID]['code'][$code_tab_ID]['title'] = $_REQUEST['title'];

      update_project_data($ID, json_encode($project_uncoded));
    }
  }
  return;
}
else if (preg_match('/^p\/(.*)$/', $query, $matches))
{
  // copy project to new project
  $ID = $matches[1];

  if ($projectdata = get_project_by_publicID($ID))
  {
    $privateID = copy_project_by_publicID($ID);
    header('Location: /e/'. $privateID);
    return;
  }
}

print("Shall we play a game?");

?>
