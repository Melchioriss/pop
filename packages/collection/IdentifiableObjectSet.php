<?php

namespace Insideone\Package\Collection;

use Malarzm\Collections\ObjectSet;

/**
 * @property Identifiable[] $elements
 */
class IdentifiableObjectSet extends ObjectSet
{
    /**
     * @param array $elements
     *
     * @throws UnidentifiableObjectException
     */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!$element instanceof Identifiable) {
                throw UnidentifiableObjectException::create($element);
            }
        }
        parent::__construct($elements);
    }

    public function identities()
    {
        $identities = [];
        foreach ($this->elements as $element) {
            $identities = $element->getIdentity();
        }

        return $identities;
    }
}
