<?php

/**
 * 框架自带的 validator 必须结合 module 使用，实际应用并不灵活，自定义一个 validator 供工程使用
 */
namespace Common\Service;

/**
 * $rules = array(
 *     $value1, $mode, array(
 *         array($rule1, $param1, $errMsg1),
 *         array($rule2, $param2, $errMsg2),
 *     ),
 * );
 *
 * $mode: 0 任何情况都校验；1 不为空的时候校验；2 有定义的时候校验
 *
 * $rule:
 *      array('accepted', null, "err msg"),         字段值为 yes, on, 或是 1 时，验证才会通过
 *      array('active_url', null, "err msg"),       字段值通过 PHP 函数 checkdnsrr 来验证是否为一个有效的网址
 *      array('alpha', null, "err msg"),            字段仅全数为字母字串时通过验证
 *      array('alpha_dash', null, "err msg"),       字段值仅允许字母、数字、破折号（-）点(.)以及底线（_）
 *      array('alpha_num', null, "err msg"),        字段值仅允许字母、数字
 *      array('array', null, "err msg"),            字段值仅允许为数组
 *      array('between', "min,max", "err msg"),     字段数值需介于指定的 min 和 max 值之间
 *      array('boolean', null, "err msg"),          需要验证的字段必须可以转换为 boolean 类型的值。可接受的输入是true、false、1、0、"1" 和 "0"。
 *      array('confirmed', "field", "err msg"),     字段值需与对应的 param field 相同
 *      array('date', null, "err msg"),             字段值通过 PHP strtotime 函数验证是否为一个合法的日期。
 *      array('date_after', "date", "err msg"),     验证字段是否是在指定日期之后。这个日期将会使用 PHP strtotime 函数验证。
 *      array('different', "field", "err msg"),     字段值需与指定的字段 field 值不同
 *      array('email', null, "err msg"),            字段值需符合 email 格式
 *      array('exclude', null, "err msg"),          不允许字段值赋值
 *      array('in', "foo,bar,...", "err msg"),      字段值需符合事先给予的清单的其中一个值
 *      array('integer', null, "err msg"),          字段值需为一个整数值
 *      array('ip', null, "err msg"),               字段值需符合 IP 位址格式
 *      array('len_between', "min,max", "err msg"), 字段值位数需介于指定的 min 和 max 值之间
 *      array('len_max', "val", "err msg"),         字段值位数不能大于指定值 val
 *      array('len_min', "val", "err msg"),         字段值位数不能小于指定值 val
 *      array('max', "val", "err msg"),             字段数值不能大于指定值 val
 *      array('min', "val", "err msg"),             字段数值不能小于指定值 val
 *      array('not_in', "foo,bar,...", "err msg"),  字段值不得为给定清单中其一
 *      array('numeric', null, "err msg"),          字段值需为数字
 *      array('size', "value", "err msg"),          字段值的尺寸需符合给定 value 值。对于字串来说，value 为需符合的字串长度。对于数字来说，value 为需符合的整数值。对于文件来说，value 为需符合的文件大小（单位 kb)。
 *      array('require', null, "err msg"),          字段值为必填
 *      array('require_if', "field,value", "err msg"),          字段值在 field 字段值为 value 时为必填。
 *      array('required_without', "foo,bar,...", "err msg"),    字段值仅在任一指定字段没有值情况下为必填。
 *      array('url', null, "err msg"),              字段值需符合 URL 的格式
 *      array('unequal', 'value', 'err msg'),       字段值需不等于给定值
 *
 * 通过校验返回 true; 否则返回 errmsg
 */
class ValidatorService {

