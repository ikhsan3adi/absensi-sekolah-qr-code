<div class="row mb-3">
	<div class="col">
		<?php if (session()->has('message')) : ?>
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<i class="material-icons">close</i>
				</button>
				<?= session('message') ?>
			</div>
		<?php endif ?>

		<?php if (session()->has('error')) : ?>
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<i class="material-icons">close</i>
				</button>
				<?= session('error') ?>
			</div>
		<?php endif ?>

		<?php if (session()->has('errors')) : ?>
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<i class="material-icons">close</i>
				</button>
				<?php foreach (session('errors') as $error) : ?>
					<span><?= $error ?></span>
				<?php endforeach ?>
			</div>
		<?php endif ?>
	</div>
</div>