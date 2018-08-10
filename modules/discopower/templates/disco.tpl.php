<?php

$this->data['header'] = $this->t('selectidp');
$this->data['jquery'] = array('core' => TRUE, 'ui' => TRUE, 'css' => TRUE);

$this->data['head'] = '<link rel="stylesheet" media="screen" type="text/css" href="' . SimpleSAML\Module::getModuleUrl('discopower/style.css')  . '" />';

$this->data['post'] = '<script type="text/javascript" src="' . SimpleSAML\Module::getModuleUrl('discopower/js/jquery.livesearch.js')  . '"></script>';
$this->data['post'] .= '<script type="text/javascript" src="' . SimpleSAML\Module::getModuleUrl('discopower/js/quicksilver.js')  . '"></script>';




if (!empty($this->data['faventry'])) $this->data['autofocus'] = 'favouritesubmit';

$this->includeAtTemplateBase('includes/header.php');

function showEntry($t, $metadata, $favourite = FALSE) {
	
	$basequerystring = '?' . 
		'entityID=' . urlencode($t->data['entityID']) . '&amp;' . 
		'return=' . urlencode($t->data['return']) . '&amp;' . 
		'returnIDParam=' . urlencode($t->data['returnIDParam']) . '&amp;idpentityid=';
	
	$extra = ($favourite ? ' favourite' : '');
	$html = '<a class="metaentry' . $extra . '" href="' . $basequerystring . urlencode($metadata['entityid']) . '">';
	
	$html .= '' . htmlspecialchars(getTranslatedName($t, $metadata)) . '';

	if(array_key_exists('icon', $metadata) && $metadata['icon'] !== NULL) {
		$iconUrl = \SimpleSAML\Utils\HTTP::resolveURL($metadata['icon']);
		$html .= '<img alt="Icon for identity provider" class="entryicon" src="' . htmlspecialchars($iconUrl) . '" />';
	}

	$html .= '</a>';
	
	return $html;
}

?>




<?php

function getTranslatedName($t, $metadata) {
	if (isset($metadata['UIInfo']['DisplayName'])) {
		$displayName = $metadata['UIInfo']['DisplayName'];
		assert(is_array($displayName)); // Should always be an array of language code -> translation
		if (!empty($displayName)) {
			return $t->getTranslator()->getPreferredTranslation($displayName);
		}
	}

	if (array_key_exists('name', $metadata)) {
		if (is_array($metadata['name'])) {
			return $t->getTranslator()->getPreferredTranslation($metadata['name']);
		} else {
			return $metadata['name'];
		}
	}
	return $metadata['entityid'];
}




if (!empty($this->data['faventry'])) {


	echo('<div class="favourite">');
	echo($this->t('previous_auth'));
	echo(' <strong>' . htmlspecialchars(getTranslatedName($this, $this->data['faventry'])) . '</strong>');
	echo('
	<form id="idpselectform" method="get" action="' . $this->data['urlpattern'] . '">
		<input type="hidden" name="entityID" value="' . htmlspecialchars($this->data['entityID']) . '" />
		<input type="hidden" name="return" value="' . htmlspecialchars($this->data['return']) . '" />
		<input type="hidden" name="returnIDParam" value="' . htmlspecialchars($this->data['returnIDParam']) . '" />
		<input type="hidden" name="idpentityid" value="' . htmlspecialchars($this->data['faventry']['entityid']) . '" />

		<input type="submit" name="formsubmit" id="favouritesubmit" value="' . $this->t('login_at') . ' ' . htmlspecialchars(getTranslatedName($this, $this->data['faventry'])) . '" /> 
	</form>');

	echo('</div>');
}


?>






<div id="tabdiv"> 

    <ul class="tabset_tabs">     
    	<?php
    	
    		$tabs = array_keys( $this->data['idplist']);
                $i = 1;
    		foreach ($tabs AS $tab) {
			if(!empty($this->data['idplist'][$tab])) {
                                if ($i === 1) {
					echo '<li class="tab-link current" data-tab="'.$tab.'"><a href="#' . $tab . '"><span>' . $this->t($this->data['tabNames'][$tab]) . '</span></a></li>';
				} else {
					echo '<li class="tab-link" data-tab="'.$tab.'"><a href="#' . $tab . '"><span>' . $this->t($this->data['tabNames'][$tab]) . '</span></a></li> ';
				}
				$i++;
			}
    		}
    	
    	?>
    </ul> 
    

<?php




foreach( $this->data['idplist'] AS $tab => $slist) {
        $first = array_keys($this->data['idplist']);
        if ($first[0] === $tab) {
	    echo '<div id="' . $tab . '" class="tabset_content current">';
        } else {
	    echo '<div id="' . $tab . '" class="tabset_content">';
        }	
	if (!empty($slist)) {

		echo('	<div class="inlinesearch">');
		echo('	<p>Incremental search...</p>');
		echo('	<form id="idpselectform" action="?" method="get"><input class="inlinesearch" type="text" value="" name="query_' . $tab . '" id="query_' . $tab . '" /></form>');
		echo('	</div>');
	
		echo('	<div class="metalist" id="list_' . $tab  . '">');
		if (!empty($this->data['preferredidp']) && array_key_exists($this->data['preferredidp'], $slist)) {
			$idpentry = $slist[$this->data['preferredidp']];
			echo (showEntry($this, $idpentry, TRUE));
		}

		foreach ($slist AS $idpentry) {
			if ($idpentry['entityid'] != $this->data['preferredidp']) {
				echo (showEntry($this, $idpentry));
			}
		}
		echo('	</div>');
	}
	echo '</div>';

}
	
?>



</div>

<script type="text/javascript">
$(document).ready(function() {
<?php
$i = 0;
foreach ($this->data['idplist'] AS $tab => $slist) {
	echo "\n" . '$("#query_' . $tab . '").liveUpdate("#list_' . $tab . '")' .
		(($i++ == 0) && (empty($this->data['faventry'])) ? '.focus()' : '') .
		';';


}
?>
});

</script>

<?php
$this->data['post'] .= '<script type="text/javascript" src="' . SimpleSAML\Module::getModuleUrl('discopower/js/javascript.js') . '"></script>';
$this->includeAtTemplateBase('includes/footer.php');
