<?php

    namespace YS\Csv;

    use Exception;

    /**
     * Class Csv
     * @author @iyogesharma
     */
    abstract class Csv
    {
        /**
         * Magic method that get executed when user
         * call some function which is not defined in class
         * The service use this function to serve as magic getter and setter
         * @param string $name method name
         * @param array $arguments
         * @return mixed
         * @throws Exception
         */
        public function __call($name, $arguments)
        {
            if (strpos($name, 'get') !== false) {
                $name = explode('get', $name)[1];
                $prop = lcfirst($name);
                return $this->$prop;
            } else if (strpos($name, 'set') !== false) {

                $name = explode('set', $name)[1];
                $prop = lcfirst($name);
                $this->$prop = $arguments[0];
            } else {
                throw new Exception(" call to an undefined method {$name} on class ");
            }
        }
    }
