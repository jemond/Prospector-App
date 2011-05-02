<?php
$navigation = array(
	'Home' => '/'
	, 'Features' => '/features'
	, 'Pricing' => '/pricing'
	, 'Sign Up' => '/signup'
	, 'Login' => '/login'
	, 'Support'=> '/support'
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
  $out[] = $html->link($title, $link, ife($link == $activeLink, array('class' => 'active')));
}
 
echo join("\n", $out);
?>