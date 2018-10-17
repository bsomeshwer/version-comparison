<?php namespace Someshwer\VersionComparison\Repo;

use Illuminate\Support\Facades\Log;
use Someshwer\VersionComparison\Lib\ResponseMaker;
use Someshwer\VersionComparison\Repo\ExpressionEvaluator;
use Someshwer\VersionComparison\Repo\Lexer;
use Someshwer\VersionComparison\Repo\Parser;

/**
 * ExpressionValidator class
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 06-10-2018
 */
class ExpressionValidator
{

    /**
     * Allowed operators variable
     *
     * @var array
     */
    private $allowed_operators = ['(', ')', '$', '&', '|', '>', '.', '<', '=', '!', ' ', 'v'];

    /**
     * Allowed digits variable
     *
     * @var array
     */
    private $allowed_digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /**
     * Excluded characters variable
     *
     * @var array
     */
    private $excluded_characters = ['$', 'v'];

    /**
     * Test version number variable
     *
     * @var string
     */
    private $test_version_number = '1.1';

    /**
     * ExpressionEvaluator variable
     *
     * @var ExpressionEvaluator
     */
    private $expression_evaluator;

    /**
     * Constant values for validation messages.
     */
    const VER_EXP_ERR_MSG = 'Version number and expression both are required!';
    const VER_ERR_MSG = 'Version number is required!';
    const VER_EXP_MSG = 'Expression is required!';
    const IN_VALID_EXP_MSG = 'Invalid expression!';
    const IN_APPROPRIATE_EXP__MSG = 'Something is inappropriate with the expression.';
    const CHECK_AGAIN_MSG = 'Please check and submit the expression again.';
    const CHECK_LOG_MSG = 'Check log for more info.';
    const NOT_ALLOWED_MSG = 'are not allowed in the expression.';

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
     * Returns allowed operators and digits
     *
     * @return array
     */
    private function getAllowedOperatorsAndDigits()
    {
        return $allowed_operators_and_digits = array_merge($this->allowed_operators, $this->allowed_digits);
    }

    /**
     * Returns allowed characters
     *
     * @return array
     */
    private function getAllowedCharacterSet()
    {
        return array_diff($this->getAllowedOperatorsAndDigits(), $this->excluded_characters);
    }

    /**
     * Returns invalid characters in the expression
     *
     * @param string $expression
     * @param array $allowed_operators_and_digits
     * @return array
     */
    private function getInvalidCharacters($expression, $allowed_operators_and_digits)
    {
        // Breaking string into single character pieces
        $expression_arr = str_split(trim($expression));
        $invalid_characters = [];
        foreach ($expression_arr as $item) {
            if (!in_array($item, $allowed_operators_and_digits, true)) {
                $invalid_characters[] = $item;
            }
        }
        return $invalid_characters;
    }

    /**
     * Validating expression to allow specified characters.
     *
     * @param string $expression
     * @param integer $flag
     * @return array
     */
    private function validateExpressionData($expression, $flag = null)
    {
        if ($flag == 1) {
            $allowed_operators_and_digits = $this->getAllowedCharacterSet();
        } else {
            $allowed_operators_and_digits = $this->getAllowedOperatorsAndDigits();
        }
        $invalid_characters = $this->getInvalidCharacters($expression, $allowed_operators_and_digits);
        if (count($invalid_characters) > 0) {
            return $invalid_characters;
        } else {
            return [];
        }
    }

    /**
     * Making expression to evaluate-able and evaluating it using expression language
     * and returning boolean value.
     *
     * @param string $expression
     * @param integer $flag
     * @return mixed
     */
    private function validateExpByExpressionLanguage($expression, $flag = null)
    {
        $parser = new Parser();
        if ($flag == 1) {
            $expression_to_be_evaluated = $parser->formatVersionExpression($expression);
        } else {
            $expression_to_be_evaluated = $parser->getExpressionToBeEvaluated($this->test_version_number, $expression);
        }
        try {
            $result = $this->expression_evaluator->evaluateExpByExpressionLanguage($expression_to_be_evaluated);
            if (!is_bool($result)) {
                return Self::IN_VALID_EXP_MSG . ' ' . self::IN_APPROPRIATE_EXP__MSG . ' ' . self::CHECK_AGAIN_MSG;
            }
            return $result;
        } catch (\Exception $e) {
            Log::error($e);
            return Self::IN_VALID_EXP_MSG . ' ' . $e->getMessage() . ' ' . self::CHECK_LOG_MSG;
        }
    }

