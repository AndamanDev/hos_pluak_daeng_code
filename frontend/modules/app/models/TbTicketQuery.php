<?php

namespace frontend\modules\app\models;

/**
 * This is the ActiveQuery class for [[TbTicket]].
 *
 * @see TbTicket
 */
class TbTicketQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TbTicket[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TbTicket|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
