<?php
/**

 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.model' );

class Multimedia {
	
	var $id;
	var $filelink;
	var $filename;
	var $realImagePath;
	var $ext;
	var $icon;
	var $base_dir;
	var $prelink;
	var $view;
	var $field_name;
	var $field_value;
	var $showlink;
	var $thumbpath;
	var $type;
	var $googleKey;
	var $mapPopup;
	var $valore;
	var $previewx;
	var $data_evento;
	var $previewy;
	var $viewx;
	var $viewy;
	var $xIframe;
	var $yIframe;
	var $xThumbIframe;
	var $yThumbIframe;
	var $autoplay;
	var $streamingCode;
	var $class;
	var $label;
	var $linkArticle;
	var $mode;
	var $id_jarticle;
	var $options;
	var $idCoverImage;
	var $x;
	var $y;
	
	var $urlPath;
	var $urlThumbPath;
	var $urlMicroPath;
	
	var $pathSwf = "plugins/system/djflibraries/assets/players/flowplayer/flowplayer-3.2.7.swf";
	var $pathAudioSwf = "plugins/system/djflibraries/assets/players/flowplayer/flowplayer.audio-3.2.2.swf";
	var $pathJS = "plugins/system/djflibraries/assets/players/flowplayer/flowplayer-3.2.6.min.js";
	var $baseIconPath = "plugins/system/djflibraries/assets/images/icons/";
	
	public function checkExtensions($extension) {
		return ((strtolower ( $extension ) == "flv" || strtolower ( $extension ) == "mp4" || strtolower ( $extension ) == "mp3" || strtolower ( $extension ) == "xls" || strtolower ( $extension ) == "zip" || strtolower ( $extension ) == "pdf" || strtolower ( $extension ) == "ppt" || strtolower ( $extension ) == "pps" || strtolower ( $extension ) == "doc"|| strtolower ( $extension ) == "rtf"));
	}
	
	function setXYMapParam() {
		$xy = explode ( ',', $this->field_value );
		$this->x = $xy [0];
		$this->y = $xy [1];
	
	}
	
	function fillParamFromRecord() {
		
		$query = 'select * from #__djfappend_field as field left join #__djfappend_field_value as valori on (field.field_value = valori.id), 
					#__djfappend_field_type as tipi 
					where field.id = ' . $this->id . ' and tipi.id = field.id_field_type ';
		
		$listaRecord = utility::getQueryArray ( $query );
		
		foreach ( $listaRecord as $questoRecord ) {
			
			$this->id_jarticle = $questoRecord->id_jarticle;
			$this->data_evento = $questoRecord->event_date;
			$this->label = $questoRecord->display_name;
			$this->options = $questoRecord->options;
			
			if ($questoRecord->options == "file" || $questoRecord->options == "file_url")
				$this->field_value = $questoRecord->url;
			else
				$this->field_value = $questoRecord->field_value;
			
			if (! empty ( $questoRecord->valore ))
				$this->valore = $questoRecord->valore;
			else {
				$this->valore = $questoRecord->field_value;
			}
			
			$this->filename = $questoRecord->filename;
			$this->filelink = $this->field_value;
			$this->realImagePath = $questoRecord->filename_sys;
			
			$this->field_name = $questoRecord->name;
			
			$queryForCoverImage = 'select id as value from #__djfappend_field 
			where id_jarticle = ' . $this->id_jarticle . ' and file_type="image" order by ordering, id desc';
			
			$this->idCoverImage = utility::getField ( $queryForCoverImage );
			
			if ($this->options == "map") {
				$this->setXYMapParam ();
			}
			
			if (empty ( $this->valore ))
				$this->valore = $this->label;
			if (empty ( $this->valore ))
				$this->valore = $this->filename;
			
			$this->urlPath = Juri::root () . $this->filelink;
			$this->urlThumbPath = Juri::root () . str_replace ( $this->filename, "", $this->filelink ) . "/thumb/" . $this->filename;
			$this->urlMicroPath = Juri::root () . str_replace ( $this->filename, "", $this->filelink ) . "/thumb/micro/" . $this->filename;
			
			break;
		}
	}
	
	function __construct($id) {
		global $mainframe, $context;
		
		$params = & JComponentHelper::getParams ( 'com_djfappend' );
		
		$this->googleKey = $params->get ( 'googleKey' );
		
		$this->xIframe = $params->get ( 'i_width' );
		$this->yIframe = $params->get ( 'i_height' );
		
		$this->viewx = $this->xIframe;
		$this->viewy = $this->yIframe;
		
		$this->xThumbIframe = $params->get ( 'it_width' );
		$this->yThumbIframe = $params->get ( 'it_height' );
		
		$this->view = JRequest::getVar ( "view" );
		
		if (empty ( $this->view ))
			$this->view = "";
		
		$this->id = $id;
		$this->fillParamFromRecord ();
		
		$this->linkArticle = "index.php?option=com_content&view=article&id=" . $this->id_jarticle;
		
		$this->ext = strtolower ( utility::right ( $this->filelink, "3" ) );
		$this->type = $this->getType ();
		
		if ($this->options == "vimeo")
			$this->type = "vimeo";
		if ($this->options == "youtube")
			$this->type = "youtube";
		if ($this->options == "youtubeplaylist")
			$this->type = "youtubeplaylist";
		if ($this->options == "map")
			$this->type = "map";
		
		$this->icon = $this->getIcon ();
		$this->mode = $this->getMode ();
		$this->streamingCode =  $this->filelink;
		$this->pathSwf = JURI::root () . $this->pathSwf;
		$this->pathJS = JURI::root () . $this->pathJS;
		$this->baseIconPath = JURI::root () . $this->baseIconPath;
		$this->realImagePath = Juri::root () . $this->realImagePath;
		$this->filelink = JURI::root () . $this->filelink;
		$document = & JFactory::getDocument ();
		$document->addScript ( JURI::root () . $this->pathJS );
		$this->class = $this->type;
		
		$this->calculateAspectRatio ();
		
		if ($this->class == "youtubeplaylist")
			$this->class = "youtube";
		
		$this->showlink = "index.php?option=com_djfappend&controller=field_detail&task=showMedia&format=raw&id_field=" . $this->id;
	
	}
	
	function printValues() {
		echo ("<h1>");
		echo ("<br>");
		echo ("id = " . $this->id);
		
		echo ("<br>");
		echo ("urlPath = " . $this->urlPath);
		
		echo ("<br>");
		echo ("urlThumbPath = " . $this->urlThumbPath);
		
		echo ("<br>");
		echo ("urlMicroPath = " . $this->urlMicroPath);
		
		echo ("<br>");
		echo ("filelink = " . $this->filelink);
		echo ("<br>");
		echo ("filename = " . $this->filename);
		echo ("<br>");
		echo ("realImagePath = " . $this->realImagePath);
		echo ("<br>");
		echo ("ext = " . $this->ext);
		echo ("<br>");
		echo ("icon = " . $this->icon);
		echo ("<br>");
		echo ("mode = " . $this->mode);
		echo ("<br>");
		echo ("type = " . $this->type);
		echo ("<br>");
		echo ("previewx = " . $this->previewx);
		echo ("<br>");
		echo ("previewy = " . $this->previewy);
		echo ("<br>");
		echo ("viewx = " . $this->viewx);
		echo ("<br>");
		echo ("viewy = " . $this->viewy);
		echo ("<br>");
		echo ("xIframe = " . $this->xIframe);
		echo ("<br>");
		echo ("yIframe = " . $this->yIframe);
		echo ("<br>");
		echo ("autoplay = " . $this->autoplay);
		echo ("<br>");
		echo ("class = " . $this->class);
		echo ("<br>");
		
		echo ("options = " . $this->options);
		echo ("<br>");
		echo ("id_jarticle = " . $this->id_jarticle);
		echo ("</h1>");
	}
	
	function getMode() {
		switch ($this->type) {
			case "document" :
				return "";
			default :
				return "popup";
		}
	}
	
	function getIcon() {
		switch ($this->type) {
			case "video" :
				return "video.gif";
			case "youtube" :
				return "video.gif";
			case "youtubeplaylist" :
				return "video.gif";
			
			case "vimeo" :
				return "video.gif";
			case "audio" :
				return "music.gif";
			case "image" :
				return "image.gif";
			case "map" :
				return "html.gif";
			case "document" :
				return $this->getIconDocument ();
			default :
				return "css.gif";
		
		}
	}
	
	public function getIconDocument($ext = '') {
		if (empty ( $ext ))
			$ext = $this->ext;
		
		switch ($ext) {
			case "doc" :
				return "word.gif";
				case "rtf" :
				return "word.gif";
			case "pdf" :
				return "pdf.gif";
			case "xls" :
				return "excel.gif";
			case "zip" :
				return "zip.gif";
			case "ppt" :
				return "ppt.gif";
			case "pps" :
				return "pps.gif";
		
		}
	}
	
	public function getFileTypeFromPath($filepath) {
		$ext = strtolower ( utility::right ( $filepath, "3" ) );
		$type = Multimedia::getType ( $ext );
		return $type;
	
	}
	public function getMediaIconFromFilename($filepath) {
		$ext = strtolower ( utility::right ( $filepath, "3" ) );
		$mediaIcon = Multimedia::getType ( $ext );
		return $mediaIcon;
	
	}
	public function getType($ext = '') {
		
		if (empty ( $ext ))
			$ext = $this->ext;
		
		switch ($ext) {
			
			case "flv" :
				return "video";
			case "wmv" :
				return "video";
			case "avi" :
				return "video";
			case "mpg" :
				return "video";
			case "mp4" :
				return "video";
			
			case "mp3" :
				return "audio";
			case "wav" :
				return "audio";
			case "wma" :
				return "audio";
			
			case "jpg" :
				return "image";
			case "gif" :
				return "image";
			case "bmp" :
				return "image";
			case "png" :
				return "image";
			
			case "doc" :
				return "document";
			case "xls" :
				return "document";
			case "ppt" :
				return "document";
			case "pps" :
				return "document";
			case "pdf" :
				return "document";
			case "rtf" :
				return "document";
				
			default :
				return "tag";
		
		}
	
	}
	
	public function showImage() {
		global $mainframe;
		$pathFile = $this->filelink;
		$path = $pathFile;
		$id_field = $this->id;
		$pathSwf = $this->pathSwf;
		$pathJS = $this->pathJS;
		
		$lang = & JFactory::getLanguage ();
		$lang->load ( 'com_djfacl', JPATH_ROOT );
		
		$finale = '
		
		<div id="player_' . $id_field . '" style="z-index:-1;display:block;width:100%;height:100%;"> 
		<a	
			href="' . $this->linkArticle . '" 
			title="' . $this->label . '" 
			name="' . $this->label . '" 
			
		>
			<img style="width:' . $this->xIframe . 'px;height:' . $this->yIframe . '" src="' . $pathFile . '"/>
		</a></div>
		';
		
		if ($this->view != "article")
			//			$finale .= '<a title="Leggi" href="' . $this->linkArticle . '"><div class="label_image" style=" width:' . $this->xIframe . 'px;"></div></a>';
			

			$finale .= '<div class="label_image" style=" width:' . $this->xIframe . 'px;"><a title="' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '" href="' . $this->linkArticle . '">' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '</a></div>';
		else
			$finale .= '<div class="label_image" style=" width:' . $this->xIframe . 'px;">
			' . $this->label . '</div>';
			
		/*$finale = '
		<a 
			
			class="modal-button"
			href="'.$this->filelink.'" 
			title="Vedi Immagine" 
			name="Vedi Immagine" 
			rel="{handler: \'iframe\'}"
		>
			<img style="width:'.$this->xIframe.'px;height:'.$this->yIframe.'" src="'.$pathFile.'"/>
		</a>
		';*/
		
		/*<script>
			$f("player_' . $id_field . '", "' . $pathSwf . '", {
			plugins:{controls:null},
			play:null,
			clip: { 
				autoPlay: true,
	   			url: \'' . $path . '\',
	   			coverImage: {
	   				url: \'' . $path . '\',
	   				scaling: \'orig\' 
				} 
	   			
		}
		});
		</script>
		';*/
		
		return $finale;
	}
	
	public function showAudio() {
		
		$lang = & JFactory::getLanguage ();
		$lang->load ( 'com_djfacl', JPATH_ROOT );
		
		$pathFile = $this->filelink;
		$path = $pathFile;
		$pathCoverImage = "";
		$id_field = $this->id;
		
		$pathSwf = $this->pathSwf;
		$pathJS = $this->pathJS;
		if ($this->autoplay)
			$this->autoplay = "true";
		else
			$this->autoplay = "false";
		
		$queryFindCoverImage = " select * from #__djfappend_field where id_jarticle = " . $this->id_jarticle . " and file_type = 'image' order by ordering, id desc ";
		$listaImmagine = utility::getQueryArray ( $queryFindCoverImage );
		foreach ( $listaImmagine as $primaImmagine ) {
			$pathCoverImage = JURI::root().$primaImmagine->url;
			break;
		}
		if (empty ( $pathCoverImage )) {
			$pathCoverImage = JURI::root().'plugins/system/djflibraries/assets/images/audio.jpg';
		}
		
		if ($this->view != "article")
			$disableControl = "controls: {
					fullscreen: false,
					mute: false,
					time: false
							
				} ";
		else
			$disableControl = "controls: {
					fullscreen: true
				}";
		
		$finale = '
		<div id="player_' . $id_field . '" style="display:block;width:100%;height:100%;"></div>
		<script>
			$f("player_' . $id_field . '", "' . $pathSwf . '", {
			plugins: {
				' . $disableControl . '
			},
			clip: { 
			   url: "' . $path . '",
			   autoBuffering: true,
			   coverImage: { url: "' . $pathCoverImage . '", scaling: "orig" },
			   autoPlay: false
			}
		});
		</script>
		
		';
		if ($this->view != "article")
			$finale .= '<div class="label_audio" style=" width:' . $this->xIframe . 'px;"><a title="' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '" href="' . $this->linkArticle . '">' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '</a></div>';
		else
			$finale .= '<div class="label_audio" style=" width:' . $this->xIframe . 'px;">
			' . $this->label . '</div>';
		
		return $finale;
	}
	
	public function showVideovimeo() {
		
		$lang = & JFactory::getLanguage ();
		$lang->load ( 'com_djfacl', JPATH_ROOT );
		
		$finale = '
		
		
		<iframe src="http://player.vimeo.com/video/' . $this->streamingCode . '"  
				width="100%" height="100%" 
				frameborder="0">
				</iframe>
		';
		if ($this->view != "article")
			$finale .= '<div class="label_video" style=" width:' . $this->xIframe . 'px;"><a title="' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '" href="' . $this->linkArticle . '">' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '</a></div>';
		
		else
			$finale .= '<div class="label_video" style=" width:' . $this->xIframe . 'px;">' . $this->label . '</div>';
		return $finale;
	}
	
	public function showVideoPlaylistYoutube() {
		
		$lang = & JFactory::getLanguage ();
		$lang->load ( 'com_djfacl', JPATH_ROOT );
		
		$finale = '<iframe src="http://www.youtube.com/embed/p/' . $this->streamingCode . '" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>';
		if ($this->view != "article")
			$finale .= '<div class="label_video" style=" width:' . $this->xIframe . 'px;"><a title="' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '" href="' . $this->linkArticle . '">' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '</a></div>';
		else
			$finale .= '<div class="label_video" style=" width:' . $this->xIframe . 'px;">' . $this->label . '</div>';
		return $finale;
	}
	
	public function showVideoYoutube() {
		
		$lang = & JFactory::getLanguage ();
		$lang->load ( 'com_djfacl', JPATH_ROOT );
		
		$finale = '
			<iframe title="YouTube video player" width="100%" height="100%" 
			src="http://www.youtube.com/embed/' . $this->streamingCode . '"  
			frameborder="0" allowfullscreen></iframe>
		';
		if ($this->view != "article")
			$finale .= '<div class="label_video" style=" width:' . $this->xIframe . 'px;"><a title="' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '" href="' . $this->linkArticle . '">' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '</a></div>';
		else
			$finale .= '<div class="label_video" style=" width:' . $this->xIframe . 'px;">' . $this->label . '</div>';
		
		return $finale;
	}
	
	function showMap() {
		
		$lang = & JFactory::getLanguage ();
		$lang->load ( 'com_djfacl', JPATH_ROOT );
		
		$x = $this->x;
		$y = $this->y;
		$width = $this->xIframe;
		$height = $this->yIframe;
		$mappa = $this->getMappa ( $x, $y, $width, $height, $this->label );
		$toReturn = '<div id="player_' . $this->id . '" style="display:block;width:100%;height:100%;">' . $mappa . '</div>';
		if ($this->view != "article")
			$toReturn .= '<div class="label_map" style=" width:' . $this->xIframe . 'px;"><a title="' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '" href="' . $this->linkArticle . '">' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '</a></div>';
		else
			$toReturn .= '<div class="label_map" style=" width:' . $this->xIframe . 'px;">' . $this->label . '</div>';
		
		return $toReturn;
	}
	
	public function getMappa() 

	{
		
		$lang = & JFactory::getLanguage ();
		$lang->load ( 'com_djfacl', JPATH_ROOT );
		if (! empty ( $this->label ))
			$this->mapPopup = '
		marker.openInfoWindowHtml("' . $this->label . '");
		map.addControl(tcontrol);
		map.addControl(control);
		';
		
		$this->rebuildPreviewSize ();
		return ('
		   	<script type="text/javascript" src="http://www.google.com/jsapi?key=' . $this->googleKey . '"></script>
			<script type="text/javascript">
			      google.load("maps", "2");

			      function initialize() {
		    	    var map = new google.maps.Map2(document.getElementById("map"));
		        	map.setMapType(G_HYBRID_MAP);
					var control = new GSmallMapControl();
					var tcontrol = new GMapTypeControl();
					map.enableContinuousZoom();
					map.enableScrollWheelZoom();
		        	map.setCenter(new google.maps.LatLng(' . $this->x . ',' . $this->y . '), 16);
		        	var point = new GLatLng(' . $this->x . ', ' . $this->y . ');
		        	var marker = new GMarker(point);
		        	' . $this->mapPopup . '
		        	map.addOverlay(marker);
				
		      }
		      google.setOnLoadCallback(initialize);
		    </script>

		    <div id="map" style="width:' . $this->xIframe . 'px; height:' . $this->yIframe . 'px"></div>');
	
	}
	
	public function showVideo() {
		
		$lang = & JFactory::getLanguage ();
		$lang->load ( 'com_djfacl', JPATH_BASE );
		
		$autoBuffering = "";
		$pathFile = $this->filelink;
		$path = $pathFile;
		$id_field = $this->id;
		$pathCoverImage = "";
		$pathSwf = $this->pathSwf;
		$pathJS = $this->pathJS;
		if ($this->autoplay)
			$this->autoplay = "true";
		else
			$this->autoplay = "false";
		
		if (! empty ( $this->idCoverImage )) {
			$coverImage = new Multimedia ( $this->idCoverImage );
			$pathCoverImage = $coverImage->filelink;
			if (! empty ( $pathCoverImage ))
				$autoBuffering = "true";
		}
		
		if ($this->view != "article")
			$disableControl = "controls: {
					volume: true,
					time: false,
					mute: false,
					play: true		
				} ";
		else
			$disableControl = "controls: {
					fullscreen: true
				}";
		
		$finale = '
		<div id="player_' . $id_field . '" style="display:block;width:100%;height:100%;"></div>';
		if ($autoBuffering == "")
			$finale .= '
			<script>
			$f("player_' . $id_field . '", "' . $pathSwf . '", {
			
			plugins: {
				controls: {
					fullscreen: true,
					autoHide: true
				},
				content: {

					height: 220,
					padding:30,
					backgroundColor: "#112233",
					opacity: 0.7,
					backgroundGradient: [0.1, 0.1, 1.0],
					html: "<p>This big overlay is a content plugin</p>",
					style: {p: {fontSize: 40}}			
				}
			},
			clip: { 
	   			url: "' . $path . '",
	   			autoBuffering: true,
	   			coverImage: { url: "' . $pathCoverImage . '", scaling: "orig" },
	   			autoPlay: ' . $this->autoplay . '
			}
		});
		</script>
			';
		else
			$finale .= '		
		<script>
			$f("player_' . $id_field . '", "' . $pathSwf . '", {
			
			playlist: [
			{
				url: "' . $pathCoverImage . '", 
				scaling: "orig"
				},
		
			{
				url: "' . $path . '", 
				autoPlay: false, 
				autoBuffering: true 
				}
			],
			
			plugins: {
			
				' . $disableControl . '
			}
		});
		</script>
	';
		if ($this->view != "article")
			$finale .= '<div class="label_video" style=" width:' . $this->xIframe . 'px;"><a title="' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '" href="' . $this->linkArticle . '">' . Jtext::_ ( 'VEDI_ARTICOLO' ) . '</a></div>';
		return $finale;
	}
	
	public function showDocument() {
		$finale = '
		<a target="doc" href="' . $this->filelink . '" title="' . $this->label . '">
			<img src="' . $this->baseIconPath . '/' . $this->icon . '" />
		</a>';
		return $finale;
	}
	
	public function calculateAspectRatio() {
		global $mainframe, $context;
		
		$view = JRequest::getVar ( "view" );
		
		$mediaType = $this->type;
		if ($mediaType == "video" || $mediaType == "audio") {
			$x = $this->viewx;
			$y = $this->viewy;
			if ($view != "article") {
				//$x = $this->previewx;
			//$y = $this->previewy;
			}
			$autoplay = $this->autoplay;
			$class = $this->type;
			$this->xIframe = $x;
			$this->yIframe = $y;
		}
		if ($mediaType == "image") {
			$x = $this->viewx;
			$y = $this->viewy;
			$thumbpath = $this->urlThumbPath;
			
			if (! empty ( $x )) {
				if ($view != "article") {
					//$x = $this->previewx;
				}
				$image = new SimpleImage ();
				
				$image->load ( $this->realImagePath );
				$height = $image->getHeight ();
				$width = $image->getWidth ();
				
				if ($height > $width) {
					$y = $x;
					$x = $y * $width / $height;
				} else
					$y = $x * $height / $width;
			}
			
			$autoplay = $this->autoplay;
			$class = $this->type;
			$this->xIframe = $x;
			$this->yIframe = $y;
		}
	}
	
	function rebuildPreviewSize() {
		
		$xIframe = $this->xIframe;
		$yIframe = $this->yIframe;
		if ($this->view != "article") {
			$xIframe = $this->xThumbIframe;
			$yIframe = $this->yThumbIframe;
			$this->mapPopup = "";
		}
		$this->xIframe = $xIframe;
		$this->yIframe = $yIframe;
		if ($this->type == "image") {
			$this->mantainAspectRatio ( $this->xIframe, $this->yIframe );
		}
	}
	
	function mantainAspectRatio($x, $y) {
		$image = new SimpleImage ();
		$image->load ( $this->realImagePath );
		$height = $image->getHeight ();
		$width = $image->getWidth ();
		if ($height > $width) {
			$y = $x;
			$x = $y * $width / $height;
			$this->xIframe = $x;
			$this->yIframe = $y;
		
		} else {
			$y = $x * $height / $width;
			$this->xIframe = $x;
			$this->yIframe = $y;
		}
	}
	
	function showvimeoOnIframe() {
		$code = $this->field_value;
		$finale = '
		<html>
		<head>
		</head>
		<body>		
		<iframe src="http://player.vimeo.com/video/' . $code . '" 
				width="100%" height="100%" 
				frameborder="0">
				</iframe>
		</body>
		</html>';
		return $finale;
	}
	
	function showMapOnIframe() {
		$x = $this->x;
		$y = $this->y;
		$this->xIframe = $this->xIframe - 20;
		$this->yIframe = $this->yIframe - 20;
		$this->view = "article";
		return ($this->getMappa ());
	}
	
	function showYoutubeOnIframe() {
		$code = $this->field_value;
		$finale = '
		<html>
		<head>
		</head>
		<body>		
		<iframe title="YouTube video player" width="100%" height="100%" src="http://www.youtube.com/embed/' . $code . '" frameborder="0" allowfullscreen></iframe>
		</body>
		</html>';
		return $finale;
	
	}
	function showYoutubePlaylistOnIframe() {
		$code = $this->field_value;
		$finale = '
		<html>
		<head>
		</head>
		<body>		
		<iframe src="http://www.youtube.com/embed/p/' . $code . '" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
		</body>
		</html>';
		return $finale;
	
	}
	
	function showVideoOnIframe() {
		$pathFile = $this->filelink;
		$path = $pathFile;
		$pathSwf = $this->pathSwf;
		$pathJS = $this->pathJS;
		$finale = '
		<html>
		<head>
		<script type="text/javascript" src="' . $pathJS . '"></script>
		</head>
		<body>		
		<a href="' . $path . '" style="display:block;width:100%;height:100%;" id="player"></a>
		<script language="JavaScript">flowplayer("player", "' . $pathSwf . '");</script>
		</body>
		</html>';
		return $finale;
	}
	
	function showAudioOnIframe() {
		$pathFile = $this->filelink;
		$path = $pathFile;
		$pathSwf = $this->pathSwf;
		$pathJS = $this->pathJS;
		
		if (! empty ( $this->idCoverImage )) {
			$coverImageObject = new Multimedia ( $this->idCoverImage );
			$coverImageString = 'coverImage: { 
	   				url: \'' . $coverImageObject->filelink . '\',
	   				scaling: \'orig\' 
				} ';
		} else{
			$pathCoverImage = JURI::root().'plugins/system/djflibraries/assets/images/audio.jpg';
			$coverImageString = 'coverImage: { 
	   				url: \'' . $pathCoverImage . '\',
	   				scaling: \'orig\' 
				} ';
			
		}
		
		$finale = '
		<html>
		<head>
		<script type="text/javascript" src="' . $pathJS . '"></script>
		</head>
		<body>		
		<div id="player" style="display:block;width:100%;height:100%;"></div>
		<script>
			$f("player", "' . $pathSwf . '", {
			plugins: {
				controls: {
					fullscreen: false,
					autoHide: false
				}
			},
			play: { opacity: 0 },
			clip: { 
	   			url: \'' . $path . '\',
	   			autoBuffering: true,
	  			' . $coverImageString . '
			}
			
		
		});
		</script>
		</body>
		</html>';
		return $finale;
	}
	
	function showImgOnIframe() {
		$pathFile = $this->filelink;
		$path = $pathFile;
		$pathSwf = $this->pathSwf;
		$pathJS = $this->pathJS;
		
		$finale = '
		<html>
		<head>
		<script type="text/javascript" src="' . $pathJS . '"></script>
		</head>
		<body>		
		<div id="player" style="display:block;width:100%;height:100%;"></div>
		<script>
			$f("player", "' . $pathSwf . '", {
			plugins: {
				
				controls: {
					opacity:0,
					autoHide: \'always\',
					fullscreen: false
				}
			},
			play: { opacity: 0 },
			clip: { 
			
	   			url: \'' . $path . '\',
	   			coverImage: { 
	   				url: \'' . $path . '\',
	   				scaling: \'orig\' 
				} 
		}
		});
		</script>
		</body>
		</html>';
		return $finale;
	}
	
	function getIconPerPopup() {
		$custom = ' name="' . $this->label . '" title="' . $this->label . '" href="' . $this->showlink . '"';
		$iconaRender = utility::getButtonCustom ( $custom, $this->baseIconPath . "/" . $this->icon, $this->mode, $this->xIframe, $this->yIframe );
		if ($this->type == "document") {
			$iconaRender = $this->showDocument ();
		}
		if ($this->type == "tag") {
			$nomeImg = "css.gif";
			$iconaRender = '<img src="' . $this->baseIconPath . $nomeImg . '"/>';
		}
		return $iconaRender;
	}
	
	function showMediaOnIframe() {
		if ($this->type == "image")
			return $this->showImgOnIframe ();
		if ($this->type == "video")
			return $this->showVideoOnIframe ();
		if ($this->type == "youtube")
			return $this->showYoutubeOnIframe ();
		if ($this->type == "youtubeplaylist")
			return $this->showYoutubePlaylistOnIframe ();
		if ($this->type == "vimeo")
			return $this->showvimeoOnIframe ();
		if ($this->type == "audio")
			return $this->showAudioOnIframe ();
		if ($this->type == "document")
			return $this->showDocument ();
		if ($this->type == "map")
			return $this->showMapOnIframe ();
	}
	function showMedia() {
		
		$this->rebuildPreviewSize ();
		
		if ($this->type == "image")
			return $this->showImage ();
		if ($this->type == "video")
			return $this->showVideo ();
		if ($this->type == "youtube")
			return $this->showVideoYoutube ();
		if ($this->type == "youtubeplaylist")
			return $this->showVideoPlaylistYoutube ();
		if ($this->type == "vimeo")
			return $this->showVideovimeo ();
		if ($this->type == "audio")
			return $this->showAudio ();
		if ($this->type == "document")
			return $this->showDocument ();
		if ($this->type == "map")
			return $this->showMap ();
	}
	
	public function getMediaFromArticleId($idPadre, $type="image", $orderby=",id desc") {
		$questoMediaId = "";
		$thisM="";
		$listaMedia = utility::getQueryArray ( 'select * from #__djfappend_field where id_jarticle = ' . $idPadre . ' and file_type = "'.$type.'" order by ordering'.$orderby );
		foreach ( $listaMedia as $questoMedia ) {
			$questoMediaId = $questoMedia->id;
			$thisM = new Multimedia($questoMediaId);
			break;
		}
		
		return $thisM;
	
	}

}