    /**
     * Analyzing expression by breaking expression into individual sub expressions and
     * evaluating each sub expression and then building new boolean expression with the
     * result of individual sub expressions and finally evaluating the boolean expression.
     *
     * @param string $expression
     * @param integer $flag
     * @return mixed
     */
    private function validateExpByAnalysis($expression, $flag = null)
    {
        $parser = new Parser();
        $lexer = new Lexer();
        try {
            if ($flag == 1) {
                $expression_to_be_evaluated = $parser->formatVersionExpression($expression);
            } else {
                $expression_to_be_evaluated = $parser->getExpressionToBeEvaluated($this->test_version_number, $expression);
            }
            $sub_expressions = $lexer->getSubExpressions($expression_to_be_evaluated);
            $sanitized_sub_expressions = $parser->sanitizeSubExpressions($sub_expressions);
            $evaluations_of_sub_expressions = $this->expression_evaluator->evaluateSubExpressions($sanitized_sub_expressions);
            $boolean_expression = $parser->substituteEvaluations($evaluations_of_sub_expressions, $expression_to_be_evaluated);
            $result = $this->expression_evaluator->evaluateExpByExpressionLanguage($boolean_expression);
            $result = ResponseMaker::makeResult($result);
            if (!is_bool($result)) {
                return Self::IN_VALID_EXP_MSG . ' ' . self::IN_APPROPRIATE_EXP__MSG . ' ' . self::CHECK_AGAIN_MSG;
            }
            return 'SUCCESS';
        } catch (\Exception $e) {
            \Log::error($e);
            return Self::IN_VALID_EXP_MSG . ' ' . $e->getMessage() . '. ' . self::CHECK_LOG_MSG;
        }
    }

    /**
     * Validates pure version expression.
     *
     * @param string $expression
     * @return mixed
     */
    public function validatePureVersionExpression($expression)
    {
        if (!$expression) {
            return self::VER_EXP_MSG;
        }
        $data_validation = $this->validateExpressionData($expression, 1);
        if (count($data_validation)) {
            return Self::IN_VALID_EXP_MSG . ' [' . implode(', ', $data_validation) . '] ' . self::NOT_ALLOWED_MSG;
        }
        $evaluation = $this->validateExpByExpressionLanguage($expression, 1);
        if (!is_bool($evaluation)) {
            return $evaluation;
        }
        $analysis = $this->validateExpByAnalysis($expression, 1);
        if ($analysis != 'SUCCESS') {
            return $analysis;
        }
        return null;
    }

    /**
     * Validates version and expression
     *
     * @param string $version_number
     * @param string $expression
     * @return mixed
     */
    public function validateVersionAndExpression($version_number, $expression)
    {
        if ((!$version_number) && (!$expression)) {
            return self::VER_EXP_ERR_MSG;
        }
        if (($version_number) && (!$expression)) {
            return self::VER_EXP_MSG;
        }
        if ((!$version_number) && ($expression)) {
            return self::VER_ERR_MSG;
        }
        $data_validation = $this->validateExpressionData($expression);
        if (count($data_validation)) {
            return Self::IN_VALID_EXP_MSG . ' [' . implode(', ', $data_validation) . '] ' . self::NOT_ALLOWED_MSG;
        }
        $evaluation = $this->validateExpByExpressionLanguage($expression);
        if (!is_bool($evaluation)) {
            return $evaluation;
        }
        $analysis = $this->validateExpByAnalysis($expression);
        if ($analysis != 'SUCCESS') {
            return $analysis;
        }
        return null;
    }

}
