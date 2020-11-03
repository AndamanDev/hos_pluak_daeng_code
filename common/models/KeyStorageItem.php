<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "key_storage_item".
 *
 * @property string $key
 * @property string $value
 * @property string $comment
 * @property int $updated_at
 * @property int $created_at
 */
class KeyStorageItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'key_storage_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'required'],
            [['value', 'comment'], 'string'],
            [['updated_at', 'created_at'], 'integer'],
            [['key'], 'string', 'max' => 128],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => 'Key',
            'value' => 'Value',
            'comment' => 'Comment',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
