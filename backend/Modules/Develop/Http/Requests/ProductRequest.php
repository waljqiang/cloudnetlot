<?php
namespace Modules\Develop\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest{
    private $rules = [
        "register" => [
            "productname" => "required|regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,100}$/iuD",
            "type" => "required|producttype",
            "size" => "required|regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,20}$/iuD",
            "productdes" => "required|max:1000",
        ],
        "list" => [
            "pageIndex" => "numeric",
            "pageOffset" => "numeric",
            "sortKey" => "in:name,type,size,aud_status,created_at",
            "sort" => "in:asc,desc"
        ],
        "info" => [
            "prtid" => "required",
        ],
        "save" => [
            "prtid" => "required",
            "productname" => "required_without:productdes|regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,100}$/iuD",
            "productdes" => "required_without:productname|max:1000"
        ],
        "delete" => [
            "prtid" => "required"
        ],
        "publish" => [
            "prtid" => "required"
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
            "productname.required" => config("PRT_NAME_REQUIRED"),
            "productname.regex" => config("PRT_NAME_REGEX"),
            "product.type.required" => config("PRT_TYPE_REQUIRED"),
            "product.type.producttype" => config("PRT_TYPE_NO"),
            "size.required" => config("PRT_SIZE_REQUIRED"),
            "size.regex" => config("PRT_SIZE_REGEX"),
            "productdes.required" => config("PRT_DES_REQUIRED"),
            "productdes.max" => config("PRT_DES_MAX"),
            "pageIndex.numeric" => config("exceptions.PAGEINDEX_NUMERIC"),
            "pageOffset.numeric" => config("exceptions.PAGEOFFSET_NUMERIC"),
            "sortkey.in" => config("exceptions.UNSUPPORT_SORTKEY"),
            "sort.in" => config("exceptions.UNSUPPORT_SORT"),
            "prtid.required" => config("exceptions.PRT_ID_REQUIRED"),
            "productname.required_without" => config("exceptions.PARAMS_INVALID"),
            "productdes.required_without" => config("exceptions.PARAMS_INVALID"),
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