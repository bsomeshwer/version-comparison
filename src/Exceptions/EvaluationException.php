<?php namespace Someshwer\VersionComparison\Exceptions;

/**
 * Evaluation Exception is a custom exception and will be thrown
 * when ever an expression is unable to evaluate.
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 26-09-2018
 */
class EvaluationException extends \Exception
{

    /**
     * Returns exception/error message
     *
     * @return string
     */
    public function __toString()
    {
        __toString("Invalid expression! And can not be evaluated.");
    }

}
