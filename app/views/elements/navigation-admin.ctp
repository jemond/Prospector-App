<?php
$navigation = array(
	'Accounts' => '/admins/accounts'
	, 'Invoices' => '/admins/invoices'
	, 'Plans' => '/admins/plans'
	, 'System Logs' => '/admins/systemlogs'
	, 'Debug Logs' => '/admins/debuglogs'
	, 'Logout' => '/admins/logout'
	
);
 
$matchingLinks = array();
foreach ($navigation as $link) {
  if (preg_match('/^'.preg_quote($link, '/').'/', substr($this->here, strlen($this->base)))) {
    $matchingLinks[strlen($link)] = $link;
  }
}
krsort($matchingLinks);
$activeLink = ife(!empty($matchingLinks), array_shift($matchingLinks));
 
$out = array();
foreach ($navigation as $title => $link) {
  $out[] = $html->link($title, $link, ife($link == $activeLink, array('style' => 'font-weight:bold; text-decoration:none;color:black')));
}
 
echo join("\n", $out);
?>