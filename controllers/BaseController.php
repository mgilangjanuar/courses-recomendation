<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Json;

class BaseController extends Controller
{
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if (! Yii::$app->request->post() && Yii::$app->request->get('spf') == 'navigate') {
            return Json::encode([
                'title' => $this->getContentsBetween($result, '<title>', '</title>'),
                'head' => $this->getContentsBetween($result, '<!-- STYLES CONTENT -->', '<!-- END OF STYLES CONTENT -->'),
                'body' => [
                    'main-content' => $this->getContentsBetween($result, '<!-- MAIN CONTENT -->', '<!-- END OF MAIN CONTENT -->')
                ],
                'foot' => $this->getContentsBetween($result, '<!-- SCRIPTS CONTENT -->', '<!-- END OF SCRIPTS CONTENT -->')
            ]);
        }

        return $result;
    }

    private function getContentsBetween($str, $startDelimiter, $endDelimiter, $index = 0)
    {
        $contents = array();
        $startDelimiterLength = strlen($startDelimiter);
        $endDelimiterLength = strlen($endDelimiter);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
            $contentStart += $startDelimiterLength;
            $contentEnd = strpos($str, $endDelimiter, $contentStart);
            if (false === $contentEnd) {
                break;
            }
            $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
            $startFrom = $contentEnd + $endDelimiterLength;
        }

        return $contents[$index];
    }
}
