<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php
		helper(['html', 'format']);
		if (!empty($msg)) {
			show_message($msg['content'], $msg['status']);
		}
		?>
		<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
			<div class="tab-content" id="myTabContent">
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Pakta Integritas</label>
					<div class="col-sm-5">
						<?php if ($result[0]['pakta_integritas'] !== ''){ ?>
							<button class="btn btn-primary"><a href="<?=BASE_URL . 'public/files/pakta-integritas/' . $result[0]['pakta_integritas']?>" style="color:white" target="_blank"><?=$_SESSION['user']['nama']?></a></button>
						<?php } ?>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Pilih File</label>
					<div class="col-sm-5">
						<input type="file" class="file form-control" name="file_pakta_integritas">
							<?php if (!empty($form_errors['file_pakta_integritas'])) echo '<small class="alert alert-danger">' . $form_errors['file_excel'] . '</small>'?>
							<small class="small" style="display:block">File ekstensi harus .pdf</small>
						<div class="mt-1">Contoh file: <a title="<?=$tabel['user']['file_pakta_integritas']['title']?>" href="<?=$tabel['user']['file_pakta_integritas']['url']?>" target="_blank"><?=$tabel['user']['file_pakta_integritas']['display']?></a></div>
						<small>File harus di print dan di upload kembali dengan tanda tangan basah</small>
						<div class="upload-img-thumb"><span class="img-prop"></span></div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
						<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>