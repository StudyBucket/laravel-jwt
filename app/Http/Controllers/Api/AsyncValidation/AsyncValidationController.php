<?php

namespace App\Http\Controllers\Api\AsyncValidation;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Config;

class AsyncValidationController extends Controller
{
    /**
      * Main validator function. Validates a single property.
      *
      * @var Request    $request
      * @var Array      $propertyRulesArray
      *
      * @return response
      */
    public function validator(Request $request, Array $propertyRulesArray)
    {
        // Finally validate the request
        $request->validate($propertyRulesArray);
    }

    /**
      * Main validator function. Validates just a single property.
      *
      * @var String     $requestType - store | update
      * @var String     $property
      * @var String     $model
      * @var Request    $request
      * @var Request    $id (optional) - id of the model; if specified!
      *
      * @return response
      */
    public function singleValidator($requestType, $property, $model, Request $request, $id = null)
    {
        if(!$model_instance = $this->getModel($model, $id)){
            $response = ['message' => '400 - Model '."$model".' not found!'];
            return response($response, 400);
        }

        // Check if the type of request (store, update) exists on modelRules of the model
        if(!$this->validateRequestType($requestType, $model_instance)){
            $response = ['message' => '400 - Request '.$requestType.' not found on '.$model.'!'];
            return response($response, 400);
        }

        // Check if the if protery exists on modelRules of the model
        try {
            $property_rules = $model_instance->modelRules($requestType)[$property];
        } catch (\Exception $e) {
            $response = ['message' => '400 - Rule for `'.$requestType.'` property `'.$property.'` not found on `'.$model.'` model!'];
            return response($response, 400);
        }
        $propertyRulesArray = [
            $property => $property_rules
        ];
        // Actually validate the property
        $this->validator($request, $propertyRulesArray);
    }

    /**
      * Multiple validator function.
      *
      * @var Request    $request
      * @var String     $model
      * @var Request    $request
      * @var String     $id (optional) - id of the model; if specified!
      *
      * @return response
      */
    public function multipleValidator($requestType, $model, Request $request, $id = null)
    {
        if(!$model_instance = $this->getModel($model, $id)){
            $response = ['message' => '400 - Model '."$model".' not found!'];
            return response($response, 400);
        }

        // Check if the type of request (store, update) exists on modelRules of the model
        if(!$this->validateRequestType($requestType, $model_instance)){
            $response = ['message' => '400 - Request '.$requestType.' not found on '.$model.'!'];
            return response($response, 400);
        }

        if($properties_rules = $this->getPropertiesFromRequest($model_instance, $requestType, $request)){

        } else {
            $response = ['message' => '400 - Rule for a property not found on `'.$model.'` model!'];
            return response($response, 400);
        }
        // Actually validate the property
        $request->validate($properties_rules);
    }

    /**
      * Returns properties (and desired rules!) from $request as $property_rules[]
      *
      * @var Model      $model_instance
      * @var String     $requestType
      * @var Request    $request
      *
      * @return response
      */
    public function getPropertiesFromRequest($model_instance, $requestType, Request $request)
    {
        $property_rules = [];
        foreach ($request->all() as $key => $value) {
            try {
                $new_rule = $model_instance->modelRules($requestType)[$key];
                $property_rules[$key] = $new_rule;
            } catch (\Exception $e) {
                //exit($e);
                return NULL;
            }
        }
        return $property_rules;
    }

    /**
      * Maps request to model.
      *
      * @var String     $model
      * @var Number     $model_id
      *
      * @return response
      */
    private function getModel($model, $model_id = null)
    {
        $modelMap = Config::get('modelmap'); // get the model specified as string out of the global modelmap!

        if(!isset($modelMap[$model])){
            // $response = ['message' => '400 - Model '."$model".' not found!'];
            // return response($response, 400);
            return NULL;
        }

        if($modelMap[$model]::find($model_id)) { // is a model_id specified on the request which belongs to an existing model?
            $model_instance = $modelMap[$model]::findOrFail($model_id);
        } else { // no model_id an therefore noch existing model given -> create a blank one!
            $model_instance = new $modelMap[$model];
        }
        return $model_instance; // send back the generated model instance
    }

    /**
      * Checks if set of rules for a type of request (store | update) exists.
      *
      * @var String     $request
      * @var Model      $model_instance
      *
      * @return response
      */
    private function validateRequestType($requestType, $model_instance)
    {
        if($model_instance->modelRules($requestType)) return true;
        return false;
    }
}
