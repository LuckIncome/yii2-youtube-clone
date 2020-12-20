<aside class="shadow">
<?php echo \yii\bootstrap4\Nav::widget([
	'options' => [
		'class' => 'd-flex flex-column nav-pills'
	],
	'items' => [
		[
			'label' => 'Dashboard',
			'url' => ['/site/index']
		],
		[
			'label' => 'Video',
			'url' => ['/video/index']
		],
	]
]) ?>
</aside>