    public function exce($values, $rules) {

        // $values $rules 必须为数组
        if (!is_array($values) || !is_array($rules)) {
            return "Validator failed, values and rules must be an array";
        }

        foreach ($rules as $item) {
            list ($attr, $mode, $ruleArr) = $item;

            // rule 格式
            if (!isset($attr)) {
                return "Validator failed, ".__LINE__;
            }
            if (!isset($mode) || !in_array($mode, array(0, 1, 2)) || !is_array($ruleArr)) {
                return "Validator failed, ".__LINE__;
            }

            // mode 0 任何情况都进行校验，value 不存在则为 null
            if ($mode == 0) {
                $value = isset($values[$attr]) ? $values[$attr] : null;
            }
            // mode 1 value 不为空的时候才进行校验
            else if ($mode == 1) {
                if (empty($values[$attr])) {
                    continue;
                }
                $value = $values[$attr];
            }
            // mode 2 value 有定义的时候才进行校验
            else if ($mode == 2) {
                if (is_null($values[$attr]) || $values[$attr] === "") {
                    continue;
                }
                $value = $values[$attr];
            }

            // 便利规则进行校验
            foreach ($ruleArr as $rule) {
                list ($func, $params, $errMsg) = $rule;
                switch ($func) {
                    case "accepted":
                        if (false == $this->_validatorAccepted($value)) {
                            return $errMsg;
                        }
                        break;

                    case "active_url":
                        if (false == $this->_validatorActiveUrl($value)) {
                            return $errMsg;
                        }
                        break;

                    case "alpha":
                        if (false == $this->_validatorAlpha($value)) {
                            return $errMsg;
                        }
                        break;

                    case "alpha_dash":
                        if (false == $this->_validatorAlphaDash($value)) {
                            return $errMsg;
                        }
                        break;

                    case "alpha_num":
                        if (false == $this->_validatorAlphaNum($value)) {
                            return $errMsg;
                        }
                        break;

                    case "array":
                        if (false == $this->_validatorArray($value)) {
                            return $errMsg;
                        }
                        break;

                    case "between":
                        list ($min, $max) = explode(",", $params);
                        if (is_null($min) || is_null($max)) {
                            return "Validator failed, ".__LINE__;
                        }
                        if (false == $this->_validatorBetween($value, $min, $max)) {
                            return $errMsg;
                        }
                        break;

                    case "boolean":
                        if (false == $this->_validatorBoolean($value)) {
                            return $errMsg;
                        }
                        break;

                    case "confirmed":
                        $fieldVal = $values[$params];
                        if (false == $this->_validatorConfirmed($value, $fieldVal)) {
                            return $errMsg;
                        }
                        break;

                    case "date":
                        if (false == $this->_validatorDate($value)) {
                            return $errMsg;
                        }
                        break;

                    case "date_after":
                        if (false == $this->_validatorDateAfter($value, $params)) {
                            return $errMsg;
                        }
                        break;

                    case "different":
                        $fieldVal = $values[$params];
                        if (false == $this->_validatorDifferent($value, $fieldVal)) {
                            return $errMsg;
                        }
                        break;

                    case "email":
                        if (false == $this->_validatorEmail($value)) {
                            return $errMsg;
                        }
                        break;

                    case "exclude":
                        if (false == $this->_validatorExclude($value)) {
                            return $errMsg;
                        }
                        break;

                    case "in":
                        $parameters = explode(",", $params);
                        if (false == $this->_validatorIn($value, $parameters)) {
                            return $errMsg;
                        }
                        break;

                    case "integer":
                        if (false == $this->_validatorInteger($value)) {
                            return $errMsg;
                        }
                        break;

                    case "ip":
                        if (false == $this->_validatorIp($value)) {
                            return $errMsg;
                        }
                        break;

                    case "len_between":
                        list ($min, $max) = explode(",", $params);
                        if (is_null($min) || is_null($max)) {
                            return "Validator failed, ".__LINE__;
                        }
                        if (false == $this->_validatorLenBetween($value, $min, $max)) {
                            return $errMsg;
                        }
                        break;

                    case "len_max":
                        if (false == $this->_validatorLenMax($value, $params)) {
                            return $errMsg;
                        }
                        break;

                    case "len_min":
                        if (false == $this->_validatorLenMin($value, $params)) {
                            return $errMsg;
                        }
                        break;

                    case "max":
                        if (false == $this->_validatorMax($value, $params)) {
                            return $errMsg;
                        }
                        break;

                    case "min":
                        if (false == $this->_validatorMin($value, $params)) {
                            return $errMsg;
                        }
                        break;

                    case "not_in":
                        $parameters = explode(",", $params);
                        if (false == $this->_validatorNotIn($value, $parameters)) {
                            return $errMsg;
                        }
                        break;

                    case "numeric":
                        if (false == $this->_validatorNumeric($value)) {
                            return $errMsg;
                        }
                        break;

                    case "size":
                        if (false == $this->_validatorSize($value, $params)) {
                            return $errMsg;
                        }
                        break;

                    case "require":
                        if (false == $this->_validatorRequire($value)) {
                            return $errMsg;
                        }
                        break;

                    case "require_if":
                        $parameters = explode(",", $params);
                        if (false == $this->_validatorRequireIf($value, $parameters, $values)) {
                            return $errMsg;
                        }
                        break;

                    case "require_without":
                        $parameters = explode(",", $params);
                        if (false == $this->_validatorRequireWithout($value, $parameters, $values)) {
                            return $errMsg;
                        }
                        break;

                    case "url":
                        if (false == $this->_validatorUrl($value)) {
                            return $errMsg;
                        }
                        break;

                    case "unequal":
                        if (false == $this->_validatorUnequal($value, $params)) {
                            return $errMsg;
                        }
                        break;

                    default:
                        return "Validator failed, ".__LINE__;
                }
            }
        }

        return true;
    }

