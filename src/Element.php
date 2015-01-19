<?php

namespace Phpbfg;

use Phpbfg\Exception;

abstract class Element {

    protected $default_parameters = [];
    protected $parameters         = [];
    protected $name   = '';
    protected $type   = '';
    protected $value  = null;
    protected $holder = 'typical';

    public function __construct($name, $type, $parameters = [])
    {
        $this->name       = $name;
        $this->type       = $type;
        $this->default_parameters['id'] = 'bootstrap-form-input-' . $name;
        $this->parameters = $parameters;
    }

    protected function loadLayout($layout)
    {
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $path = $base . 'layouts/elements/' . $layout . '.html';
        if(!file_exists($path) || ($file = file_get_contents($path)) === false){
            throw new Exception('Layout "' . $layout . '" not found!');
        }
        return $file;
    }

    public function render()
    {
        $html_input  = $this->loadLayout($this->type);
        $html_holder = $this->loadLayout('../holders/' . $this->holder);
        $params = array_merge_recursive($this->default_parameters, $this->parameters);
        $params_string = $this->arrayToParams($params);
        return sprintf(
            $html_holder,
            sprintf(
                $html_input,
                $params_string
            ),
            array_key_exists('id', $params) ? $params['id'] : '',
            array_key_exists('label', $params) ? $params['label'] : ''
        );
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    private function arrayToParams(array $array){
        $params = [];
        foreach($array as $key => $value){
            if(is_array($value)){
                $value = implode(' ', $value);
            }
            if(is_int($key)){
                $params[] = $value;
            } else {
                $params[] = $key . '="' . addcslashes($value, '"\\') . '"';
            }
        }
        return implode(' ', $params);
    }
    
}