<?php
namespace Model;
use \DB;

// Todo DB Skeleton Code

class HospitalListModel extends \Model {

  public static function HospitalListDataCount(){
    return DB::query("SELECT COUNT(*) FROM HospitalList", DB::SELECT)->execute()->as_array();
  }
  public static function HospitalListData($limit_result, $numPages){
    return DB::query("SELECT Name, State, ID FROM HospitalList LIMIT $limit_result, 20", DB::SELECT)->execute()->as_array();
  }
  public static function uid($usrnm){
    return DB::query("SELECT id FROM users WHERE username = '$usrnm'", DB::SELECT)->execute()->as_array();
  }
  public static function add_new_hospital_comment($ID, $MPN, $Aut, $likes = 0, $dislikes = 0, $ctext){
    return DB::insert('UserComments')->columns(array(
      'CUID', 'ProviderID', 'CID', 'Author', 'likes', 'dislikes', 'UComment', 'CreateDate', 'EditDate'
    ))->values(array(
      $ID, $MPN, DB::expr('DEFAULT'),  $Aut, $likes, $dislikes, $ctext, DB::expr('DEFAULT'), 0
    ))->execute();
  }
  public static function add_new_hospital_subcomment($ID, $MPN, $ParentID, $Aut, $likes = 0, $dislikes = 0, $ctext){
    return DB::insert('SubComment')->columns(array(
      'SCID', 'SCUID', 'ProviderID', 'ParentID', 'SAuthor', 'Slikes', 'Sdislikes', 'SUComment', 'CreateDate', 'EditDate'
    ))->values(array(
      DB::expr('DEFAULT'), $ID, $MPN, $ParentID, $Aut, $likes, $dislikes, $ctext, DB::expr('DEFAULT'), 0
    ))->execute();
  }
  public static function fetch_comment($ID){
    return DB::query("SELECT CID, Author, UComment, CreateDate, EditDate, likes FROM UserComments WHERE ProviderID=$ID",DB::SELECT)->execute()->as_array();
  }
  public static function fetch_sub_comment($ID){
    return DB::query("SELECT SAuthor, SUComment, ParentID, CreateDate, EditDate FROM SubComment WHERE ProviderID=$ID",DB::SELECT)->execute()->as_array();
  }
  public static function edit_Pcomment($ID, $text){
    return  DB::update('UserComments')->set(array('UComment' => $text,))->where('CID', $ID)->execute();
  }
 }
