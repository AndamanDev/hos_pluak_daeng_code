<?php
namespace inspinia\behaviors;

use Yii;
use Closure;
use yii\base\Behavior;
use yii\base\Event;
/**

 * use inspinia\behaviors\CoreMultiValueBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => CoreMultiValueBehavior::className(),
 *             'attributes' => [
 *                 ActiveRecord::EVENT_BEFORE_INSERT => 'attribute1',
 *                 ActiveRecord::EVENT_BEFORE_UPDATE => 'attribute2',
 *             ],
 *             'value' => function ($event) {
 *                 return $event->sender[$event->data];
 *             },
 *         ],
 *     ];
 * }
 */
class CoreMultiValueBehavior extends Behavior
{
    /**
     * @var array list of attributes that are to be automatically filled with the value specified via [[value]].
     * The array keys are the ActiveRecord events upon which the attributes are to be updated,
     * and the array values are the corresponding attribute(s) to be updated. You can use a string to represent
     * a single attribute, or an array to represent a list of attributes. For example,
     *
     * ```php
     * [
     *     ActiveRecord::EVENT_BEFORE_INSERT => ['attribute1', 'attribute2'],
     *     ActiveRecord::EVENT_BEFORE_UPDATE => 'attribute2',
     * ]
     * ```
     */
    public $attributes = [];
    /**
     * @var mixed the value that will be assigned to the current attributes. This can be an anonymous function
     * or an arbitrary value. If the former, the return value of the function will be assigned to the attributes.
     * The signature of the function should be as follows,
     *
     * ```php
     * function ($event)
     * {
     *     // return value will be assigned to the attribute
     * }
     * ```
     */
    public $value;


    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_fill_keys(array_keys($this->attributes), 'evaluateAttributes');
    }

    /**
     * Evaluates the attribute value and assigns it to the current attributes.
     * @param Event $event
     */
    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            foreach ($attributes as $attribute) {
				$event->data = $attribute;
				$value = $this->getValue($event);
                // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
                if (is_string($attribute)) {
                    $this->owner->$attribute = $value;
                }
            }
        }
    }

    /**
     * Returns the value of the current attributes.
     * This method is called by [[evaluateAttributes()]]. Its return value will be assigned
     * to the attributes corresponding to the triggering event.
     * @param Event $event the event that triggers the current attribute updating.
     * @return mixed the attribute value
     */
    protected function getValue($event)
    {
        return $this->value instanceof Closure ? call_user_func($this->value, $event) : $this->value;
    }
}
