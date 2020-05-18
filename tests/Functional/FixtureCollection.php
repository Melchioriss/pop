<?php

namespace PlayOrPay\Tests\Functional;

use ArrayObject;
use Exception;
use IteratorAggregate;
use Nelmio\Alice\Loader\NativeLoader;

/**
 * @implements IteratorAggregate<number, object>
 */
class FixtureCollection implements IteratorAggregate
{
    /** @var object[] */
    private $objects;

    /**
     * @param object[] $objects
     */
    public function __construct(array $objects)
    {
        $this->objects = $objects;
    }

    public static function fromFile(string $file): self
    {
        $loader = new NativeLoader();
        $objects = $loader->loadFile($file)->getObjects();

        return new self($objects);
    }

    public function getIterator()
    {
        return new ArrayObject($this->objects);
    }

    /**
     * @param string $name
     *
     * @throws Exception
     *
     * @return object
     */
    public function get(string $name): object
    {
        if (!empty($this->objects[$name])) {
            return $this->objects[$name];
        }

        throw new Exception(sprintf("There is no fixture with name '%s'", $name));
    }

    /**
     * @param string $class
     * @param int|null $limit
     * @param object[] $excluded
     *
     * @return object[]
     */
    public function findAllOf(string $class, int $limit = null, array $excluded = []): array
    {
        $counter = 0;
        $found = [];
        foreach ($this->objects as $object) {
            if (in_array($object, $excluded, true)) {
                continue;
            }

            if (is_a($object, $class, true)) {
                $found[] = $object;

                ++$counter;
                if ($limit && $counter > $limit) {
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * @param string   $class
     * @param object[] $excluded
     *
     * @throws Exception
     *
     * @return object
     */
    public function getOneOf(string $class, array $excluded = []): object
    {
        $entities = $this->findAllOf($class, 1, $excluded);
        if (!$entities) {
            throw new Exception(sprintf("Can't find '%s' object", $class));
        }

        return $entities[0];
    }
}
