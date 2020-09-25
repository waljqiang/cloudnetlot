<?php
namespace Modules\Develop\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest{
    private $rules = [
        "develop" => [
            "name" => "required|max:100",
            "idcard" => "required|alpha_num|max:100",
            "enterprise" => "required|max:100",
            "enterprise_des" => "required|max:1000",
            "enterprisecode" => "required|max:100",
            "lang" => "in:zh-cn,en-us",
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
                $regex = "*" . $action;
                if($this->is($regex)){
                    $rules = $rule;
                    break;
                }
            }
        }
        return $rules;
    }

    //条件验证
/*    protected function getValidatorInstance(){
        $validator = parent::getValidatorInstance();
        return $validator->sometimes("area", "exists:area,code", function ($input) {
            return is_numeric($input->area);
        });
    }*/

    public function messages()
    {
        return [
            "name.required" => config("exceptions.NAME_REQUIRED"),
            "name.max" => config("exceptions.NAME_MAX"),
            "idcard.required" => config("exceptions.IDCARD_REQUIRED"),
            "idcard.alpha_num" => config("exceptions.IDCARD_ALPHA_NUM"),
            "idcard.max" => config("exceptions.IDCARD_MAX"),
            "enterprise.required" => config("exceptions.ENTERPRISE_REQUIRED"),
            "enterprise.max" => config("exceptions.ENTERPRISE_MAX"),
            "enterprise_des.required" => config("exceptions.ENTERPRISE_DES_REQUIRED"),
            "enterprise_des_max" => config("exceptions.ENTERPRISE_DES_MAX"),
            "enterprisecode.required" => config("exceptions.ENTERPRISE_CODE_REQUIRED"),
            "enterprisecode.max" => config("exceptions.ENTERPRISE_MAX"),
            "lang.in" => config("exceptions.LANG_IN"),
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