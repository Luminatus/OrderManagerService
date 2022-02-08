<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;


class Controller extends BaseController
{
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        return $this->validateWithValidator($request, $validator);
    }

    protected function validateWithValidator(Request $request, Validator $validator)
    {
        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        return $this->extractInputFromRules($request, $validator->getRules());
    }


    public function getChunkedResults($qb, callable $formatter = null)
    {
        $result = [];
        $qb->chunk(50, function ($rows) use (&$result, $formatter) {
            foreach ($rows as $row) {
                if ($formatter) {
                    $row = $formatter($row);
                }
                $result[] = $row;
            }
        });

        return $result;
    }
}
