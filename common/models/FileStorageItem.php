<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "file_storage_item".
 *
 * @property int $id
 * @property string $component
 * @property string $base_url
 * @property string $path
 * @property string $type
 * @property int $size
 * @property string $name
 * @property string $upload_ip
 * @property int $created_at
 */
class FileStorageItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_storage_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['component', 'base_url', 'path', 'created_at'], 'required'],
            [['size', 'created_at'], 'integer'],
            [['component', 'type', 'name'], 'string', 'max' => 255],
            [['base_url', 'path'], 'string', 'max' => 1024],
            [['upload_ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'component' => 'Component',
            'base_url' => 'Base Url',
            'path' => 'Path',
            'type' => 'Type',
            'size' => 'Size',
            'name' => 'Name',
            'upload_ip' => 'Upload Ip',
            'created_at' => 'Created At',
        ];
    }
}
