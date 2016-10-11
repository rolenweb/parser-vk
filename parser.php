<?php

use tools\CurlClient;
use tools\hunspell;
//use tools\SymfonyParser;
//use Symfony\Component\DomCrawler\Link;
//use Symfony\Component\CssSelector\CssSelectorConverter;
use Simplon\Mysql\Mysql;
//use tiagoalessio\TesseractOCR;


require 'vendor/autoload.php';
require 'tools/CurlClient.php';
require 'tools/hunspell.php';
//require 'tools/SymfonyParser.php';
//
//
//$inputStr = "The sixth sick sheikhs sixth sheeeps sik";


$publics = file('list_public.txt');
if (empty($publics)) {
	error('The list of publics is null');
}
for (;;)
{
	foreach ($publics as $public) {
		success('Parse: '.$public);
		$parse = parseVk(rtrim($public));
		saveDatebase($parse,$public);
		info('Sleep 5 sec');
		sleep(5);
	}
	info('Sleep 15 min');
	sleep(900);
}

function parseVk($url)
{
	$client = new CurlClient();
	$content = $client->parsePage($url);
	if (empty($content)) {
		error('Content is null');
	}
	$text = $client->parseProperty($content,'string','div._wall_post_cont',$url = null,$attr = null);
	$style = $client->parseProperty($content,'attribute','div._wall_post_cont div.page_post_sized_thumbs a',$url = null,'style');
	$id = $client->parseProperty($content,'attribute','div._wall_post_cont',$url = null,'id');
	return spitArray($text,$style,$id);
}

function spitArray($arr1,$arr2,$arr3)
{
	$out = [];
	$path_pic = 'pics/pic.jpg';
	if (empty($arr1) || empty($arr2) || empty($arr3)) {
		error('Array is null');
		return;
	}
	foreach ($arr1 as $key => $value) {
		if (empty(rtrim($value)) === false) {
			$out[$key]['title'] = clearText(getTitle($value));
			$out[$key]['description'] = clearText(rtrim($value));
			$out[$key]['pic'] = (empty($arr2[$key]) === false) ? getPicUrl($arr2[$key]) : null;
			$out[$key]['id'] = (empty($arr3[$key]) === false) ? rtrim($arr3[$key]) : null;
		}else{
			if (empty($arr2[$key]) === false) {
				$url = getPicUrl($arr2[$key]);
				copy($url, $path_pic);
				$scan_pic = tesseract($path_pic,'rus');
				info($scan_pic);
				$check = checkName(clearText($scan_pic));
				info($check);
				if ($check === 'GOOD') {
					$out[$key]['title'] = clearText(getTitle($scan_pic));
					$out[$key]['description'] = null;
					$out[$key]['pic'] = getPicUrl($arr2[$key]);
					$out[$key]['id'] = (empty($arr3[$key]) === false) ? rtrim($arr3[$key]) : null;
				}
			}
		}
	}
	return $out;
}

function tesseract($pic,$lang = null)
{
	if (file_exists($pic) === false) {
		error('Pic is not found');
	}
	$tes = new TesseractOCR($pic);
	if ($lang !== null) {
		$tes->lang($lang);
	}
	return $tes->run();
}

function getPicUrl($string)
{
	if (empty($string)) {
		return;
	}
	preg_match('/url\((.*)\)/', $string, $match);
	if (empty($match[1])) {
		return;
	}
	return $match[1];
}

function getTitle($content)
{
	$out = '';
	if (empty($content)) {
		return $out;
	}
	$words = explode(' ', $content);
	if (empty($words)) {
		return $out;
	}
	foreach ($words as $key => $word) {
		if ($key < 5) {
			$out.= rtrim($word).' ';
		}
		
	}
	return rtrim($out);
}

function checkName($inputStr)
{
	if (empty($inputStr)) {
		return;
	}
	
	$inputStr1 = trim($inputStr);
	// Get total number of words in input string
	$wordCount  = count(explode(' ',$inputStr1));
	// initialise hunspell, defined in include
	$hunspell = new hunspell($inputStr1);
	$parseResponse = $hunspell->get();
	$errorCount = count($parseResponse);
	$errorPercent = ($errorCount/$wordCount) * 100;
	if ($errorPercent === 0){
	  return "BAD";
	}
	else {
	  return "GOOD";
	}
}

function clearText($string)
{
	$black = [
		'\r\n',
        '\r',
        '\n',
        '.',
        ',',
        '?',
        '!',
        "\"",
        '(',
        ')',
        '/',
        '/',
        ':',
        ';',
        '[',
        ']',
        '«',
        '»',
        '$',
        '“',
        '”',
        '„',
        '\'',
        '\\',


	];
    return str_replace($black, '',  $string);
} 

function saveDatebase($arr,$url)
{
	
	if (empty($arr)) {
		error('Array for save in database is null');
	}
	
	foreach ($arr as $key => $item) {
		info('Try to save post id: '.$item['id']);
		if (empty($item['id']) === false) {
			if (connectDb()->fetchColumn('SELECT id FROM post WHERE public_post_id = :id',[':id' => $item['id']]) === null) {
				$data = [];
				$data[0]['public_url'] = $url;
				$data[0]['public_post_id'] = $item['id'];
				$data[0]['title'] = $item['title'];
				$data[0]['text'] = $item['description'];
				$data[0]['created_at'] = time();
				$data[0]['updated_at'] = time();
				$post_id = insertTable('post',$data);
				if (empty($post_id[0]) === false) {
					success('Title, description are saved');
					$data = [];
					$data[0]['post_id'] = $post_id[0];
					$data[0]['url'] = $item['pic'];
					$data[0]['created_at'] = time();
					$data[0]['updated_at'] = time();
					
					$img_id = insertTable('image',$data);
					if (empty($img_id) === false) {
						success('Url image is saved');
					}else{
						error('Image is not saved');	
					}
				}else{
					error('Post is not saved');
				}
			}else{
				error('Post id: '.$item['id'].' is already saved');
			}
		}else{
			error('Post id is null');
		}
		
	}
}


function connectDb()
{
	require 'config/db.php';
	
	return new Mysql(
	    $config['host'],
	    $config['user'],
	    $config['password'],
	    $config['database']
	);
}

function insertTable($table,$data)
{
	return connectDb()->insertMany($table, $data);	
}

function updateTable($table, $condr, $data)
{
	connectDb()->update($table, $condr, $data);	
}

function setUp()
{
	$data = [
		[
			'table_name' => 'model',
			'created_at' => time(),
			'updated_at' => time(),
		],
		[
			'table_name' => 'frame',
			'created_at' => time(),
			'updated_at' => time(),
		],
		[
			'table_name' => 'complectation',
			'created_at' => time(),
			'updated_at' => time(),
		],
		[
			'table_name' => 'parts_group',
			'created_at' => time(),
			'updated_at' => time(),
		],
		[
			'table_name' => 'parts_sub2_group',
			'created_at' => time(),
			'updated_at' => time(),
		],
		[
			'table_name' => 'parts',
			'created_at' => time(),
			'updated_at' => time(),
		],
	];

	insertTable('schedule',$data);	
}

function error($string)
{
	echo "\033[31m".$string."\033[0m".PHP_EOL;
}

function success($string)
{
	echo "\033[32m".$string."\033[0m".PHP_EOL;
}

function info($string)
{
	echo "\033[33m".$string."\033[0m".PHP_EOL;
}

?>