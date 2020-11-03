<?php
namespace frontend\modules\app\components;

use frontend\modules\app\traits\ModelTrait;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

class SoundComponent extends Component
{
    use ModelTrait;

    public $que_number;

    public $counter_id;

    private $_source;

    public function init()
    {
        parent::init();
        if ($this->que_number == null || $this->counter_id == null) {
            throw new InvalidConfigException(
                "{counter_id} or {que_number} was not found. \n\n"
            );
        }
        $this->getMedia();
    }

    private function getMedia()
    {
        if ($this->que_number != null && $this->counter_id != null) {
            $txt_split = str_split($this->que_number);
            $modelCounter = $this->findModelCounterService($this->counter_id);
            $modelSound = $modelCounter->sound;//เสียงหมายเลข (หนึ่ง สอง สาม)
            $soundService = $modelCounter->soundService;//เสียงบริการ (ที่ช่อง ที่ห้อง ที่โต๊ะ)
            $basePath = "/media/" . $modelSound['sound_path_name'];
            $begin = [$basePath . "/please.wav"]; //เชิญหมายเลข
            $end = [//ที่โต๊ะ 1 ค่ะ
                "/media/" . $soundService['sound_path_name'] . '/' . $soundService['sound_name'],
                $basePath . '/' . $modelSound['sound_name'],
                $basePath . '/' . $modelSound['sound_path_name'] . '_Sir.wav',
            ];

            $sound = array_map(function ($num) use ($basePath, $modelSound) {//A001
                return $basePath . '/' . $modelSound['sound_path_name'] . '_' . $num . '.wav';
            }, $txt_split);
            $sound = ArrayHelper::merge($begin, $sound);//[เชิญหมายเลข, A001]
            $sound = ArrayHelper::merge($sound, $end);// [เชิญหมายเลขA001, ที่โต๊ะ 1 ค่ะ]
            $this->_source = $sound;
        }
    }

    public function getSource()
    {
        return $this->_source;
    }
}
