<?php
namespace inspinia\widgets\tablecolumn;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use Closure;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class ActionTable extends BaseObject
{
    public $attribute = 'actions';

    public $title;

    public $controller;

    public $headerOptions = ['class' => 'dt-center dt-body-center dt-nowrap action-column'];

    public $template = '{view} {update} {delete}';

    public $buttons = [];

    public $visibleButtons = [];

    public $urlCreator;

    public $buttonOptions = [];

    public $viewOptions = [];

    public $updateOptions = [];

    public $deleteOptions = [];

    public $dropdownButton = ['class' => 'btn btn-default'];

    public $dropdownMenu = ['class' => 'text-left'];

    public $dropdownOptions = [];

    public $dropdown = false;

    public $options = ["className" => "dt-center dt-nowrap"];

    public $itemLabelSingle;

    protected $_isDropdown = false;

    public $emptyCell = '&nbsp;';

    public function init()
    {
        parent::init();
        $this->_isDropdown = $this->dropdown;
        $this->initDefaultButtons();
    }

    protected function initDefaultButtons()
    {
        $this->setDefaultButton('view', Yii::t('yii', 'View'), 'eye-open');
        $this->setDefaultButton('update', Yii::t('yii', 'Update'), 'pencil');
        $this->setDefaultButton('delete', Yii::t('yii', 'Delete'), 'trash');
    }

    protected function setDefaultButton($name, $title, $icon)
    {
        if (isset($this->buttons[$name])) {
            return;
        }
        $this->buttons[$name] = function ($url) use ($name, $title, $icon) {
            $opts = "{$name}Options";
            $options = ['title' => $title, 'aria-label' => $title, 'data-pjax' => '0'];
            if ($name === 'delete') {
                $item = isset($this->itemLabelSingle) ? $this->itemLabelSingle : Yii::t('yii', 'item');
                $options['data-method'] = 'post';
                $options['data-confirm'] = Yii::t('yii', 'Are you sure you want to delete this item?', ['item' => $item]);
            }
            $options = array_replace_recursive($options, $this->buttonOptions, $this->$opts);
            $label = $this->renderLabel($options, $title, ['class' => "glyphicon glyphicon-{$icon}"]);
            $link = Html::a($label, $url, $options);
            if ($this->_isDropdown) {
                $options['tabindex'] = '-1';
                return "<li>{$link}</li>\n";
            } else {
                return $link;
            }
        };
    }

    protected function renderLabel(&$options, $title, $iconOptions = [])
    {
        $label = ArrayHelper::remove($options, 'label');
        if (is_null($label)) {
            $icon = $this->renderIcon($options, $iconOptions);
            if (strlen($icon) > 0) {
                $label = $this->_isDropdown ? ($icon . ' ' . $title) : $icon;
            } else {
                $label = $title;
            }
        }
        return $label;
    }

    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }

    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
        }
        $params = is_array($key) ? $key : ['id' => (string) $key];
        $params[0] = $this->controller ? $this->controller . '/' . $action : $action;
        return Url::toRoute($params);
    }

    public function renderDataCell($model, $key, $index)
    {
        return $this->renderDataCellContent($model, $key, $index);
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $content = $this->renderCellContent($model, $key, $index);
        $options = $this->dropdownButton;
        $trimmed = trim($content);
        if ($this->_isDropdown  && !empty($trimmed)) {
            $label = ArrayHelper::remove($options, 'label', Yii::t('yii', 'Actions'));
            $caret = ArrayHelper::remove($options, 'caret', ' <span class="caret"></span>');
            $options = array_replace_recursive($options, ['type' => 'button', 'data-toggle' => 'dropdown']);
            Html::addCssClass($options, 'dropdown-toggle');
            $button = Html::button($label . $caret, $options);
            Html::addCssClass($this->dropdownMenu, 'dropdown-menu');
            $dropdown = $button . PHP_EOL . Html::tag('ul', $content, $this->dropdownMenu);
            Html::addCssClass($this->dropdownOptions, 'dropdown');
            return Html::tag('div', $dropdown, $this->dropdownOptions);
        }
        return $content;
    }

    public function renderHeaderCell()
    {
        return Html::tag('th', $this->renderHeaderCellContent(), $this->headerOptions);
    }

    protected function renderHeaderCellContent()
    {
        return trim($this->title) !== '' ? $this->title : $this->getHeaderCellLabel();
    }

    protected function getHeaderCellLabel()
    {
        return $this->emptyCell;
    }

    protected function renderCellContent($model, $key, $index)
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if (isset($this->visibleButtons[$name])) {
                $isVisible = $this->visibleButtons[$name] instanceof \Closure
                    ? call_user_func($this->visibleButtons[$name], $model, $key, $index)
                    : $this->visibleButtons[$name];
            } else {
                $isVisible = true;
            }
            if ($isVisible && isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);
                return call_user_func($this->buttons[$name], $url, $model, $key);
            }
            return '';
        }, $this->template);
    }

    protected function renderIcon(&$options, $iconOptions = [])
    {
        $icon = ArrayHelper::remove($options, 'icon');
        if ($icon === false) {
            $icon = '';
        } elseif (!is_string($icon)) {
            if (is_array($icon)) {
                $iconOptions = array_replace_recursive($iconOptions, $icon);
            }
            $tag = ArrayHelper::remove($iconOptions, 'tag', 'span');
            $icon = Html::tag($tag, '', $iconOptions);
        }
        return $icon;
    }
}