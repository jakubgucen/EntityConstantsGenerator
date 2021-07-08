<?php

namespace JakubGucen\EntityConstantsGenerator\Traits;

trait MetaEntityTrait
{
    /**
     * @param string $propertyName
     * @return mixed
     */
    public function get(string $propertyName)
    {
        $func = 'get' . ucfirst($propertyName);

        return $this->$func();
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return self
     */
    public function set(string $propertyName, $value): self
    {
        $func = 'set' . ucfirst($propertyName);

        return $this->$func($value);
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return self
     */
    public function add(string $propertyName, $value): self
    {
        $func = 'add' . ucfirst($propertyName);

        return $this->$func($value);
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return self
     */
    public function remove(string $propertyName, $value): self
    {
        $func = 'remove' . ucfirst($propertyName);

        return $this->$func($value);
    }
}
