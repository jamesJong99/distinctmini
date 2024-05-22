
<?php
require_once("ClassesStore/Util.php");

class headComponent extends Util
{

	public function head($title, $descripcion)
	{

?>
		<!DOCTYPE html>
		<html lang="es">

		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link rel="icon" sizes="192x192" href="https://static.wixstatic.com/media/76d6ea_cb3c6158b72149e3b97d5bb0758935a4%7Emv2.jpg/v1/fill/w_192%2Ch_192%2Clg_1%2Cusm_0.66_1.00_0.01/76d6ea_cb3c6158b72149e3b97d5bb0758935a4%7Emv2.jpg" type="image/jpeg">

			<link rel="stylesheet" href="<?php echo BASE_URL_STORE ?>css/style.css">

			<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->

			<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
			<script src="<?php echo BASE_URL_STORE ?>javascript/jquery.redirect.js"></script>
			<script type="text/javascript" src="<?php echo BASE_URL_STORE ?>javascript/funciones.js"></script>

			<script src="https://cdn.tailwindcss.com"></script>

			<!-- Fancy Lightbox -->
			<link rel="stylesheet" href="<?php echo BASE_URL ?>fancybox/source/jquery.fancybox.css?v=2.1.7" type="text/css" media="screen" />
			<script type="text/javascript" src="<?php echo BASE_URL ?>fancybox/source/jquery.fancybox.pack.js?v=2.1.7"></script>
			<link rel="stylesheet" href="<?php echo BASE_URL ?>fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
			<script type="text/javascript" src="<?php echo BASE_URL ?>fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
			<script type="text/javascript" src="<?php echo BASE_URL ?>fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
			<link rel="stylesheet" href="<?php echo BASE_URL ?>fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
			<script type="text/javascript" src="<?php echo BASE_URL ?>fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

			<!-- Glide.js -->
			<!-- Incluye los estilos de Glide.js -->
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css">
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.theme.min.css">

			<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.7/dist/cdn.min.js"></script>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="facebook-domain-verification" content="teregua2oxrqz1yy8855utw4siso20" />
			<meta name="description" content="<?php echo $descripcion; ?>">
			<title><?php echo $title; ?></title>
		</head>
<?php
	}
}
?>