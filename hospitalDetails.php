<?php echo Asset::js('jquery-latest.min.js'); ?>
<?php echo Asset::js('jquery.tablesorter.js'); ?>
<?php echo Asset::js('jquery.tablesorter.widgets.js'); ?>
<table id="myTable" class="tablesorter t1">
  <thead>
  <tr>
  <th>DRG Number</th>
  <th>DRG Definition</th>
  <th>Average Covered Charges</th>
  <th>Average Total Payments</th>
  <th>Average Medicare Payments</th>
  </tr>
  </thead>
  <tbody>

    <?php
    $CurID = $TargetInfo[0]['ID'];
      echo '<h3 style="text-align:center;"> MPN: '.$CurID." - ".$TargetInfo[0]['Name']."</h3>";
      echo '<h4 style="text-align:center;">'.$TargetInfo[0]['StreetAddress']." - ".$TargetInfo[0]['City']." - ".$TargetInfo[0]['State']."</h4>";
      foreach($hospitalInfo as $hospital){
        $DRGnum = $hospital['DRGNumber'];
        $DRGdef = $hospital['DRGDefinition'];
        $ACC = $hospital['ACC'];
        $ATP = $hospital['ATP'];
        $AMP = $hospital['AMP'];
        $l = Uri::base() . "index/main/DRGInfo?drg=" .$DRGnum."&id=".$CurID;
        echo "<tr><td><a href='".$l."'>".$DRGnum.'</a></td><td>'.$DRGdef.'</td><td>'.$ACC.'</td><td>'.$ATP.'</td><td>'.$AMP.'</td></tr>';
      }

    ?>
</tbody>
</table>
<?php
$results_per_page = 20; // Limit data per page
$number_of_results = $ResultCount[0]['COUNT(*)']; // Total data number
$this_page =  "./hospitalDetails.php";

 if(!isset($_GET['page']) || !$page = intval($_GET['page'])) {
    $page = 1;
  }

  // extra variables to append to navigation links (optional)
  $linkextra = [];
  if(isset($_GET['var1']) && $var1 = $_GET['var1']) { // repeat as needed for each extra variable
    $linkextra[] = "var1=" . urlencode($var1);
  }
  $linkextra = implode("&amp;", $linkextra);
  if($linkextra) {
    $linkextra .= "&amp;";
  }

  // build array containing links to all pages
  $tmp = [];
  for($p=1, $i=0; $i < $number_of_results; $p++, $i += $results_per_page) {
    if($page == $p) {
      // current page shown as bold, no link
      $tmp[] = "<b>{$p}</b>";
    } else {
      $tmp[] = "<a href=\"{$this_page}?{$linkextra}page={$p}&uri={$segment}\">{$p}</a>";
    }
  }

  // thin out the links (optional)
  for($i = count($tmp) - 3; $i > 1; $i--) {
    if(abs($page - $i - 1) > 2) {
      unset($tmp[$i]);
    }
  }

  // display page navigation iff data covers more than one page
  if(count($tmp) > 1) {
    echo "<p>";

    if($page > 1) {
      // display 'Prev' link
      echo "<a href=\"{$this_page}?{$linkextra}page=" . ($page - 1) . "&uri=".$segment."\">&laquo; Prev</a> | ";
    } else {
      echo "Page ";
    }

    $lastlink = 0;
    foreach($tmp as $i => $link) {
      if($i > $lastlink + 1) {
        echo " ... "; // where one or more links have been omitted
      } elseif($i) {
        echo " | ";
      }
      echo $link;
      $lastlink = $i;
    }

    if($page <= $lastlink) {
      // display 'Next' link
      echo " | <a href=\"{$this_page}?{$linkextra}page=" . ($page + 1) . "&uri=".$segment."\">Next &raquo;</a>";
    }

    echo "</p>\n\n";
  }
?>
</table>

<?php
echo '<div class="commentSectionContainer">';
if(isset($_SESSION['username'])){
  echo Form::open(array(
      'action' => '/index/main/new_comment',
      'method' => 'post'
  ));
  echo' <p>Create a New Commentfor MPN '.$CurID.': </p>
  <div class="CtextAreaParent"><textarea name="comment_content" id="comment_content" class="CTextArea" placeholder="Enter Comment" rows="5"></textarea></div>
  <div class = "CSubmit"><input type="hidden" name="MPN" id="MPN" value="'.$CurID.'">
  <input type="submit" name="submit" class="TopComSubmit" value="Submit"></div>';
echo Form::close();
}

