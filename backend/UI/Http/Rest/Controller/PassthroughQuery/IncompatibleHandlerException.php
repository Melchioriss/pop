<?php

namespace PlayOrPay\UI\Http\Rest\Controller\PassthroughQuery;

use Exception;

class IncompatibleHandlerException extends Exception
{
    public static function becauseItShouldReturnCollectionOrArray(string $controller, string $command, $retuned)
    {
        $retunedType = gettype($retuned);
        if ($retunedType === 'object') {
            $retunedType = get_class($retuned);
        }

        return new self(sprintf("Command '%s' handler isn't compatible with '%s' controller because it returned '%s', but we expect array of Collection", $command, $controller, $retunedType));
    }
}
