<?php
namespace Backelite\AppBundle\ObjectArraySorter;

class ObjectArraySorter
{
    /**
     * TODO: Alias to sortByMethodResult
     * like OAS::sortByFooByName => getFoo()->getName()
     */
    // public static function __callStatic($methodName, $args)
    // {
    // }

    /*
     * Return the first value of an array
     * @param array $array
     * @return mixed
     */
    public static function first(array $array)
    {
        self::isEmptyArray(__METHOD__, $array);
        foreach ($array as $value) {
            return $value;
        }
    }

    /*
     * Return the truncated array with $limit
     * @param array $array
     * @return array
     */
    public static function truncate(array $array, $limit)
    {
        self::isEmptyArray(__METHOD__, $array);
        self::hasAtLeast(__METHOD__, $array, $limit);

        return array_splice($array, $limit);
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
     * Sort by object's attribute
     * @param array $array
     * @param $attributeName
     * @return array
     */
    public static function sortByAttribute(array $array, $attributeName)
    {
        uasort($array, function($a, $b) use($attributeName) {
            return strcmp($a->$attributeName, $b->$attributeName);
        });

        return $array;
    }

    /*
     * Set the method results (can be chained) as array keys (sorted ASC)
     * @param array $array
     * @param $methodName
     * @return array
     */
    public static function sortByMethodResult(array $array, $methodName, $saveDuplicateKeys = false)
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
            if ($saveDuplicateKeys && isset($sortedArray[$k])) {
                $k = self::getUniqueKeyName($k, $sortedArray);
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

    //Protected methods
    protected static function getUniqueKeyName($key, $array)
    {
        do {
            $key .= "'";
        } while(array_key_exists($key, $array));
        return $key;
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
    protected static function hasAtLeast($method, $array, $limit)
    {
        if (count($array) < $limit) {
            throw new \Exception(
                "The limit given to ObjectArraySorter::".$method." is greater than the array."
            );
        }
    }
}
