<?php
    $rootBaseUrl = '';
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    
    $_SESSION['V1c1T2NHTjNQVDA9__activePage'] = '404';
	
	echo '
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="UTF-8">
			<title>RSKD</title>
			<link rel="icon" type="image/png" href="'.$httpHost.'vendor/dist/img/rskd.png">
			<link rel="stylesheet" href="'.$httpHost.'vendor/plugins/bootstrap3/bootstrap.min.css">
			<link rel="stylesheet" href="'.$httpHost.'vendor/fonts/arvo/arvo.css">
			<style>
				.page_404 {
					padding: 40px 0;
					background: #fff;
					font-family: \'Arvo\', serif;
				}

				.page_404 img {
					width: 100%;
				}

				.four_zero_four_bg {
					background-image: url(\''.$httpHost.'vendor/dist/img/404.gif\');
					height: 400px;
					background-position: center;
				}

				.four_zero_four_bg h1 {
					font-size: 80px;
				}

				.four_zero_four_bg h3 {
					font-size: 80px;
				}

				.link_404 {
					color: #fff !important;
					padding: 10px 20px;
					background: #39ac31;
					margin: 20px 0;
					display: inline-block;
				}

				.contant_box_404 {
					margin-top: -50px;
				}
			</style>
		</head>
		<body>
			<section class="page_404">
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<div class="col-sm-10 col-sm-offset-1 text-center">
								<div class="four_zero_four_bg">
									<h1 class="text-center">404</h1>
								</div>
								<div class="contant_box_404">
									<h3 class="h2">
										Look like you\'re lost
									</h3>
									<p>the page you are looking for not avaible!</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</body>
	</html>';

    unset($_SESSION['V1c1T2NHTjNQVDA9__notification']);
?>