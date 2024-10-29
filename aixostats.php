<?php
/*
Plugin Name: aiXoStats
Plugin URI: http://www.aixo.fr/aixostats/
Description: Gérez tous vos services de statistiques simplement.
Version: 1.2.1
Author: Ek0
Author URI: http://www.aixo.fr
*/
/*  Copyright 2008  Ek0  (email : Ek0@aixo.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* DETECTION DES DONNEES DU VISITEUR */
function aixostats_detectuser(){
	global $aixostats_username;
	global $aixostats_email;
	global $aixostats_userlevel;

	$author = str_replace("\"","\\\"",$_COOKIE['comment_author_'.COOKIEHASH]);
	$email = str_replace("\"","\\\"",$_COOKIE['comment_author_email_'.COOKIEHASH]);
	if (!empty($author)) {
		$aixostats_username = $author;
		$aixostats_email = $email;
	}
	else {
		$aixostats_username = NULL;
	}

	if (is_user_logged_in()) {
		global $userdata;
		get_currentuserinfo();
		$aixostats_username = $userdata->user_login;
		$aixostats_email = $userdata->user_email;
		$aixostats_userlevel = $userdata->user_level;
	}
}


/* ECRITURE DU FOOTER*/
function aixostats_ajouterfooter(){
	global $aixostats_username;
	global $aixostats_email;
	global $aixostats_userlevel;
	
	/* Banissement d'une IP*/
	$ip = get_option('aixostats_banned_ip');
	$ip_activer = get_option('aixostats_banned_ip_activer');
	
	if($ip_activer == 'on' && strcmp($_SERVER['REMOTE_ADDR'], $ip) == 0){
  		 echo '<!-- [aiXoStats] Votre adresse IP ('.$ip.') est bannie des statistiques du site.-->';
  	}
  	else{
  	
  		/*Banissement des admins*/
  		$ban_admins_activer = get_option('aixostats_ban_admins_activer');
		
  		if($ban_admins_activer == 'on' && $aixostats_userlevel == '10') {
            echo '<!-- [aiXoStats] Les administrateurs loggés du site sont bannis des statistiques. -->';
      }
  		else{
      		/*Paramètres généraux*/
      		$auto_tag_commentators = get_option('aixostats_auto_tag_commentators');
      		
        	/*Google analytics*/
        	$google_activer = get_option('aixostats_google_activer');
        	if ($google_activer == 'on'){
			    $google_id = get_option('aixostats_google_id');
        		if ($google_id == NULL || $google_id == '') {
        			echo '<!-- [aiXoStats] Votre ID Google Analytics n\'est pas configuré.-->';
        		}else{
        			echo '<!-- [aiXoStats] Google Analytics-->';
        			echo "<script type=\"text/javascript\">\r\n";
        			echo "var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\r\n";
        			echo "document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\r\n";
        			echo "</script>\r\n";
        			echo "<script type=\"text/javascript\">\r\n";
        			echo "var pageTracker = _gat._getTracker(\"".$google_id."\");\r\n";
        			echo "pageTracker._initData();\r\n";
        			echo "pageTracker._trackPageview();\r\n";
        			echo "</script>\r\n";
        		}
        	}
  				
  			/* Woopra */
        	$woopra_activer = get_option('aixostats_woopra_activer');	
        	if ($woopra_activer == 'on'){
				$woopra_id = get_option('aixostats_woopra_id');
        		if ($woopra_id == NULL || $woopra_id == '') {
        			echo '<!-- [aiXoStats] Votre ID Woopra n\'est pas configuré.-->';
        		}else{
        			echo '<!-- [aiXoStats] Woopra-->';						
					echo "<script type=\"text/javascript\">\r\n";
					echo "woopra_id = '".$woopra_id."';\r\n";
					if ($auto_tag_commentators == 'on' && $aixostats_username != NULL) {
						echo "var woopra_array = new Array();\r\n";
						echo "woopra_array['name'] = '".$aixostats_username."';\r\n";
						echo "woopra_array['Email'] = '".$aixostats_email."';\r\n";
						echo "woopra_array['avatar'] = 'http://www.gravatar.com/avatar.php?gravatar_id=" . md5(strtolower($aixostats_email))."&size=60&default=http%3A%2F%2Fstatic.woopra.com%2Fimages%2Favatar.png';\r\n";
					}
					echo "</script>\r\n";
					echo "<script src=\"http://static.woopra.com/js/woopra.js\" type=\"text/javascript\"></script>\r\n";
				}
			}
					
  			/* reInvigorate*/
        	$reinvigorate_activer = get_option('aixostats_reinvigorate_activer');					
        	if ($reinvigorate_activer == 'on'){
				$reinvigorate_id = get_option('aixostats_reinvigorate_id');
        		if ($reinvigorate_id == NULL || $reinvigorate_id == '') {
        			echo '<!-- [aiXoStats] Votre ID reInvigorate n\'est pas configuré.-->';
        		}else{
        			echo '<!-- [aiXoStats] reInvigorate-->';				
					echo "<script type=\"text/javascript\" src=\"http://include.reinvigorate.net/re_.js\"></script>\r\n";
					echo "<script type=\"text/javascript\">\r\n";
					echo "//<![CDATA[\r\n";
					if ($auto_tag_commentators == 'on' && $aixostats_username != NULL) {
						 echo "var re_name_tag = \"".$aixostats_username."\";\r\n";
						 echo "var re_context_tag = \"mailto:".$aixostats_email."\";\r\n";
					}
					echo "re_(\"".$reinvigorate_id."\");\r\n";
					echo "//]]>\r\n";
					echo "</script>\r\n";
				}
			}

  			/* Sitemeter*/
        	$sitemeter_activer = get_option('aixostats_sitemeter_activer');
        	if ($sitemeter_activer == 'on'){
				$sitemeter_id1 = get_option('aixostats_sitemeter_id1');
        		if ($sitemeter_id1 == NULL || $sitemeter_id1 == '') {
        			echo '<!-- [aiXoStats] Votre ID Sitemeter n\'est pas configuré.-->';
        		}else{
					$sitemeter_id2 = get_option('aixostats_sitemeter_id2');
        			echo '<!-- [aiXoStats] Sitemeter-->';							
					echo "<script type=\"text/javascript\" src=\"http://".$sitemeter_id1.".sitemeter.com/js/counter.js?site=".$sitemeter_id1.$sitemeter_id2."\">\r\n";
					echo "</script>\r\n";
					echo "<noscript>\r\n";
					echo "<a href=\"http://".$sitemeter_id1.".sitemeter.com/stats.asp?site=".$sitemeter_id1.$sitemeter_id2."\" target=\"_top\">\r\n";
					echo "<img src=\"http://".$sitemeter_id1.".sitemeter.com/meter.asp?site=".$sitemeter_id1.$sitemeter_id2."\" alt=\"Site Meter\" border=\"0\"/></a>\r\n";
					echo "</noscript>\r\n";
				}
			}

  			/* Clicky*/
        	$clicky_activer = get_option('aixostats_clicky_activer');
			if ($clicky_activer == 'on'){
				$clicky_id = get_option('aixostats_clicky_id');
        		if ($clicky_id == NULL || $clicky_id == '') {
        			echo '<!-- [aiXoStats] Votre ID Clicky n\'est pas configuré.-->';
        		}else{
        			echo '<!-- [aiXoStats] Clicky-->';					
					echo "<script src=\"http://static.getclicky.com/".$clicky_id.".js\" type=\"text/javascript\"></script>\r\n";
					echo "<noscript><p><img alt=\"Clicky\" src=\"http://in.getclicky.com/".$clicky_id."-db1.gif\" /></p></noscript>\r\n";
				}
			}
  		}
	}
}

