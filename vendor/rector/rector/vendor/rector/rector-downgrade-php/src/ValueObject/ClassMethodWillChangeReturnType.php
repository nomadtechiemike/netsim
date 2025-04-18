<?php

declare (strict_types=1);
namespace Rector\ValueObject;

final class ClassMethodWillChangeReturnType
{
    /**
     * @readonly
     */
    private string $className;
    /**
     * @readonly
     */
    private string $methodName;
    public function __construct(string $className, string $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;
    }
    public function getClassName() : string
    {
        return $this->className;
    }
    public function getMethodName() : string
    {
        return $this->methodName;
    }
}
