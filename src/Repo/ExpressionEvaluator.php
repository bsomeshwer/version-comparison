<?php namespace Someshwer\VersionComparison\Repo;

use Composer\Semver\Comparator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * ExpressionEvaluator class
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 06-10-2018
 */
class ExpressionEvaluator
{

    /**
     * Constant error/exception messages
     */
    const IN_VALID_EXP_MSG = 'Invalid expression!';
    const CHECK_AGAIN_MSG = 'Please check the expression and submit again.';
    const CHECK_LOG_MSG = 'Check log for more information.';

    /**
     * Evaluates expression using expression language and
     * returns boolean value either true or false.
     *
     * @param string $boolean_expression
     * @return boolean
     */
    public function evaluateExpByExpressionLanguage($boolean_expression)
    {
        $expression_language = new ExpressionLanguage();
        return $result = $expression_language->evaluate($boolean_expression);
    }

    /**
     * Evaluating sub expressions using Semver library and
     * returning the results in an array.
     *
     * @param array $sanitized_sub_expressions
     * @return array
     */
    public function evaluateSubExpressions($sanitized_sub_expressions)
    {
        $evaluations = [];
        $evaluations = [];
        foreach ($sanitized_sub_expressions as $expression) {
            $operands = preg_split('/[>,<,<=,>=,==,!=,===,!==]{1,2}/', $expression);
            $operand1 = trim($operands[0]);
            $operand2 = trim($operands[1]);
            preg_match('/[>,<,<=,>=,==,!=,===,!==]{1,2}/', $expression, $matches);
            if (reset($matches) == false) {
                $msg = self::IN_VALID_EXP_MSG . ' ' . self::CHECK_AGAIN_MSG . ' ' . self::CHECK_LOG_MSG;
                // Manually throwing exception
                throw new \Exception($msg);
            }
            $operator = trim(reset($matches));
            $result = Comparator::compare($operand1, $operator, $operand2);
            $evaluations[$expression] = $result ? 1 : 0;
        }
        return $evaluations;
    }

}