/* MENU D'ADMINISTRATION WORDPRESS */
function aixostats_menuadmin(){
	add_options_page('aiXoStats', 'aiXoStats', 8, __FILE__, 'aixostats_affichermenuadmin');
}

function aixostats_affichermenuadmin(){
	?>
	<div style="margin: 30px; padding: 20px; border: 1px solid #ccc;">
 	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<h2>Configuration d'aiXoStats</h2>
		<p>Pour plus d'informations et de l'aide, visitez <a href="http://www.aixo.fr/aixostats">la page officielle du plugin aiXoStats.</a><br/>aiXoStats est un plugin développé par <a href="http://www.aixo.fr/contact">Ek0</a> pour <a href="http://www.aixo.fr">aiXo.fr</a>.</p>
	    <table border="0" cellpadding="4" cellspacing="4" width="400">
	        <tr>
	          <td align="left"><strong>Options générales</strong></td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_auto_tag_commentators">Tagger automatiquement les utilisateurs connus</label></td>
	          <td align="left"><input type="checkbox" name="aixostats_auto_tag_commentators" id="aixostats_auto_tag_commentators" <?php echo (get_option('aixostats_auto_tag_commentators')=='on')?"checked":""; ?> /><br/>		</td>
	        </tr>
	    	<tr>
	          <td align="right"><label for="aixostats_ban_admins_activer">Bannir les administrateurs du site des statistiques</label></td>
	          <td align="left"><input type="checkbox" name="aixostats_ban_admins_activer" id="aixostats_ban_admins_activer" <?php echo (get_option('aixostats_ban_admins_activer')=='on')?"checked":""; ?> /><br/></td>
	        </tr>
	    	<tr>
	          <td align="right"><label for="aixostats_banned_ip_activer">Bannir votre adresse IP des statistiques</label></td>
	          <td align="left"><input type="checkbox" name="aixostats_banned_ip_activer" id="aixostats_banned_ip_activer" <?php echo (get_option('aixostats_banned_ip_activer')=='on')?"checked":""; ?> /><br/></td>
	        </tr>
	    	<tr>
	          <td align="right"><label for="aixostats_banned_ip">Adresse IP à bannir (votre adresse IP courante est <i><?php echo $_SERVER['REMOTE_ADDR']; ?></i>)</label></td>
	          <td align="left"><input type="text" name="aixostats_banned_ip" id="aixostats_banned_ip" value="<?php echo get_option('aixostats_banned_ip'); ?>"/><br/></td>
	        </tr>
	    	<tr>
	          <td align="left"><strong>Google Analytics</strong></td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_google_activer">Activer Google Analytics</label></td>
	          <td align="left"><input type="checkbox" name="aixostats_google_activer" id="aixostats_google_activer" <?php echo (get_option('aixostats_google_activer')=='on')?"checked":""; ?> /><br/>	</td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_google_id">Votre ID Google Analytics (sous la forme UA-XXXXXXX-X)</label></td>
	          <td align="left"><input type="text" name="aixostats_google_id" id="aixostats_google_id" value="<?php echo get_option('aixostats_google_id'); ?>"/><br/></td>
	        </tr>
	    	<tr>
	          <td align="left"><strong>Woopra</strong></td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_woopra_activer">Activer Woopra</label></td>
	          <td align="left"><input type="checkbox" name="aixostats_woopra_activer" id="aixostats_woopra_activer" <?php echo (get_option('aixostats_woopra_activer')=='on')?"checked":""; ?> /><br/>	</td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_woopra_id">Votre ID Woopra</label></td>
	          <td align="left"><input type="text" name="aixostats_woopra_id" id="aixostats_woopra_id" value="<?php echo get_option('aixostats_woopra_id'); ?>"/><br/></td>
	        </tr>
	    	<tr>
	          <td align="left"><strong>reInvigorate</strong></td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_reinvigorate_activer">Activer reInvigorate</label></td>
	          <td align="left"><input type="checkbox" name="aixostats_reinvigorate_activer" id="aixostats_reinvigorate_activer" <?php echo (get_option('aixostats_reinvigorate_activer')=='on')?"checked":""; ?> /><br/>	</td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_reinvigorate_id">Votre ID reInvigorate</label></td>
	          <td align="left"><input type="text" name="aixostats_reinvigorate_id" id="aixostats_reinvigorate_id" value="<?php echo get_option('aixostats_reinvigorate_id'); ?>"/><br/></td>
	        </tr>
	    	<tr>
	          <td align="left"><strong>Sitemeter</strong></td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_sitemeter_activer">Activer Sitemeter</label></td>
	          <td align="left"><input type="checkbox" name="aixostats_sitemeter_activer" id="aixostats_sitemeter_activer" <?php echo (get_option('aixostats_sitemeter_activer')=='on')?"checked":""; ?> /><br/>	</td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_sitemeter_id1">Votre ID Sitemeter (partie imposée, exemple: <i>s48</i>)</label></td>
	          <td align="left"><input type="text" name="aixostats_sitemeter_id1" id="aixostats_sitemeter_id1" value="<?php echo get_option('aixostats_sitemeter_id1'); ?>"/><br/></td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_sitemeter_id2">Votre ID Sitemeter (partie personnelle)</label></td>
	          <td align="left"><input type="text" name="aixostats_sitemeter_id2" id="aixostats_sitemeter_id2" value="<?php echo get_option('aixostats_sitemeter_id2'); ?>"/><br/></td>
	        </tr>
			<tr>
	          <td align="left"><strong>Clicky</strong></td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_clicky_activer">Activer Clicky</label></td>
	          <td align="left"><input type="checkbox" name="aixostats_clicky_activer" id="aixostats_clicky_activer" <?php echo (get_option('aixostats_clicky_activer')=='on')?"checked":""; ?> /><br/>	</td>
	        </tr>
	        <tr>
	          <td align="right"><label for="aixostats_clicky_id">Votre ID Clicky</label></td>
	          <td align="left"><input type="text" name="aixostats_clicky_id" id="aixostats_clicky_id" value="<?php echo get_option('aixostats_clicky_id'); ?>"/><br/></td>
	        </tr>
	        <tr>
				<td align="right"></td>
	          <td align="left"><p class="submit"><input type="submit" name="Submit" value="Enregistrer" /></p></td>
	        </tr>
	    </table>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="aixostats_auto_tag_commentators, aixostats_ban_admins_activer, aixostats_banned_ip_activer, aixostats_banned_ip, aixostats_google_activer, aixostats_google_id, aixostats_woopra_activer, aixostats_woopra_id, aixostats_reinvigorate_activer, aixostats_reinvigorate_id, aixostats_sitemeter_activer, aixostats_sitemeter_id1, aixostats_sitemeter_id2, aixostats_clicky_activer, aixostats_clicky_id" />
	</form>
</div>
<?php }

/* Définition des Hooks*/
add_action('admin_menu', 'aixostats_menuadmin');
add_action('template_redirect', 'aixostats_detectuser');
add_action('wp_footer', 'aixostats_ajouterfooter');

?>
