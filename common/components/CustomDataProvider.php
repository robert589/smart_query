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

    /**
     * @var string query of the search engine
     */
    public static $query;
    public $x_query;
    /**
     * @var string name of the media source like BBC, etc
     */
    public static $source;
    public $x_source;
    /**
     * @var string sort_by Popularity or latest
     */
    public static $sort_by;
    public $x_sort_by;
    /**
     * @var category
     */

    public static $category;
    public $x_category;


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

        self::$query = $this->x_query;
        self::$category = $this->x_category;
        self::$sort_by = $this->x_sort_by;
        self::$source = $this->x_source;
        $url = $this->getUrl($this->getPagination());
        $client = new Client();

        $results = $client->createRequest()
            ->setMethod('post')
            ->setUrl($url)
            ->send();

        if (isset($results->getData()['response'])) {
            $this->totalCount = (int)$results->getData()['response']['numFound'];

        } else {
            $this->totalCount = 0;


        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {

        if (($pagination = $this->getPagination()) !== false) {

            $url = $this->getUrl($pagination);


            $client = new Client();

            $results = $client->createRequest()
                ->setMethod('post')
                ->setUrl($url)
                ->send();


            if($results->isOk){


                /*
                if(isset($results->getData()['response'])){
                    $this->getPagination()->totalCount =  (int) $results->getData()['response']['numFound'] ;

                }
                else{
                    $this->getPagination()->totalCount = 0;
                }*/

                $results = $results->getData();
                $results = $results['response']['docs'];
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

    private function getUrl($pagination = null){

        if($this->totalCount == 0 || $this->totalCount == null){
            $pagination->totalCount = 100;
        }
        else{
            $pagination->totalCount = $this->totalCount;
        }

        $limit = $pagination->getPageSize();
        $offset = $pagination->getOffset();

        $url = "http://solr.kenrick95.xyz/solr/cz4034/select?&wt=json&indent=true&start=" . $offset . '&rows=' . $limit;

        //query
        if(self::$query == '' || self::$query == null){
            $url .= '&q=message%3A' . '*' . '';
        }
        else{
            $url .= '&q=message%3A"' . self::$query . '"';
        }

        //source
        if(self::$source != '' || self::$source != null){
            $url .= '&fq=source%3A"' . self::$source . '"';
        }

        //sort_by
        if(self::$sort_by == 'Popularity'){
            $url .= "&sort=like_count+desc";

        }
        else if(self::$sort_by == 'Latest'){
            $url .= "&sort=created_time+desc";
        }

        //category
        if(self::$category != null){
            $url .= '&fq=category%3A' . self::$category;

        }

        if($this->getPagination()->getOffset() > 0){
            Yii::$app->end('heelo' . self::$source);
        }

        return $url;

    }

}