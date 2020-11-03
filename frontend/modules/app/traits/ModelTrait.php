<?php

namespace frontend\modules\app\traits;

use Yii;
use yii\web\NotFoundHttpException;
use frontend\modules\app\models\TbTicket;
use frontend\modules\app\models\TbServiceGroup;
use frontend\modules\app\models\TbService;
use frontend\modules\app\models\TbCounterServiceType;
use frontend\modules\app\models\TbCounterService;
use frontend\modules\app\models\TbSoundStation;
use frontend\modules\app\models\TbSound;
use frontend\modules\app\models\TbQue;
use frontend\modules\app\models\TbServiceProfile;
use frontend\modules\app\models\TbQtrans;
use frontend\modules\app\models\TbCaller;
use frontend\modules\app\models\TbDisplay;

trait ModelTrait
{
    protected function findModelTicket($id)
    {
        if (($model = TbTicket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelServiceGroup($id)
    {
        if (($model = TbServiceGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelService($id)
    {
        if (($model = TbService::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelCounterServiceType($id)
    {
        if (($model = TbCounterServiceType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelCounterService($id)
    {
        if (($model = TbCounterService::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelSoundStation($id)
    {
        if (($model = TbSoundStation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelSound($id)
    {
        if (($model = TbSound::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelQue($id)
    {
        if (($model = TbQue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelServiceProfile($id)
    {
        if (($model = TbServiceProfile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelQtrans($id)
    {
        if (($model = TbQtrans::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelCaller($id)
    {
        if (($model = TbCaller::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelDisplay($id)
    {
        if (($model = TbDisplay::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}