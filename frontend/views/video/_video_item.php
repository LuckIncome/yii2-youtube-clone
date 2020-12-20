<?php
/** @var $model \common\models\Video */
use yii\helpers\Url;
?>

<div class="card" style="width: 18rem;">
	<a href="<?php echo Url::to(['/video/view', 'id' => $model->video_id]) ?>">
		<div class="embed-responsive embed-responsive-16by9 mb-3">
			<video poster="<?php echo $model->getThumbnailLink() ?>" src="<?php echo $model->getVideoLink() ?>"class="embed-responsive-item"></video>
		</div>
	</a>
	<div class="card-body p-2">
		<h6 class="card-title m-0"><?php echo $model->title ?></h6>
		<p class="text-muted card-text m-0">
			<?php echo \common\helpers\Html::channelLink($model->createdBy) ?>	
		</p>
		<p class="text-muted card-text m-0">
			<?php echo $model->getViews()->count() ?> views . 
			<?php echo Yii::$app->formatter->asRelativeTime($model->created_at) ?>	
		</p>
	</div>
</div>