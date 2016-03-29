<?php
namespace common\components;

use Yii;
use yii\data\BaseDataProvider;
use yii\httpclient\Client;

class CustomDataProvider extends BaseDataProvider
{
    /**
     * @var string name of the CSV file to read
     */
    public $filename;

    /**
     * @var string|callable name of the key column or a callable returning it
     */
    public $key;

    public $url;

    /**
     * @var$totalCount
     */
    public $totalCount;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $url = $this->url;
        $client = new Client();

        $results = $client->createRequest()
            ->setMethod('post')
            ->setUrl($url)
            ->send();


        if($results->isOk){

            $results = $results->getData();
            $results = $results['response']['numFound'];
            $this->totalCount =  $results;
        }
        else{
            $this->totalCount = 0;
        }

    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {

        if (($pagination = $this->getPagination()) !== false) {


            $url = $this->url;

            if(strpos($url, 'Straits Times') !== false){
               // Yii::$app->end($url);
            }
            $client = new Client();

            if (($pagination = $this->getPagination()) !== false) {
                $pagination->totalCount = $this->totalCount;
                $url .= '&start=' .$pagination->getOffset() . '&rows=' . $pagination->getLimit();
            }


            $results = $client->createRequest()
                ->setMethod('post')
                ->setUrl($url)
                ->send();


            if($results->isOk){

                $results = $results->getData();
                $results = $results['response']['docs'];

                $file = fopen(Yii::getAlias('@text'). '/output.txt', 'w+');
                return $results;
            }
            else{
                Yii::$app->end('Failed to response data, report admin');
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareKeys($models)
    {
        if ($this->key !== null) {
            $keys = [];
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        } else {
            return array_keys($models);
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        return $this->totalCount;
    }


}