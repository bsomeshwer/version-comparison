<?php namespace Someshwer\VersionComparison\Repo;

/**
 * Parser class
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 06-10-2018
 */
class Parser
{

    /**
     * Formats version expression
     *
     * @param string $expression
     * @return string
     */
    public function formatVersionExpression($expression)
    {
        // Converting version numbers into string format
        $expression_to_be_evaluated = preg_replace_callback('/\d+(?:\.\d+)+/', function ($match) {
            return "'" . $match[0] . "'";
        }, $expression);
        return $expression_to_be_evaluated;
    }

    /**
     * Get expression to be evaluated like substitutes version identifier and
     * converts numeric version number into string format.
     *
     * @param string $version_number
     * @param string $expression
     * @return string
     */
    public function getExpressionToBeEvaluated($version_number, $expression)
    {
        // Substituting version number in the expression
        $expression_after_substitution = preg_replace_callback('/\$v/', function ($match) use ($version_number) {
            return $version_number;
        }, $expression);

        // Converting version numbers into string format
        $expression_to_be_evaluated = preg_replace_callback('/\d+(?:\.\d+)+/', function ($match) {
            return "'" . $match[0] . "'";
        }, $expression_after_substitution);
        return $expression_to_be_evaluated;
    }

    /**
     * Sanitizing sub expression inside an array.
     *
     * @param array $sub_expressions
     * @return array
     */
    public function sanitizeSubExpressions($sub_expressions)
    {
        return array_map(function ($item) {
            return str_replace(')', '', str_replace('(', '', trim($item)));
        }, $sub_expressions);
    }

    /**
     * Substituting evaluations in the expressions to form boolean expression.
     *
     * @param array $evaluations_of_sub_expressions
     * @param string $expression_to_be_evaluated
     * @return string
     */
    public function substituteEvaluations($evaluations_of_sub_expressions, $expression_to_be_evaluated)
    {
        // Substituting evaluations in the expression
        $sub_expressions = array_keys($evaluations_of_sub_expressions);
        $evaluations = array_values($evaluations_of_sub_expressions);
        return $boolean_expression = str_replace($sub_expressions, $evaluations, $expression_to_be_evaluated);
    }

}
