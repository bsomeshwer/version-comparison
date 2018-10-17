<?php namespace Someshwer\VersionComparison\Lib;

use Composer\Semver\Comparator;
use Illuminate\Support\Facades\Log;
use Someshwer\VersionComparison\Repo\Lexer;
use Someshwer\VersionComparison\Repo\Parser;
use Someshwer\VersionComparison\Lib\ResponseMaker;
use Someshwer\VersionComparison\Repo\VersionValidator;
use Someshwer\VersionComparison\Repo\ExpressionEvaluator;
use Someshwer\VersionComparison\Facades\ExpressionValidator;

/**
 * VersionComparator class
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 06-10-2018
 */
class VersionComparator
{

    /**
     * ExpressionEvaluator variable
     *
     * @var ExpressionEvaluator
     */
    private $expression_evaluator;

    /**
     * ExpressionEvaluator constructor function
     *
     * @param ExpressionEvaluator $expression_evaluator
     */
    public function __construct(ExpressionEvaluator $expression_evaluator)
    {
        $this->expression_evaluator = $expression_evaluator;
    }

    /**
     * Evaluates expression
     *
     * @param string $expression
     * @param string $version_number
     * @return ResponseMaker
     */
    private function evaluateExpression($expression, $version_number = null)
    {
        $parser = new Parser();
        $lexer = new Lexer();
        try {
            if ($version_number != null) {
                $expression_to_be_evaluated = $parser->getExpressionToBeEvaluated(trim($version_number), trim($expression));
            } else {
                $expression_to_be_evaluated = $parser->formatVersionExpression($expression);
            }
            $sub_expressions = $lexer->getSubExpressions($expression_to_be_evaluated);
            $sanitized_sub_expressions = $parser->sanitizeSubExpressions($sub_expressions);
            $evaluations_of_sub_expressions = $this->expression_evaluator->evaluateSubExpressions($sanitized_sub_expressions);
            $boolean_expression = $parser->substituteEvaluations($evaluations_of_sub_expressions, $expression_to_be_evaluated);
            $result = $this->expression_evaluator->evaluateExpByExpressionLanguage($boolean_expression);
            return ResponseMaker::makeSuccessResponse($result);
        } catch (\Exception $e) {
            Log::error($e);
            $err_msg = 'Invalid expression! ' . $e->getMessage() . '. ' . 'Check log for more info.';
            return ResponseMaker::makeValidationResponse($err_msg);
        }
    }

    /**
     * Compares two version strings.
     * Compares two version string using specified operator
     * and returns result.
     *
     * @param string $version1
     * @param string $version2
     * @param string $operator
     * @return ResponseMaker
     */
    public function compare($version1 = null, $version2 = null, $operator = null)
    {
        $version_validator = new VersionValidator();
        $version_validation = $version_validator->validateVersionNumber($version1, $version2);
        if ($version_validation) {
            return ResponseMaker::makeValidationResponse($version_validation);
        }
        $operator_validation = $version_validator->validateOperator($operator);
        if ($operator_validation) {
            return ResponseMaker::makeValidationResponse($operator_validation);
        }
        $result = Comparator::compare($version1, $operator, $version2);
        return ResponseMaker::makeSuccessResponse($result);
    }

    /**
     * Evaluates version expression
     *
     * @param string $expression
     * @return ResponseMaker
     */
    public function evaluate($expression = null)
    {
        $validation = ExpressionValidator::validatePureVersionExpression(trim($expression));
        if ($validation) {
            return ResponseMaker::makeValidationResponse($validation);
        }
        return $this->evaluateExpression($expression);
    }

    /**
     * Substitutes version number and evaluates the expression.
     *
     * @param string $version_number
     * @param string $expression
     * @return ResponseMaker
     */
    public function substituteThenEvaluate($version_number = null, $expression = null)
    {
        $validation = ExpressionValidator::validateVersionAndExpression($version_number, $expression);
        if ($validation) {
            return ResponseMaker::makeValidationResponse($validation);
        }
        return $this->evaluateExpression($expression, $version_number);
    }

}
