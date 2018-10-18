<?php namespace Someshwer\VersionComparison\Lib;

use Composer\Semver\Comparator;
use Illuminate\Support\Facades\Log;
use Someshwer\VersionComparison\Facades\ExpressionValidator;
use Someshwer\VersionComparison\Lib\ResponseMaker;
use Someshwer\VersionComparison\Repo\ExpressionEvaluator;
use Someshwer\VersionComparison\Repo\Lexer;
use Someshwer\VersionComparison\Repo\Parser;
use Someshwer\VersionComparison\Repo\VersionValidator;

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
     * This method returns some useful information about the package.
     *
     * @return array
     */
    public function info()
    {
        $description = 'Laravel Version Comparison - This Laravel package compares two version strings
            and gives the Boolean result. This package also resolves version expressions like
            (($v > 1.24.0) && ($v < 1.25.1.0)) || ($v == 1.26 || $v == 1.27) where $v must be
            substituted with the version number to be compared. Hence the package can be used
            for version expressions evaluation.';
        return [
            'package_name' => 'Laravel - VersionComparison',
            'description' => preg_replace('/\s+/', ' ', trim($description)),
            'latest_release' => '2.1.1',
            'stable_version' => '2.1.1',
            'author' => 'Someshwer Bandapally <bsomeshwer89@gmail.com>',
        ];
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
