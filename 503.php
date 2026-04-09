<?php
   $rootBaseUrl = '';
$codeSource = 'bsis';
$httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
$documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
?>

<div class="col-12">
	<center>
	<img class="mw-100" src="<?php echo $httpHost.'vendor/dist/img/underconstruction.jpg' ?>"  class="product-image" alt="Product Image">
	</center>
</div>