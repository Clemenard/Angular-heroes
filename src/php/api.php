<?php $db  = new PDO(
  'mysql:host=localhost;dbname=beewake',
  'root',
  'root',
  array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  )
);
$setFighter=new FighterModel($db);

abstract class Fighter{

  public $nom;
  public $vie;
  public $liste_attaques=["poing","pied"];
  public function __construct($nom,$vie){
    $this->nom = $nom;
    $this->vie = $vie;
  }
  public function frappe($coup,Fighter $target){
    if($this->vie == 0 || $target->vie == 0) return;
    switch($coup){
      case 'poing' : $damage = 1; $chance = 1; break;
      case 'pied' : $damage = 2; $chance = 4; break;
      case 'magie' : $damage = 4; $chance = 6; break;
      case 'hache' : $damage = 10; $chance = 2; break;
    }
    $succes = rand(1,10);
    if($succes > $chance){
      $target->vie -= $damage;
      if($target->vie<0) $target->vie=0;
      return $this->nom.' donne un coup de '.$coup.' et retire '.$damage.' point(s) de vie à '.$target->nom .'<br>';
    }
    else{
      return $target->nom.' esquive le coup de '.$coup.' porté par '.$this->nom.'<br>';
    }
  }
  public function winner(Fighter $target){
    if( $this->vie > $target->vie){
      return $this->nom.' remporte le combat !<br>';
    }
    elseif( $this->vie < $target->vie){
      return $target->nom.' remporte le combat !<br>';
    }
    else{
      return 'Match nul !<br>';
    }
  }

}

class Warrior extends Fighter{
  public $liste_attaques=["poing","pied","hache"];


}

class Wizard extends Fighter{

  public $liste_attaques=["poing","pied","magie"];

}

class FighterModel {

  protected $db;

public function __construct($db){$this->setDb($db);}

public function setDb(PDO $db)
{
$this->db = $db;}

public function getDb()
{
return $this->db;}

  function execRequest($req,$params=array()){
    $r = $this->db->prepare($req);
    if ( !empty($params) ){
      // sanatize et bindvalue
      foreach($params as $key => $value){
        $params[$key] = htmlspecialchars($value,ENT_QUOTES);
        $r->bindValue($key,$params[$key],PDO::PARAM_STR);
      }
    }
    $r->execute();
    if ( !empty( $r->errorInfo()[2] )){
      die('Request failed - please contact the administrator');
    }
    return $r;
  }

  public function getFighter($id){
$query = $this->execRequest("SELECT * FROM fighters  WHERE id_fighters= :id ",array('id'=> $id));
if( $query->rowCount() == 1 ){
  $query->setFetchMode(PDO::FETCH_CLASS,'UserInternEntity');
$user=$query->fetch();
return $user;
}
else {
    return false;
}
  }


  public function getAllFighters(){
    $query = $this->execRequest("SELECT * FROM fighters");
    if( $query->rowCount() > 0 ){
    $users=$query->fetchAll(PDO::FETCH_CLASS,'UserInternEntity');
    return $users;
    }
    else {
        return false;
    }
      }

  public function addFighter($infos){
    $this->execRequest('INSERT INTO fighters ('.implode(',', array_keys($infos)).') VALUES (:'.implode(', :', array_keys($infos)).')',$infos);
    return $this->getDb()->lastInsertId();
  }

  public function deleteFighter($id){
     $this->execRequest("DELETE FROM fighters WHERE id_fighters=:id",array('id' => $id));
  }

  public function updateFighter($id,$infos){
    foreach($infos as $key=>$values){
      $newValues[]="$key = :$key";
    }
  $infos['id']=$id;
    $this->execRequest('UPDATE fighters SET '.implode(',',$newValues) .' WHERE id_users = :id',$infos);
  }
  }
$setUser
  if(isset($_GET['action'] && $_GET['action']=='list')){
    $fighters=$setFighter->getallFighter();
    echo json_encode($fighters);
    }

?>
