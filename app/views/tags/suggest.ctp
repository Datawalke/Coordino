<?php

	echo 'var tags = [';
	$count = count($tags);
	$i = 1;
	foreach($tags as $tag) {
		if($i != $count) {
			echo ' { name: "' . $tag['tags']['tag'] . '", count: "' . $tag[0]['count'] . '" }, ';
		} else {
			echo  '{ name: "' . $tag['tags']['tag'] . '", count: "' . $tag[0]['count'] . '" }';
		}
		$i++;
	}
	echo '];'



?>
