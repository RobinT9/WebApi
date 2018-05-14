<?php
namespace classes;


class Common{

    public $request;
    public $ApiCode;

	public function __construct()
    {
        $this->request = new Request();
        $this->ApiCode = Config::get('API_CODE');
    }


}