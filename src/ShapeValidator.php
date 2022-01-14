<?php


namespace SettleApi;


class ShapeValidator
{
    protected $shape;
    protected $data;

    /**
     * @param array $shape
     * @param array $data
     */
    public function __construct($shape = [], $data = [])
    {
        $this->shape = $shape;
        $this->data = $data;
    }

    public function validate()
    {
        $errors = [];

        $extra_keys = array_diff(array_keys($this->data), array_keys($this->shape));
        foreach($extra_keys as $extra_key) {
            $errors[$extra_key] = "Field '{$extra_key}' is not supported.";
        }

        foreach($this->shape as $key => $rules) {
            if (!is_array($rules)) $rules = explode('|', $rules);

            $error = $this->validateField($key, $rules);

            if ($error != '') {
                $errors[$key] = $error;
            }
        }

        if (!empty($errors)) {
            throw new SettleApiException("Shape validation error", 400, null, $errors);
        }
    }

    /**
     * @param string $key
     * @param array $rules
     * @return string
     */
    protected function validateField($key, $rules)
    {
        $error = '';
        $value = $this->data[$key] ?? null;

        foreach ($rules as $rule) {
            $error = $this->validateValue($key, $value, $rule);
            if ($error != '') break;
        }

        return $error;
    }

    protected function validateValue($key, $value, $rule)
    {
        $error = '';

        switch($rule) {
            case 'required':
                if (is_null($value)) {
                    $error = "Field '{$key}' is required.";
                }
                break;
            case 'string':
                if (!is_null($value) && !is_string($value)) {
                    $error = "Field '{$key}' is must be a string.";
                }
                break;
            case 'numeric':
                if (!is_null($value) && !is_numeric($value)) {
                    $error = "Field '{$key}' is must have numeric value.";
                }
                break;
        }

        return $error;
    }
}