<?php

/* Class Amazon
 * Takes configuration information
 * Makes API calls to MWS for Pay With Amazon
 * returns Response Object
 */


class Amazon
{

    private $config = array(
        'merchant_id' => false,
        'secret_key' => false,
        'access_key' => false,
        'region' => false,
        'currency_code' => false,
        'sandbox' => false,
        'application_name' => null,
        'client_id' => null,
    );


    public function __construct($config = null)
    {
        if (!is_null($config)) {

            if (is_array($config)) {
                $configArray = $config;
            } elseif (!is_array($config)) {
                $configArray = $this->checkIfFileExists($config);
            }
            if (is_array($configArray)) {
                $this->checkConfigKeys($configArray);
            } else {
                throw new \Exception('$config is of the incorrect type ' . gettype($configArray) . ' and should be of the type array');
            }
        } else {
            throw new \Exception('$config cannot be null.');
        }
    }


    function _signParameters(array $parameters)
    {
        $parameters["lwaClientId"] = $this->config['client_id'];
        $parameters["sellerId"] = $this->config['merchant_id'];
        $parameters["accessKey"] = $this->config['access_key'];;
        $stringToSign = null;
        $algorithm = "HmacSHA256";
        $stringToSign = $this->_calculateStringToSignV2($parameters);

        return $this->_sign($stringToSign, $algorithm);
    }

    function _calculateStringToSignV2(array $parameters)
    {
        $data = 'POST';
        $data .= "\n";
        $data .= "payments.amazon.com";
        $data .= "\n";
        $data .= "/";
        $data .= "\n";
        $data .= $this->_getParametersAsString($parameters);
        return $data;
    }

    function _getParametersAsString(array $parameters)
    {
        $queryParameters = array();
        foreach ($parameters as $key => $value) {
            $queryParameters[] = $key . '=' . $this->_urlencode($value);
        }
        return implode('&', $queryParameters);
    }

    function _urlencode($value)
    {
        return str_replace('%7E', '~', rawurlencode($value));
    }

    function _sign($data, $algorithm)
    {
        if ($algorithm === 'HmacSHA1') {
            $hash = 'sha1';
        } else if ($algorithm === 'HmacSHA256') {
            $hash = 'sha256';
        } else {
            throw new Exception("Non-supported signing method specified");
        }
        return base64_encode(hash_hmac($hash, $data, $this->config['secret_key'], true));
    }


    /* Trim the input Array key values */

    private function trimArray($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = trim($value);
        }
        return $array;
    }

    /* Checks if the keys of the input configuration matches the keys in the config array
     * if they match the values are taken else throws exception
     * strict case match is not performed
     */

    private function checkConfigKeys($config)
    {
        $config = array_change_key_case($config, CASE_LOWER);
        $config = $this->trimArray($config);
        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->config)) {
                $this->config[$key] = $value;
            } else {
                throw new \Exception('Key ' . $key . ' is either not part of the configuration or has incorrect Key name.
				check the config array key names to match your key names of your config array', 1);
            }
        }
    }


    /* checkIfFileExists -  check if the JSON file exists in the path provided */

    private function checkIfFileExists($config)
    {
        if (file_exists($config)) {
            $jsonString = file_get_contents($config);
            $configArray = json_decode($jsonString, true);

            $jsonError = json_last_error();

            if ($jsonError != 0) {
                $errorMsg = "Error with message - content is not in json format" . $this->getErrorMessageForJsonError($jsonError) . " " . $configArray;
                throw new \Exception($errorMsg);
            }
        } else {
            $errorMsg = '$config is not a Json File path or the Json File was not found in the path provided';
            throw new \Exception($errorMsg);
        }
        return $configArray;
    }



    /* Convert a json error code to a descriptive error message
     *
     * @param int $jsonError message code
     *
     * @return string error message
     */

    private function getErrorMessageForJsonError($jsonError)
    {
        switch ($jsonError) {
            case JSON_ERROR_DEPTH:
                return " - maximum stack depth exceeded.";
                break;
            case JSON_ERROR_STATE_MISMATCH:
                return " - invalid or malformed JSON.";
                break;
            case JSON_ERROR_CTRL_CHAR:
                return " - control character error.";
                break;
            case JSON_ERROR_SYNTAX:
                return " - syntax error.";
                break;
            default:
                return ".";
                break;
        }
    }
}
