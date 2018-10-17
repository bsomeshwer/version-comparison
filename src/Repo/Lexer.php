<?php namespace Someshwer\VersionComparison\Repo;

/**
 * Lexer class
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 06-10-2018
 */
class Lexer
{

    /**
     * Returns sub expressions by breaking given expression into
     * individual sub expressions using PHP regular expressions.
     */
    public function getSubExpressions($expression_to_be_evaluated)
    {
        // Breaking an expression into sub expressions by logical operators &&, ||, etc.
        $sub_expressions = preg_split('/[&&,||]{2}/', $expression_to_be_evaluated);
        return $sub_expressions;
    }

}