echo '<div class="CommentSectionTitle"><p> User Comments for MPN '.$CurID.': </p></div>';
foreach($unit_info as $c){
  echo '<div class="commentContainer">';
  echo '
    <div class="updownContainer">
    <input type="submit" name="up" class="Upvote" value="&#708">
    <input type="submit" name="down" class="DownVote" value="&#709">
     </div>
  ';
  if($c['EditDate'] != 0) {
    echo '<div class="CUser"><strong>'.$c['Author'].'</strong> on: '.$c['CreateDate'].' | Edited on:'.$c['EditDate'].'</div>';
  }
  else{
    echo '<div class="CUser"><strong>'.$c['Author'].'</strong> on: '.$c['CreateDate'].'</div>';
  }
  echo '<div class="ComBlock"><p>'.$c['UComment'].'</p></div>';
  if(isset($_SESSION['username']) && $_SESSION['username'] == $c['Author']){
    echo Form::open(array(
      'action' => '/index/main/edit_comment',
      'method' => 'post'
    ));
    echo'
    <div class="CtextAreaParent"><textarea name="comment_content" id="comment_content" class="CTextArea" rows="5">'.$c['UComment'].'</textarea></div>
    <div class = "CSubmit"><input type="hidden" name="CID" id="CID" value="'.$c['CID'].'">
    <input type="submit" name="edit" class="TopComSubmit" value="Submit Edit &#9998"><input type="submit" name="delete" class="TopComSubmit" value="Delete Comment &#8855"></div>';
    echo Form::close();
  }
  else if(isset($_SESSION['username'])){
    echo Form::open(array(
      'action' => '/index/main/sub_comment',
      'method' => 'post'
    ));
    echo'
    <div class="CtextAreaParent"><textarea name="comment_content" id="comment_content" class="CTextArea" placeholder="Reply to: '.$c['Author'].'"rows="5"></textarea></div>
    <div class = "CSubmit"><input type="hidden" name="MPN" id="MPN" value="'.$CurID.'"><input type="hidden" name="CID" id="CID" value="'.$c['CID'].'">
    <input type="submit" name="submit" class="TopComSubmit" value="Reply"></div>';
    echo Form::close();
  }
  echo '<div class="SubCommentContainer">';
  foreach($sub_comments as $s){
    if($s['ParentID'] == $c['CID']){
      echo '<div class="SubcommentBlock">';
      if($s['EditDate'] != 0) {
       echo '<div class="CUser"><strong>'.$s['SAuthor'].'</strong> on: '.$s['CreateDate'].' | Edited on:'.$s['EditDate'].'</div>';
      }
      else{
        echo '<div class="CUser"><strong>'.$s['SAuthor'].'</strong> on: '.$s['CreateDate'].'</div>';
      }
      echo '<div class="ComBlock"><p>'.$s['SUComment'].'</p></div>';
      if(isset($_SESSION['username']) && $_SESSION['username'] == $s['SAuthor']){
        echo Form::open(array(
          'action' => '/index/main/edit_scomment',
          'method' => 'post'
        ));
        echo'
        <div class="CtextAreaParent"><textarea name="comment_content" id="comment_content" class="CTextArea" rows="5">'.$s['SUComment'].'</textarea></div>
        <div class = "CSubmit"><input type="hidden" name="MPN" id="MPN" value="'.$CurID.'">
        <input type="submit" name="edit" class="TopComSubmit" value="Submit Edit &#9998"><input type="submit" name="delete" class="TopComSubmit" value="Delete Comment &#8855"></div>';
      echo Form::close();
      }
      echo'</div>';
    }
  }
  echo'</div>';
  echo'</div>';
}

echo'</div>';
echo '</div>';
?>

<script>
function Vote() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        UpdateVotes();
    };
    xmlhttp.open("GET", "/index/main/increment_likes_controller");
    xmlhttp.send();
  }
}
function UpdateVotes(){
  xmlhttp.onreadystatechange = function() {
  if (this.readyState == 4 && this.status == 200) {
        document.getElementById("txtHint").innerHTML = this.responseText;
  };
  xmlhttp.open("GET", "/index/main/controller_to_get_vote_count?CID=" //Comments CID );
  xmlhttp.send();
}

</script>
<form action="">
  <input type="button" id="up" name="up" value="+1"onkeyup="Vote()">
</form>

<script>
$(function() {
    $("#myTable").tablesorter();
});
</script>
