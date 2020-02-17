<?php

namespace PlayOrPay\Tests\Functional;

use ArrayObject;
use Exception;
use IteratorAggregate;
use Nelmio\Alice\Loader\NativeLoader;

class FixtureCollection implements IteratorAggregate
{
    private $objects;

    public function __construct(array $objects)
    {
        $this->objects = $objects;
    }

    public static function fromFile(string $file)
    {
        $loader = new NativeLoader;
        $objects = $loader->loadFile($file)->getObjects();
        return new self($objects);
    }

    public function getIterator()
    {
        return new ArrayObject($this->objects);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function getByName(string $name)
    {
        if ($this->objects[$name]) {
            return $this->objects[$name];
        }

        throw new Exception(sprintf("There is no fixture with name '%s'", $name));
    }

    public function findAllOf(string $class, int $limit = null, array $excluded = [])
    {
        $counter = 0;
        $found = [];
        foreach ($this->objects as $object) {
            if (in_array($object, $excluded, true)) {
                continue;
            }

            if (is_a($object, $class, true)) {
                $found[] = $object;

                $counter++;
                if ($limit && $counter > $limit) {
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * @param string $class
     * @param object[] $excluded
     * @return mixed
     * @throws Exception
     */
    public function getOneOf(string $class, array $excluded = [])
    {
        $entities = $this->findAllOf($class, 1, $excluded);
        if (!$entities) {
            throw new Exception(sprintf("Can't find '%s' object", $class));
        }

        return $entities[0];
    }
}

