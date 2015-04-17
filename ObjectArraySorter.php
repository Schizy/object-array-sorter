<?php
namespace Backelite\AppBundle\ObjectArraySorter;

class ObjectArraySorter
{
    /*
     * Return the first value of an array
     * @param array $array
     * @return mixed
     */
    public function first(array $array)
    {
        self::isEmptyArray(__METHOD__, $array);
        foreach ($array as $value) {
            return $value;
        }
    }

    /*
     * Alias to sortByMethodResult with getId
     * @param array $array
     * @return array
     */
    public static function sortById(array $array)
    {
        return self::sortByMethodResult($array, 'getId');
    }

    /*
     * Set the method results (can be chained) as array keys (sorted ASC)
     * @param array $array
     * @param $methodName
     * @return array
     */
    public static function sortByMethodResult(array $array, $methodName)
    {
        $sortedArray = [];
        $methodName = preg_replace('#\(|\)#', '', $methodName);
        $methodName = explode('->', $methodName);

        foreach ($array as $object) {
            self::isObject($object);
            $k = clone $object;
            foreach ($methodName as $method) {
                self::isMethodExists($k, $method);
                $k = $k->$method();
            }
            $sortedArray[$k] = $object;
        }
        ksort($sortedArray, SORT_FLAG_CASE | SORT_NATURAL);

        return $sortedArray;
    }

    /*
     * Filter an array by testing methods result
     * @param array $array
     * @param $methodName
     * @return array
     */
    public static function filterByMethodResult(array $array, $methodName)
    {
        //TODO: let the method chaining here too
        //TODO: precise the expected result in third argument?

        $sortedArray = [];

        foreach ($array as $i => $object) {
            self::isObject($object);
            self::isMethodExists($object, $methodName);

            if ($object->$methodName()) {
                $sortedArray[$i] = $object;
            }
        }

        return $sortedArray;
    }

    protected static function isObject($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException(
                'The array given to the ObjectArraySorter must contain only objects.'
            );
        }
    }
    protected static function isMethodExists($object, $methodName)
    {
        if (!method_exists($object, $methodName)) {
            throw new \InvalidArgumentException(
                'The method '.$methodName.' is not implemented in '.get_class($object)
            );
        }
    }
    protected static function isEmptyArray($method, array $array)
    {
        if (!$array) {
            throw new \InvalidArgumentException(
                "The array given to ObjectArraySorter::".$method." can't be empty."
            );
        }
    }
}
