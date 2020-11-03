<?php
namespace inspinia\widgets\tablecolumn;

use Yii;
use yii\i18n\Formatter;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

class ColumnData extends BaseObject
{
    public $columns = [];

    public $dataProvider;

    public $emptyText;

    public $emptyCell = '&nbsp;';

    public $formatter;

    public $dataColumnClass;

    public $itemLabelSingle;

    public $title;

    public function init()
    {
        parent::init();

        if ($this->formatter === null) {
            $this->formatter = Yii::$app->getFormatter();
        } elseif (is_array($this->formatter)) {
            $this->formatter = Yii::createObject($this->formatter);
        }
        if (!$this->formatter instanceof Formatter) {
            throw new InvalidConfigException('The "formatter" property must be either a Format object or a configuration array.');
        }

        if ($this->dataProvider === null) {
            throw new InvalidConfigException('The "dataProvider" property must be set.');
        }
        if ($this->emptyText === null) {
            $this->emptyText = Yii::t('yii', 'No results found.');
        }
        if (!isset($this->itemLabelSingle)) {
            $this->itemLabelSingle = 'item';
        }
        $this->dataProvider->pagination->pageSize = false;
        $this->initColumns();
    }

    protected function initColumns()
    {
        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = Yii::createObject(array_merge([
                    'class' => $this->dataColumnClass ?: ColumnTable::className(),
                ], $column));
            }
            $this->columns[$i] = $column;
        }
    }

    protected function createDataColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
        }
        return Yii::createObject([
            'class' => $this->dataColumnClass ?: ColumnTable::className(),
            'attribute' => $matches[1],
            'format' => isset($matches[3]) ? $matches[3] : 'text',
        ]);
    }

    public function renderDataColumns()
    {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            $rows[] = $this->renderDataRow($model, $key, $index);
        }
        if (empty($rows) && $this->emptyText !== false) {
            return [];
        }
        return $rows;
    }

    public function renderDataRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells['index'] = ($index + 1);
            $cells['DT_RowId'] = $key;
            $cells['DT_RowAttr'] = ['data-key' => is_array($key) ? json_encode($key) : (string) $key];
            $cells[trim($column->attribute)] = $column->renderDataCell($model, $key, $index);
        }
        return $cells;
    }
}