<?php namespace JonathanTorres\Construct;

use Illuminate\Support\Str as StringHelper;

class Str
{

    /**
     * Regex to match project name against.
     * Must be: vendor/package
     *
     * @var string
     **/
    protected $regEx = '{^[A-Za-z0-9][A-Za-z0-9_.-]*/[A-Za-z0-9][A-Za-z0-9_.-]*$}u';

    /**
     * Check if the entered project name is valid.
     *
     * @param string $name
     *
     * @return boolean
     **/
    public function isValid($name)
    {
        if (preg_match($this->regEx, $name) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Convert string to studly case.
     * Ex: jonathan -> Jonathan
     *
     * @param string $string
     *
     * @return string
     **/
    public function toStudly($string)
    {
        return StringHelper::studly($string);
    }

    /**
     * Convert string to lower case.
     * Ex: Jonathan -> jonathan
     *
     * @param string $string
     *
     * @return string
     **/
    public function toLower($string)
    {
        return strtolower($string);
    }

    /**
     * Convert string to camel case.
     *
     * @param  string  $string
     * @param  boolean $capitalizeFirstCharacter Defaults to false.
     *
     * @return string
     */
    public function toCamelCase($string, $capitalizeFirstCharacter = false)
    {
       $string = str_replace(
           ' ',
           '',
           ucwords(str_replace(array('-', '_'), ' ', $string))
       );

       if (!$capitalizeFirstCharacter) {
           $string = lcfirst($string);
       }

       return $string;
    }

    /**
     * Split project name in a pretty array.
     *
     * @param string $string
     *
     * @return array
     **/
    public function split($string)
    {
        $project = explode('/', $string);

        return [
            'vendor' => $project[0],
            'project' => $project[1],
        ];
    }
}
