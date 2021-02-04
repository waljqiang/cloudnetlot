<?php
namespace Modules\Home\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemRequest extends FormRequest{
    private $rules = [
    	"getclient" => [  
            "appid" => "required",
            "secret" => "required",
            "prtid" => "required",
            "mac" => "required",
            "type" => "required"
    	]
    ];
	/**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        $rules = [];
        if(!empty($this->rules)){
            foreach ($this->rules as $action => $rule) {
                $regex = "*/" . $action;
                if($this->is($regex)){
                    $rules = $rule;
                    break;
                }
            }
        }
        return $rules;
    }

    public function messages()
    {
        return [
            "appid.required" => config("exceptions.APPID_REQUIRED"),
            "secret.required" => config("exceptions.APP_SECRET_REQUIRED"),
            "prtid.required" => config("exceptions.PRT_ID_REQUIRED"),
            "mac.required" => config("exceptions.DEV_MAC_REQUIRED"),
            "type.required" => config("exceptions.DEV_TYPE_REQUIRED"),
        ];
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}