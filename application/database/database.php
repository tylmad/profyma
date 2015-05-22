<?

$db_projects_file = '/var/www/profyma.se/application/database/projects.sqlite';
$db_users_file = '/var/www/profyma.se/application/database/users.sqlite';
      
if (! file_exists($db_projects_file))
{
  $db_projects = new PDO('sqlite:'. $db_projects_file);
  
  $db_projects->query('CREATE TABLE Projects
                          (id INTEGER PRIMARY KEY,
                           author TEXT,
                           created TEXT,
                           updated TEXT,
                           metadata TEXT,
                           privateID TEXT,
                           publicID TEXT,
                           data TEXT)');
}
else
{
  $db_projects = new PDO('sqlite:'. $db_projects_file);
}

if (! file_exists($db_users_file))
{
  $db_users = new PDO('sqlite:'. $db_users_file);
  
  $db_users->query('CREATE TABLE Users
                          (id INTEGER PRIMARY KEY,
                           email TEXT,
                           password TEXT,
                           metadata TEXT)');
}
else
{
  $db_users = new PDO('sqlite:'. $db_users_file);
}


function get_project_by_privateID($privateID)
{
  GLOBAL $db_projects;
  $statement = $db_projects->prepare('SELECT * FROM Projects WHERE privateID = ?');
  $statement->execute(array($privateID));
  return $statement->fetch();
}
    
function get_public_projects()
{
  $all_projects = get_all_projects();

  $public_projects = array();
  $unsorted_public_projects = array();

  $categories = array("Matematik 1","Matematik 2","Matematik 3","Matematik 4","Matematik 5","Fysik 1","Fysik 2","Fysik 3","Programmering 1","Programmering 2");
  foreach ($categories as $cat)
    $public_projects[$cat] = array();

  foreach ($all_projects as $project)
  {
    $metadata_uncoded = json_decode($project['metadata'], true);
    if ($metadata_uncoded['public'] == 'true')
    {
      $uncategorized = true;
      if (is_array($metadata_uncoded['categories']))
        foreach($metadata_uncoded['categories'] as $i => $cat)
          if ($cat == "1")
          {
            $public_projects[$categories[$i]][] = $project;
            $uncategorized = false;
          }

      if ($uncategorized)
        $unsorted_public_projects[] = $project;
    }
  }

  return array("categorized" => $public_projects, "unsorted" => $unsorted_public_projects);
}

function get_all_projects()
{
  GLOBAL $db_projects;
  $statement = $db_projects->prepare('SELECT id, author, created, updated, metadata, privateID FROM Projects ORDER BY updated DESC');
  $statement->execute(array());
  return $statement->fetchAll();
}

function get_project_by_publicID($publicID)
{
  GLOBAL $db_projects;
  $statement = $db_projects->prepare('SELECT * FROM Projects WHERE publicID = ?');
  $statement->execute(array($publicID));
  return $statement->fetch();
}

function copy_project_by_publicID($publicID)
{
  GLOBAL $db_projects;
  $statement = $db_projects->prepare('SELECT * FROM Projects WHERE publicID = ?');
  $statement->execute(array($publicID));
  $original = $statement->fetch();

  $new_project = create_project();

  $metadata_uncoded = json_decode($original['metadata'], true);
  $metadata_uncoded['public'] = 'false';
  $metadata_uncoded['author'] = '';

  update_project_data($new_project, $original['data']);
  update_project_metadata($new_project, json_encode($metadata_uncoded));

  return $new_project;
}

function update_project_data($privateID, $data)
{
  GLOBAL $db_projects;
  $statement = $db_projects->prepare('UPDATE Projects SET data = ?, updated = ? WHERE privateID = ?');
  $statement->execute(array($data, time(), $privateID));
}

function update_project_metadata($privateID, $metadata)
{
  GLOBAL $db_projects;
  $statement = $db_projects->prepare('UPDATE Projects SET metadata = ?, updated = ? WHERE privateID = ?');
  $statement->execute(array($metadata, time(), $privateID));
}

function create_project()
{

  function rand_string($length = 6)
  {
    $output = 'abcdefghijkmnopqrstuvwyz23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
  
    $str = '';
    $output_count = strlen($output);
    for ($i = 0; $i < $length; $i++)
      $str .= $output[mt_rand(0, $output_count - 1)];
  
    return $str;
  }

  GLOBAL $db_projects;

  $project = array();
  $project[0] = array("title" => "Flik 1", "text" => "# Titel\n\n## Subtitel\n\nEtt stycke text där ord kan ha ¤kursiv stil¤ och ¤¤fet stil¤¤. Du kan även ha ekvationer:\n\n\$v^{ut}_{B} = v^{in}_{A}\hat{n}\$", "code" => array());
  $project[0]['code'][0] = array("title" => "Kodruta 1", "code" => "//Massa kod");

  do
  {
    $privateID = rand_string();
  } while (get_project_by_privateID($privateID));
      
  do
  {
    $publicID = rand_string();
  } while (get_project_by_publicID($publicID));
      
  $statement =
    $db_projects->prepare('INSERT INTO Projects (privateID, publicID, updated, created, data)
                            VALUES (?, ?, ?, ?, ?)');
  
  $statement->execute(array($privateID, $publicID, time(), time(), json_encode($project)));
  
  return $privateID;
}

?>