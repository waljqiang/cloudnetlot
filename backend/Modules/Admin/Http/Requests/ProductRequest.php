<?php
namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest{
    private $rules = [
        "product/list" => [
            "pageIndex" => "numeric",
            "pageOffset" => "numeric",
            "sortKey" => "in:name,type,size,aud_status,created_at",
            "sort" => "in:asc,desc"
        ],
        "product/info" => [
            "prtid" => "required"
        ],
        "product/approve" => [
            "prtid" => "required",
            "enable" => "in:0,1",
            "lang" => "in:zh-cn,en-us"
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
            "pageIndex.numeric" => config("exceptions.PAGEINDEX_NUMERIC"),
            "pageOffset.numeric" => config("exceptions.PAGEOFFSET_NUMERIC"),
            "sortkey.in" => config("exceptions.UNSUPPORT_SORTKEY"),
            "sort.in" => config("exceptions.UNSUPPORT_SORT"),
            "prtid.required" => config("exceptions.PRT_ID_REQUIRED"),
            "enable.in" => config("exceptions.ENABLE_IN"),
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