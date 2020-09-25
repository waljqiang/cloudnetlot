<?php
namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DevelopRequest extends FormRequest{
    private $rules = [
    	"list" => [
    		"pageIndex" => "numeric",
    		"pageOffset" => "numeric",
    		"sortKey" => "in:username,enterprise,created_at",
    		"sort" => "in:asc,desc"
    	],
        "info" => [
            "uid" => "required"
        ],
        "approve" => [
            "uid" => "required",
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
        	"pageIndex.numeric" => config("exceptions.PAGEINDEX_NUMERIC"),
            "pageOffset.numeric" => config("exceptions.PAGEOFFSET_NUMERIC"),
            "sortkey.in" => config("exceptions.UNSUPPORT_SORTKEY"),
            "sort.in" => config("exceptions.UNSUPPORT_SORT"),
            "uid.required" => config("exceptions.USER_UID_REQURIED"),
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