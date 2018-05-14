<?php
namespace classes;


class Trade extends Common{

    public $redis;

    public function __construct(\Redis $redis)
    {
        parent::__construct();
        $this->redis = $redis;
    }

    public function getKlineData()
	{
        $resolution = $this->request->get('resolution');//$_GET['resolution'];
        $symbol     = $this->request->get('symbol');
        if(empty($symbol)||empty($resolution)){
            return $data = [
                'code'      =>  400,
                'message'   =>  $this->ApiCode[400],
                'data'      =>  ''
            ];
        }

        $resolution_array = [1,5,15,30,60,1440,10080,30000];
        if(!in_array($resolution, $resolution_array)){
            return $data = [
                'code'      =>  100,
                'message'   =>  $this->ApiCode[100],
                'data'      =>  ''
            ];
        }

        $min = $this->redis->get($symbol.':klinedata:TransactionOrder_'.$symbol.'_'.$resolution);
        if(!$min){
            return $data = [
                'code'      =>  500,
                'message'   => $this->ApiCode[500],
                'data'      =>  ''
            ];
        }

        $min = json_decode($min,true);
        $newData = array_map(function($m){
            $m['high'] = $m['maxPrice'];
            $m['low'] = $m['minPrice'];
            $m['time'] = strtotime($m['transactionEndTime'])*1000;
            $m['isBarClosed'] = true;
            $m['isLastBar'] = false;
            $m['open'] = $m['startPrice'];
            $m['close'] = $m['endPrice'];
            $m['volume'] = $m['transactionCount'];
            unset($m['endPrice'],$m['minPrice'],$m['maxPrice'],$m['transactionEndTime'],$m['startPrice'],$m['endPrice'],$m['transactionCount'],$m['saasId'],$m['id'],$m['transactionTime']);
            return $m;
        },$min);

        $newData = array_reverse($newData);

        unset($newData[800]);

        return $data = [
            'code'      =>  0,
            'message'   => 'success',
            'data'      =>  $newData
        ];
	}


	public function getPriceDemical()
    {
        $symbol = $this->request->get('symbol');
        if(empty($symbol)){
            return $data = [
                'code'      =>  400,
                'message'   => $this->ApiCode[400],
                'data'      =>  ''
            ];
        }
        $price = $this->redis->get($symbol.':currentExchangPrice');
        if(!$price){
            return $data = [
                'code'      =>  500,
                'message'   =>  $this->ApiCode[500],
                'data'      =>  ''
            ];
        }
        $price = floatval($price);
        $demical = explode('.',$price);
        $demical = $demical[1];
        $len = strlen($demical);
        $pricedemical = pow(10,$len);
        return $data = [
            'code'      =>  0,
            'message'   => 'success',
            'data'      =>  $pricedemical
        ];
    }


}