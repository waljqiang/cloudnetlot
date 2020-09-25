<?php
namespace Modules\Home\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkgroupRequest extends FormRequest{
    private $rules = [
        "upload/config" => [
            "file" => "file|mimetypes:text/plain"
        ],
    	"add" => [
    		"name" => "required|regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_@]{1,10}$/iuD",
    		"description" => "nullable|max:100",
    		"auto" => "required_with_all:config_id|in:0,1"
    	],
        "all" => [
            "tree" => "in:0,1"
        ],
        "info" => [
            "gid" => "required"
        ],
        "save" => [
            "gid" => "required",
            "name" => "regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_@]{1,10}$/iuD",
            "description" => "nullable|max:100",
            "auto" => "required_with_all:config_id|in:0,1"
        ],
        "delete" => [
            "gid" => "required"
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
            "file.file" => config("exceptions.FILE_FILE"),
            "file.mimetypes" => config("exceptions.FILE_FILEEXT"),
            "name.required" => config("exceptions.GROUP_NAME_REQUIRED"),
            "name.regex" => config("exceptions.GROUP_NAME_INVALID"),
            "description.max" => config("exceptions.GROUP_DESC_MAX"),
            "auto.required_with_all" => config("exceptions.AUTO_WITH_CONFIG"),
            "auto.in" => config("exceptions.AUTO_IN"),
            "tree.in" => config("exceptions.TREE_IN"),
            "gid.required" => config("exceptions.GID_REQUIRED"),
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