<?php
namespace App;

use \Rakit\Validation\Validator;

class Validation
{
    public static function validate($data, $rules)
    {

        $validator = new Validator;

        $validation = $validator->make($data, $rules);
        $validation->validate();

        if ($validation->fails()) {
            $errors = ($validation->errors())->firstOfAll();

            Response::JSON($errors, 400);
        }
    }
}
