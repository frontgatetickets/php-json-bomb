<?php
namespace frontgatetickets\Util;

class JSON
{

    /*
     * encode
     * Function signature matches that of official PHP json_encode.  Usage is identical.
     */
    public static function encode($obj, $options = 0)
    {
        return json_encode($obj, $options);
    }

    /*
     * decode
     * Function signature matches that of official PHP json_decode.  Usage is identical.
     */
    public static function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        /*
         * Call json_decode with the right signature for the PHP version
         */
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $decoded = json_decode($json, $assoc, $depth, $options);
        } elseif (version_compare(phpversion(), '5.3.0', '>=')) {
            $decoded = json_decode($json, $assoc, $depth);
        } else {
            $decoded = json_decode($json, $assoc);
        }

        // A big block of error-handling:

        $json_error_code = json_last_error();
        if ($json_error_code !== JSON_ERROR_NONE) {
            if (version_compare(phpversion(), '5.5.0', '>=')) {
                // 5.5's got this handy function.
                throw new JSONException("JSON Error: " . json_last_error_msg());
            } else {
                switch ($json_error_code) {
                    case JSON_ERROR_DEPTH:
                        throw new JSONException("JSON Error: $json_error_code (JSON_ERROR_DEPTH)");
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        throw new JSONException("JSON Error: $json_error_code (JSON_ERROR_STATE_MISMATCH)");
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        throw new JSONException("JSON Error: $json_error_code (JSON_ERROR_CTRL_CHAR)");
                        break;
                    case JSON_ERROR_SYNTAX:
                        throw new JSONException("JSON Error: $json_error_code (JSON_ERROR_SYNTAX)");
                        break;
                    case JSON_ERROR_UTF8:
                        throw new JSONException("JSON Error: $json_error_code (JSON_ERROR_UTF8)");
                        break;
                    default:
                        throw new JSONException("JSON Error: $json_error_code (Unrecognized error code)");
                        break;
                }
            }
        }

        return $decoded;
    }

    /*
     * Strictly speaking, JSON does not allow comments, but when using JSON as configuration,
     * it's nice to be able to comment things.
     * This strips single-line comments of the slash-slash and slash-star varieties
     * @param	String	$json
     */
    public static function stripComments($json)
    {
        return preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);
    }

    /**
     * backwards-compat
     */
    public static function strip_comments() {
        return call_user_func_array(array(__CLASS__, 'stripComments'), func_get_args());
    }

}

