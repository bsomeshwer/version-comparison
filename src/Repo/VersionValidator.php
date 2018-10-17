<?php namespace Someshwer\VersionComparison\Repo;

/**
 * VersionValidator class
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 06-10-2018
 */
class VersionValidator
{

    /**
     * Allowed version characters variable
     *
     * @var array
     */
    private $allowed_version_characters = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.'];

    /**
     * Allowed operators variable
     *
     * @var array
     */
    private $allowed_operators = ['<', '>', '=', '!'];

    /**
     * Validates version numbers
     *
     * @param mixed $version1
     * @param mixed $version2
     * @return mixed
     */
    public function validateVersionNumber($version1 = null, $version2 = null)
    {
        if ((!$version1) || (!$version2)) {
            return 'Version numbers(v1,v2) are required!';
        }
        $version1_chars = str_split($version1);
        $version2_chars = str_split($version2);
        if (count(array_diff($version1_chars, $this->allowed_version_characters))) {
            return 'Version 1 is invalid!';
        }
        if (count(array_diff($version2_chars, $this->allowed_version_characters))) {
            return 'Version 2 is invalid!';
        }
        return null;
    }

    /**
     * Validates operator
     *
     * @param string $operator
     * @return mixed
     */
    public function validateOperator($operator)
    {
        if (!$operator) {
            return 'Operator is required!';
        }
        $op_chars = str_split($operator);
        if (count(array_diff($op_chars, $this->allowed_operators))) {
            return 'Invalid operator!';
        }
        return null;
    }

}
