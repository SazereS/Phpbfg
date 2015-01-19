<?php

namespace Phpbfg;

abstract class Form {

    protected $options = [];

    /**
     * @var \Phpbfg\Element[]
     */
    protected $elements = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->initialize();
    }

    abstract public function initialize();

    /**
     * Append new element to form
     * @param string $name Name for element
     * @param string $type Type of element. Allowed values: text, password, email, file, checkbox, textarea, radio, static, button
     * @param array $parameters
     * @return \Phpbfg\Element
     */
    public function addElement($name, $type, $parameters = [])
    {
        $class = '\\Phpbfg\\Element\\' . ucfirst($type);
        $this->elements[$name] = new $class($name, $type, $parameters);
        return $this->elements[$name];
    }

    /**
     * Removes element from the form
     * @param string $name
     */
    public function removeElement($name)
    {
        if(array_key_exists($this->elements, $name)){
            unset($this->elements[$name]);
        }
    }

    public function fillForm()
    {

    }

    private function prepareToFill(array $array)
    {

    }

    public function validate()
    {

    }

    public function render()
    {
        $elements = [];
        foreach ($this->elements as $element) {
            $elements[] = $element->render();
        }


        $form = $this->loadLayout('form');


        return sprintf($form, implode('',$elements), $this->arrayToParams($this->options));
    }

    private function loadLayout($layout)
    {
        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $path = $base . 'layouts/forms/' . $layout . '.html';
        if(!file_exists($path) || ($file = file_get_contents($path)) === false){
            throw new Exception('Layout "' . $layout . '" not found!');
        }
        return $file;
    }

    private function arrayToParams(array $array){
        $params = [];
        foreach($array as $key => $value){
            if(is_int($key)){
                $params[] = $value;
            } else {
                $params[] = $key . '="' . addcslashes($value, '"\\') . '"';
            }
        }
        return implode(' ', $params);
    }

}