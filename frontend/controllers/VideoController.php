<?php

namespace frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Video;
use common\models\VideoView;
use common\models\VideoLike;

class VideoController extends Controller
{
	public function behaviors() 
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'only' => ['like', 'dislike', 'history'],
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@']
					]
				]
			],
			'verb' => [
				'class' => VerbFilter::class,
				'actions' => [
					'like' => ['post'],
					'dislike' => ['post']
				]
			]
		];
	}
	public function actionIndex() 
	{
		$dataProvider = new ActiveDataProvider([
			'query' => Video::find()->with('createdBy')->published()->latest(),
			'pagination' => [
				'pageSize' => 2
			]
		]);
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
	public function actionView($id) 
	{
		$this->layout = 'blank';
		$video = $this->findVideo($id);

		$videoView = new VideoView();
		$videoView->video_id = $id;
		$videoView->user_id = \Yii::$app->user->id;
		$videoView->created_at = time();
		$videoView->save();

		$similarVideos = Video::find()
			->published()
			->andWhere(['NOT', ['video_id' => $id]])
			->byKeyword($video->title)
			->limit(10)
			->all();

		return $this->render('view', [
			'model' => $video,
			'similarVideos' => $similarVideos
		]);
	}
	public function actionLike($id) 
	{
		$video = $this->findVideo($id);
		$userId = \Yii::$app->user->id;
		$videoDislike = VideoLike::find()->userIdVideoId($userId, $id)->one();
		if (!$videoDislike) {
			$this->saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
		} else if ($videoDislike->type == VideoLike::TYPE_LIKE) {
			$videoDislike->delete();
		} else {
			$videoDislike->delete();
			$this->saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
		}
	
		return $this->renderAjax('_buttons', [
			'model' => $video
		]);
	}

	public function actionDislike($id) 
	{
		$video = $this->findVideo($id);
		$userId = \Yii::$app->user->id;
		$videoDislike = VideoLike::find()->userIdVideoId($userId, $id)->one();
		if (!$videoDislike) {
			$this->saveLikeDislike($id, $userId, VideoLike::TYPE_DISLIKE);
		} else if ($videoDislike->type == VideoLike::TYPE_DISLIKE) {
			$videoDislike->delete();
		} else {
			$videoDislike->delete();
			$this->saveLikeDislike($id, $userId, VideoLike::TYPE_DISLIKE);
		}
	
		return $this->renderAjax('_buttons', [
			'model' => $video
		]);
	}

	public function actionSearch($keyword) 
	{
		$query = Video::find()
            ->with('createdBy')
            ->published()
            ->latest();
        if ($keyword) {
            $query->byKeyword($keyword);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
		return $this->render('search', [
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionHistory() 
	{
		$query = Video::find()
			->alias('v')
            ->innerJoin("(SELECT video_id, MAX(created_at) as max_date FROM video_view
                    WHERE user_id = :userId
                    GROUP BY video_id) vv", 'vv.video_id = v.video_id', [
                'userId' => \Yii::$app->user->id
            ])
            ->orderBy("vv.max_date DESC");

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
		return $this->render('history', [
			'dataProvider' => $dataProvider,
		]);
	}

	protected function findVideo($id) 
	{
		$video = Video::findOne($id);
		if(!$video) {
			throw new NotFoundHttpException('Video does not exit');
		}
		return $video;
	}

	protected function saveLikeDislike($videoId, $userId, $type) 
	{
		$videoDislike = new VideoLike();
		$videoDislike->video_id = $videoId;
		$videoDislike->user_id = $userId;
		$videoDislike->type = $type;
		$videoDislike->created_at = time();
		$videoDislike->save();
	}
}