<?php
/* @var $this \yii\web\View */
/* @var $content string */
use common\widgets\Alert;
$this->beginContent('@frontend/views/layouts/base.php');
?>
<main class="d-flex">
	<div class="container p-3">
		<?= Alert::widget() ?>
		<?= $content ?>
	</div>
</main>
<?php $this->endContent() ?>
