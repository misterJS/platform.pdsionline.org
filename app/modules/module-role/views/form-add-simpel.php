<?php
// require_once('app/includes/functions.php');
helper ('html');?>

<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php
		if (!empty($msg)) {
			show_alert($msg);
		}
		
		foreach ($module as $val) {
			$module_option[$val['module_id']] = $val['module_judul'];
		}
		?>
		<form method="post" class="modal-form" id="add-form" action="<?=current_url()?>" >
			<div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Module</label>
					<div class="col-sm-8 form-inline">
						<?=options(['name'=> 'module_id'], $module_option);?>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Role</label>
					<div class="col-sm-8">
						
						<?php 
						$checkbox[] = ['attr' => ['id' => 'check-all', 'name' => 'check_all'], 'label' =>'Check All / Uncheck All'];
						echo checkbox($checkbox, ['check_all']);
						
						echo '<hr class="mt-1 mb-2"/>';
						echo '<div id="check-all-wrapper">';
						$checkbox = [];
						foreach ($role as $val) {
							$checkbox[] = ['attr' => ['id' => $val['role_id'], 'name' => 'role_' . $val['role_id']], 'label' => $val['role_judul']];
						}
						
						if (isset($_POST['module_id'])) {
							$checked = array_keys($_POST);
						} else {
							foreach ($role as $val) {
								$checked[] = 'role_' . $val['role_id'];
							}
						}
						
						echo checkbox($checkbox, $checked);
						echo '</div>';
						?>
						
					</div>
				</div>
				
				<?php 
				$id = '';
				if (!empty($_GET['id'])) {
					$id = $_GET['id'];
				} elseif (!empty($msg['module_id'])) { // ADD Auto Increment
					$id = $msg['module_id'];
				} ?>
				<input type="hidden" name="id" value="<?=$id?>"/>
				<button type="submit" name="submit" value="submit" class="btn btn-primary mt-2">Save</button>
			</div>
		</form>
	</div>
</div>