    private function _anyFailingRequired($parameters, $values) {

        foreach ($parameters as $key) {
            if (!$this->_validatorRequire($values[$key])) {
                return true;
            }
        }
        return false;
    }

    private function _validatorAccepted($value) {

        $acceptable = array('yes', 'on', '1', 1, true, 'true');

        return in_array($value, $acceptable, true);
    }

    private function _validatorActiveUrl($value) {

        $url = str_replace(array('http://', 'https://', 'ftp://'), '', strtolower($value));

        return checkdnsrr($url);
    }

    private function _validatorAlpha($value) {

        return preg_match('/^[\pL\pM]+$/u', $value);
    }

    private function _validatorAlphaDash($value) {

        return preg_match('/^[\pL\pM\pN\._-]+$/u', $value);
    }

    private function _validatorAlphaNum($value) {

        return preg_match('/^[\pL\pM\pN]+$/u', $value);
    }

    private function _validatorArray($value) {

        return is_array($value);
    }

    private function _validatorBetween($value, $min, $max) {

        $val = intval($value);
        return $val >= $min && $val <= $max;
    }

    private function _validatorBoolean($value) {

        $acceptable = array(true, false, 0, 1, '0', '1', 'on');

        return in_array($value, $acceptable, true);
    }

    private function _validatorConfirmed($value, $fieldVal) {

        return $value === $fieldVal;
    }

    private function _validatorDate($value) {

        if ($value instanceof DateTime) {
            return true;
        }
        if (strtotime($value) === false) {
            return false;
        }
        $date = date_parse($value);
        return checkdate($date['month'], $date['day'], $date['year']);
    }

    private function _validatorDateAfter($value, $parameters) {

        $afterDate = strtotime($parameters);
        if (false === $afterDate) {
            return false;
        }
        return strtotime($value) >= $afterDate ? true : false;
    }

    private function _validatorDifferent($value, $fieldVal) {

        return $value !== $fieldVal;
    }

    private function _validatorEmail($value) {

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function _validatorExclude($value) {

        return is_null($value) ? true : false;
    }

    private function _validatorIn($value, $parameters) {

        return in_array((string)$value, $parameters);
    }

    private function _validatorInteger($value) {

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function _validatorIp($value) {

        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    private function _validatorLenBetween($value, $min, $max) {

        $len = strlen(strval($value));
        return $len >= $min && $len <= $max;
    }

    private function _validatorLenMax($value, $max) {

        return strlen(strval($value)) <= $max;
    }

    private function _validatorLenMin($value, $min) {

        return strlen(strval($value)) >= $min;
    }

    private function _validatorMax($value, $max) {
        $value = trim($value);
        if (!is_numeric($value)) {
            return false;
        }
        return $value <= trim($max);
    }

    private function _validatorMin($value, $min) {

        if (!is_numeric(trim($value))) {
            return false;
        }
        return $value >= $min;
    }

    private function _validatorNotIn($value, $parameters) {

        return !in_array((string)$value, $parameters);
    }

    private function _validatorNumeric($value) {

        return is_numeric(trim($value));
    }

    private function _validatorSize($value, $size) {

        if (is_numeric($value)) {
            return $value == $size;
        } else if (is_array($value)) {
            return count($value) == $size;
        } else if ($value instanceof File) {
            return $value->getSize() / 1024 == $size;
        } else {
            return strlen($value) == $size;
        }
    }

    private function _validatorRequire($value) {

        if (is_null($value)) {
            return false;
        } else if (is_string($value) && trim($value) === '') {
            return false;
        } else if (is_array($value) && count($value) < 1) {
            return false;
        } else if ($value instanceof File) {
            return (string)$value->getPath() != '';
        }
        return true;
    }

    private function _validatorRequireIf($value, $parameters, $values) {

        if ($values[$parameters[0]] == $parameters[1]) {
            return $this->_validatorRequire($value);
        }

        return true;
    }

    private function _validatorRequireWithout($value, $parameters, $values) {

        if ($this->_anyFailingRequired($parameters, $values)) {
            return $this->_validatorRequire($value);
        }

        return true;
    }

    private function _validatorUrl($value) {

        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function _validatorUnequal($value, $rule) {
        if ($value == $rule) {
            return false;
        }
        return true;
    }
}
