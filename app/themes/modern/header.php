<?php 
/**
*	Developed by: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2022
*/

if (empty($_SESSION['user'])) {
	$title = 'Error';
	$content = 'Layout ini memerlukan login';
	include (BASE_PATH . 'system/views/error.php');
	exit;
	
}
global $current_module;
global $js;
global $styles;
global $app_layout;
global $setting_app;
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<title><?=$current_module['judul_module']?> | <?=$setting_app['judul_web']?></title>
<meta name="descrition" content="<?=$current_module['deskripsi']?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="<?=$config['base_url'] . 'public/images/favicon.png?r=' . time()?>" />
<link rel="stylesheet" type="text/css" href="<?=$config['base_url'] . 'public/vendors/font-awesome/css/all.css?='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config['base_url'] . 'public/vendors/bootstrap/css/bootstrap.min.css?r='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=THEME_URL . 'css/bootstrap-custom.css?r=' . time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config['base_url'] . 'public/vendors/sweetalert2/sweetalert2.min.css?r='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config['base_url'] . 'public/vendors/overlayscrollbars/OverlayScrollbars.min.css?r='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=THEME_URL . 'css/site.css?r='.time()?>"/>

<!-- Data Tables -->
<link rel="stylesheet" type="text/css" href="<?=$config['base_url'] . 'public/vendors/datatables/dist/css/dataTables.bootstrap5.min.css?r='.time()?>"/>
<!-- // Data Tables -->

<link rel="stylesheet" id="style-switch" type="text/css" href="<?=THEME_URL . 'css/color-schemes/'.$app_layout['color_scheme'].'.css?r='.time()?>"/>
<link rel="stylesheet" id="style-switch-sidebar" type="text/css" href="<?=THEME_URL . 'css/color-schemes/'.$app_layout['sidebar_color'].'-sidebar.css?r='.time()?>"/>
<link rel="stylesheet" id="font-switch" type="text/css" href="<?=THEME_URL . 'css/fonts/'.$app_layout['font_family'].'.css?r='.time()?>"/>
<link rel="stylesheet" id="font-size-switch" type="text/css" href="<?=THEME_URL . 'css/fonts/font-size-'.$app_layout['font_size'].'.css?r='.time()?>"/>
<link rel="stylesheet" id="logo-background-color-switch" type="text/css" href="<?=THEME_URL . 'css/color-schemes/'.$app_layout['logo_background_color'].'-logo-background.css?r='.time()?>"/>
<?php
if (@$styles) {
	foreach($styles as $file) {
		echo '<link rel="stylesheet" type="text/css" href="'.$file.'?r='.time().'"/>';
	}
}
?>
<link rel="stylesheet" type="text/css" href="<?=THEME_URL . 'css/override.css?r='.time()?>"/>
<script type="text/javascript">
	var base_url = "<?=$config['base_url']?>";
	var module_url = "<?=module_url()?>";
	var current_url = "<?=current_url()?>";
	var theme_url = "<?=theme_url()?>";
	var filepicker_server_url = "<?=$config['filepicker_server_url']?>";
	var filepicker_icon_url = "<?=$config['filepicker_icon_url']?>";
</script>
</script>
<script type="text/javascript" src="<?=$config['base_url'] . 'public/vendors/jquery/jquery.min.js?='.time()?>"></script>
<script type="text/javascript" src="<?=$config['base_url'] . 'public/vendors/bootstrap/js/bootstrap.min.js?r='.time()?>"></script>
<script type="text/javascript" src="<?=$config['base_url'] . 'public/vendors/bootbox/bootbox.min.js?r='.time()?>"></script>
<script type="text/javascript" src="<?=$config['base_url'] . 'public/vendors/sweetalert2/sweetalert2.min.js?r='.time()?>"></script>
<script type="text/javascript" src="<?=$config['base_url'] . 'public/vendors/overlayscrollbars/jquery.overlayScrollbars.min.js?r='.time()?>"></script>
<script type="text/javascript" src="<?=$config['base_url'] . 'public/themes/modern/js/functions.js?r='.time()?>"></script>
<script type="text/javascript" src="<?=THEME_URL . 'js/site.js?r='.time()?>"></script>

<!-- Data Tables -->
<script type="text/javascript" src="<?=$config['base_url'] . '/public/vendors/datatables/dist/js/jquery.dataTables.min.js?r='.time()?>"></script>
<script type="text/javascript" src="<?=$config['base_url'] . '/public/vendors/datatables/dist/js/dataTables.bootstrap5.min.js?r='.time()?>"></script>
<!-- // Data Tables -->

