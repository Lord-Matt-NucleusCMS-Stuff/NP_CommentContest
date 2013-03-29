<?php
/*
Nucleus CMS Comment Contest Plugin V0.1.1
(C) 2007 Lord Matt

Version history
  v0.1.1 Tidy up code for public release
	v0.1.0 First release 

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
Contact me : letters@lordmatt.co.uk
You may use and distribute this freely as long as you leave the copyrights intact.

*/
class NP_CommentContest extends NucleusPlugin {
	function getName() {return 'Comment Contest';}
	function getAuthor() {return 'Lord Matt';}
	function getURL() {return 'http://lordmatt.co.uk/';}
	function getVersion() {return '0.1.1';}
	
	function getDescription() {
		return 'Allows you to turn selected comments into contest entries for any given item.  Add CommentContest as a  template var on an item.';
	}
             
	function doTemplateVar($item){
		global $member;	
		if (($member->isAdmin()===True) AND ($member->isLoggedIn())){
			echo ' <small> 
			<p><a href="action.php?action=plugin&name=CommentContest&type=start&id=' . $item->itemid . '">CONTEST?</a></p> 
			</small>';
		}
	}


	function doAction($actionType) {
		global $member;	
	if (!$member->isLoggedIn()){
		return "Sorry this is an admin only area!";
	}
	echo "
	<style>
	body{padding:0;margin:0;}
	</style>
	<div style='padding:0;width:100%;height:50px;background-image: url(http://static.flickr.com/109/285685940_3bb6f39a6f_s_d.jpg);'>
		<img src='http://static.flickr.com/109/285685940_3bb6f39a6f_s_d.jpg' width='1' hieght='75' /></div>
	<div style='padding:25%;padding-top:25px;padding-bottom:25px;'>
		";
	
		if (($actionType == 'start') AND ($member->isAdmin())){
	
			// it would have been nice to have API to do this for me.
			$query =  'SELECT c.citem as itemid, c.cnumber as commentid, c.cbody as body, c.cuser as user, c.cmail as userid, c.cmember as memberid, c.ctime, c.chost as host, c.cip as ip, c.cblog as blogid'
				   . ' FROM '.sql_table('comment').' as c'
				   . ' WHERE c.citem=' . getvar('id')
				   . ' ORDER BY c.ctime';

			$comments = sql_query($query);
			$commentcount = mysql_num_rows($comments);
			
			echo '
				<form style="padding:5%;" action="action.php?action=plugin&name=CommentContest&type=process" method="post">
				<p>Ok then, which of these comments are valid entries</p>
				<p style="font-size:85%;padding:5%;"><em>Be aware the data is not at all checked so be sure you trust all your site admins</em></p>
			';
			 
			while ($row = mysql_fetch_assoc($comments)){
				echo '<div id="c_' . $row['commentid'] .'" style="padding:12px;border:1px dotted blue;">';
				echo '<p><input type="checkbox" value="' . $row['commentid'] . '" name="comments[]"> Check to add this one to the draw<br />';
				echo $row['userid'] . ' <br /> Member ID #' . $row['memberid'] . ' <br /> ' . $row['user'] . ' <br /> '  . $row['ip'] . ' <br /> ' . $row['body'] .   '</p>';
				echo '</div>';
			}

			echo '
				<input type="submit" name="submit" value="submit">
				</form>
			';
			
			//$content = COMMENT::getComment(getvar('id'));
			
		}elseif(($actionType == 'process') AND ($member->isAdmin())){
		
		$allcomments = $_POST['comments'];
		
		//pick a winner
		$winnerpos = rand(0,count($allcomments)-1);
		
		$content = COMMENT::getComment($allcomments[$winnerpos]);
		
		// From COMMENTS.php from NucleusCMS core
		// create smart links
		if (isValidMailAddress($content['userid'])){
			$content['userlinkraw'] = 'mailto:'.$content['userid'];
		}elseif (strstr($content['userid'],'http://') != false){
			$content['userlinkraw'] = $content['userid'];
		}elseif (strstr($content['userid'],'www') != false){
			$content['userlinkraw'] = 'http://'.$content['userid'];
		}
		echo "<p>The winner is <A href='" . $content['userlinkraw'] . "'>" . $content['user'] . " (well done?)</a></p>";
		echo "<p>Winning poster:</p>";
		echo '<p>Member ID #' . $row['memberid'] . ' <br /> '  . $content['userid'] . ' <br /> '  . $content['user'] . ' <br /> '  . $content['ip'] . ' <br /> ' . $content['body'] .   '</p>';
		
		}else{
			return 'invalid action';
		}
		echo "</div><hr /><p>Comment Contest by <a href='http://lordmatt.co.uk'>Lord Matt</a> image by <a href='http://flickr.com/photo_zoom.gne?id=285685940&size=sq'>aid85</a></p>";
	}

}
