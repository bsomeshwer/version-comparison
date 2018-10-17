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
                throw new \Exception('xxx');
                //TODO:: Validation need to be done.
            }
            $operator = trim(reset($matches));
            $result = Comparator::compare($operand1, $operator, $operand2);
            $evaluations[$expression] = $result ? 1 : 0;
        }
        return $evaluations;
    }

}