<?php
if (@$js) {
	foreach($js as $file) {
		if (is_array($file)) {
			if ($file['print']) {
				echo '<script type="text/javascript">' . $file['script'] . '</script>' . "\n";
			}
		} else {
			echo '<script type="text/javascript" src="'.$file.'?r='.time().'"></script>' . "\n";
		}
	}
}

$user = $_SESSION['user'];
?>
</head>
<body>
	<header class="nav-header shadow">
		<div class="nav-header-logo pull-left">
			<a class="header-logo" href="<?=$config['base_url']?>" title="Jagowebdev">
				<img height="44px" src="<?=BASE_URL . $config['images_path'] . $setting_app['logo_app']?>"/>
			</a>
		</div>
		<div class="pull-left nav-header-left">
			<ul class="nav-header">
				<li>
					<a href="#" id="mobile-menu-btn">
						<i class="fa fa-bars"></i>
					</a>
				</li>
			</ul>
		</div>
		<div class="pull-right mobile-menu-btn-right">
			<a href="#" id="mobile-menu-btn-right">
				<i class="fa fa-ellipsis-h"></i>
			</a>
		</div>
		<div class="pull-right nav-header nav-header-right">
			
			<ul>
				<!-- <li><a class="icon-link" href="<?=$config['base_url']?>setting-layout"><i class="fas fa-cog"></i></a></li> -->
				
				<li>
					<?php $img_url = !empty($user['avatar']) && file_exists(BASE_PATH . '/public/images/user/' . $user['avatar']) ? $config['base_url'] . $config['user_images_path'] . $user['avatar'] : $config['base_url'] . $config['user_images_path'] . 'default.png';
					$account_link = $config['base_url'] . 'user';
					?>
					<a class="profile-btn" href="<?=$account_link?>"><img src="<?=$img_url?>" alt="user_img"></a>
					<div class="account-menu-container shadow-sm">
						<?php
						if ($is_loggedin) { 
							?>
							<ul class="account-menu">
								<li class="account-img-profile">
									<div class="avatar-profile">
										<img src="<?=$img_url?>" alt="user_img">
									</div>
									<div class="card-content">
									<p><?=strtoupper($user['nama'])?></p>
									<p><small>Email: <?=$user['email']?></small></p>
									</div>
								</li>
								<li><a href="<?=$config['base_url']?>user/edit-password">Change Password</a></li>
								<li><a href="<?=$config['base_url']?>login/logout">Logout</a></li>
							</ul>
						<?php } else { ?>
							<div class="float-login">
							<form method="post" action="<?=$config['base_url']?>login">
								<input type="email" name="email" value="" placeholder="Email" required>
								<input type="password" name="password" value="" placeholder="Password" required>
								<div class="checkbox">
									<label style="font-weight:normal"><input name="remember" value="1" type="checkbox">&nbsp;&nbsp;Remember me</label>
								</div>
								<button type="submit"  style="width:100%" class="btn btn-success" name="submit">Submit</button>
								<?php
								$form_token = $auth->generateFormToken('login_form_token_header');
								?>
								<input type="hidden" name="form_token" value="<?=$form_token?>"/>
								<input type="hidden" name="login_form_header" value="login_form_header"/>
							</form>
							<a href="<?=$config['base_url'] . 'recovery'?>">Lupa password?</a>
							</div>
						<?php }?>
					</div>
				</li>
			</ul>
		
		</div>
	</header>
	<div class="site-content">
		<?php
		require_once('app/includes/functions.php');
		
		// MENU - SIDEBAR
		$menu = get_menu();
		// $list_menu = menu_list($menu_db);

		?>
		<div class="sidebar shadow">
			<nav>
				<?php
				foreach ($menu as $val) {
					$kategori = $val['kategori'];
					if ($kategori['show_title'] == 'Y') {
						echo '<div class="menu-kategori">
								<div class="menu-kategori-wrapper">
									<h6 class="title">' . $kategori['nama_kategori'] . '</h6>';
									if ($kategori['deskripsi']) {
										echo '<small clas="menu-kategori-desc">' . $kategori['deskripsi'] . '</small>';
									}
						echo '</div>
							</div>';
					}
					$list_menu = menu_list($val['menu']);
					echo build_menu($list_menu);
				}
				?>
			</nav>
		</div>
		<div class="content">
		<?=!empty($breadcrumb) ? breadcrumb($breadcrumb) : ''?>
		<div class="content-wrapper">
		