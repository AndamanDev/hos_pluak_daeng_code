<?php

namespace frontend\modules\app\models;

/**
 * This is the ActiveQuery class for [[TbServiceGroup]].
 *
 * @see TbServiceGroup
 */
class TbServiceGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TbServiceGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TbServiceGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
