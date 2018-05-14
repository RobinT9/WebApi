<?php
namespace classes;


class Request{

    public $get;
    public $post;
    public function __construct()
    {
        $this->get = $this->strip($_GET);
        $this->post = $this->strip($_POST);
    }

    public function get($key = null)
    {
        if($key==null){
            return $this->get;
        }else{
            isset($this->get[$key])?$value =  $this->get[$key]:$value = null;
            return $value;
        }

    }

    public function post($key = null)
    {
        if($key==null){
            return $this->post;
        }else{
            isset($this->post[$key])?$value =  $this->post[$key]:$value = null;
            return $value;
        }
    }

    private function strip($data)
    {
        return array_map(function($d){
            return stripslashes(strip_tags($d));
        },$data);
    }


}