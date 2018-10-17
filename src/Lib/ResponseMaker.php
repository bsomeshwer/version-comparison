<?php namespace Someshwer\VersionComparison\Lib;

/**
 * ResponseMaker class
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 06-10-2018
 */
class ResponseMaker
{

    /**
     * Makes boolean result
     *
     * @param mixed $result
     * @return boolean
     */
    public static function makeResult($result)
    {
        return ($result === 1) ? true : (($result === 0) ? false : $result);
    }

    /**
     * Creates validation response
     * It creates validation response when ever validation error is occurred.
     *
     * @param string $err_msg
     * @return Response
     */
    public static function makeValidationResponse($err_msg)
    {
        return response([
            'status' => 'ERROR',
            'error_type' => 'validation',
            'message' => 'Unable to evaluate the expression!',
            'error_message' => $err_msg
        ], 422);
    }

    /**
     * It creates success responseW.
     *
     * @param mixed $result
     * @return Response
     */
    public static function makeSuccessResponse($result)
    {
        $result = self::makeResult($result);
        return response([
            'status' => 'SUCCESS',
            'message' => 'Expression successfully evaluated!',
            'result' => $result
        ], 200);
    }

}
