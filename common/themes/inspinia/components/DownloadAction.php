<?php
namespace inspinia\components;

use Yii;
use yii\base\Action;
use kartik\mpdf\Pdf;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class DownloadAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $config = $request->post('config');
        $name = ArrayHelper::getValue($config, 'filename', 'export');
        $pdfConfig = ArrayHelper::getValue($config, 'config', []);
        
        $content = $request->post('export_content', 'No data found');
        $this->generatePDF($content, "{$name}.pdf", $pdfConfig);
        return;
    }

    protected function generatePDF($content, $filename, $config = [])
    {
        unset($config['contentBefore'], $config['contentAfter']);
        $config['filename'] = $filename;
        $config['methods']['SetAuthor'] = ['McomScience Solutions'];
        $config['methods']['SetCreator'] = ['Export Extension'];
        $config['content'] = $content;
        $pdf = new Pdf($config);
        echo $pdf->render();
    }
}