<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	function placeholder() {
		return <<<HTML
		<div id="i152placeholder"></div>
HTML;
	}

	function gallerySlider($section = 'homepage') {
		$images = showGallery($section, 100, 4);
		
		return <<<HTML
		
		<div id="welcome2text" class="text_link">
			<div id="slidingpanelContainer">
				<div id="slidingpanelsheet">{$images}</div>
			</div>
			<div id="seeourcats"></div>
			<div id="slidingbuttonnextpanel">
				<a href="#" class="nm"></a>
			</div>
			<div id="slidingbuttonpreviouspane">
				<a href="#" class="nm"></a>
			</div>
		</div>
HTML;
	}

	function mainGallery($section = 'homepage') {
		$images = showGallery($section, 108, 4, 3);
		
		return <<<HTML
		
		<div class="galleryContainer flow">
			<div class="galleryNavigation">
				<div class="buttons">
					<span id="estorebuttonpreviousthumb"><a href="#" class="nm"></a></span>
					<span id="estorebuttonnextthumbset"><a href="#" class="nm"></a></span>
				</div>
				<div class="galleryImagesContainer" class="column">
					{$images}
				</div>
			</div>
			<div class="galleryPlaceholder">
				<div id="i143estoremetadata" class="text_link column">
					<p class="estore_d title">Sample Cat Title</p>
				</div>
				<a href="#" class="galleryImagePlaceholder"></a>
			</div>
		</div>
HTML;
	}
	
	function showGallery($folder = 'main', $width = 150, $num = 10, $breakPoint = 0) {
		$CI = &get_instance();
		
		if (!$CI->config->item('gallery_dir')) {
			return '';
		}
		
		$imageList = array();
		$dir = $CI->config->item('gallery_dir') . $folder . '/';
		
		if ($handle = opendir('.' . $dir)) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match("/\.png$/", $file)) {
					$imageList[] = $file;
				} elseif (preg_match("/\.jpg$/", $file)) {
					$imageList[] = $file;
				} elseif (preg_match("/\.gif$/", $file)) {
					$imageList[] = $file;
				} elseif (preg_match("/\.jpeg$/", $file)) {
					$imageList[] = $file;
				}
		
			}
			closedir($handle);
		}
		
		$return = '<div class="galleryImageContainer">';
		
		sort($imageList);
		
		foreach($imageList as $key => $i){
			list($title, ) = explode('.', $i, 2);
			$return .= "<a href='". $dir . $i."' title='" . $title . "'><img height='" . $width ."' src='". $dir . $i."' /></a> ";
			
			if (($key + 1) >= $num) {
				break;
			}
			
			if (($breakPoint > 0) && ((($key + 1) % $breakPoint) == 0)) {
				$return .= '</div><div class="galleryImageContainer">';
			}
		}
		
		$return .= '</div>';
		
		return $return;
		
	}