<?php
namespace inspinia\widgets;

use kartik\icons\Icon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu as BaseMenu;

class Menu extends BaseMenu
{
    public $linkTemplate = '<a href="{url}">{icon} <span class="nav-label">{label}</span></a>';

    public $submenuTemplate = "\n<ul class=\"nav nav-second-level collapse\">\n{items}\n</ul>\n";

    public $labelTemplate = '{label}';

    public $activateParents = true;

    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            Html::addCssClass($options, $class);

            $menu = $this->renderItem($item, $options);
            if (!empty($item['items'])) {
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $menu .= strtr($submenuTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                ]);
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }

    protected function renderItem($item, $options = [])
    {
        $linkTemplate = $this->getLinkTemplate($item, $options);
        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $linkTemplate);

            return strtr($template, [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item['label'],
                '{icon}' => isset($item['icon']) ? Icon::show($item['icon'],['style'=>'font-size: 1.5em;']) : '',
                '{badge}' => isset($item['badge']) ? Html::tag('span', $item['badge'], ArrayHelper::getValue($item, 'badgeOptions', ['class' => 'pull-right label label-primary'])) : '',
            ]);
        }

        $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

        return strtr($template, [
            '{label}' => $item['label'],
            '{icon}' => isset($item['icon']) ? Icon::show($item['icon'],['style'=>'font-size: 1.5em;']) : '',
            '{badge}' => isset($item['badge']) ? Html::tag('span', $item['badge'], ArrayHelper::getValue($item, 'badgeOptions', ['class' => 'pull-right label label-primary'])) : '',
        ]);
    }

    protected function getLinkTemplate($item, $options)
    {
        $navheader = ArrayHelper::getValue($options, 'class', '');
        $linkTemplate = $this->linkTemplate;

        if ($navheader == 'nav-header') {
            $linkTemplate = $this->labelTemplate;
        } elseif (!empty($item['items'])) {
            $linkTemplate = '<a href="{url}">{icon} <span class="nav-label">{label}</span><span class="fa arrow"></span></a>';
        } elseif (isset($item['children']) && $item['children']) {
            $linkTemplate = '<a href="{url}">{icon} {label}</a>';
        } elseif (isset($item['badge'])) {
            $linkTemplate = '<a href="{url}">{icon} <span class="nav-label">{label}</span>{badge}</a>';
        }
        return $linkTemplate;
    }
}
