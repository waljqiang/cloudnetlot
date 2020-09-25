<?php
namespace Modules\Home\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest{
    private $rules = [
        "bind" => [
            "prtid" => "required",
        	"mac" => "required",
        	"username" => "required",
        	"password" => "required",
        ],
        "list" => [
            "gid" => "required",
            "status" => "in:0,1",
            "sortkey" => "in:dev_ip,name,type,join_time",
            "sort" => "in:asc,desc",
            "pageIndex" => "numeric",
            "pageOffset" => "numeric"
        ],
        "infos" => [
            "mac" => "required",
            "type" => "required|array",
            "type.*" => "in:2,3,4,5,6,7"
        ],
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

    public function messages()
    {
        return [
            "prtid.required" => config("exceptions.PRT_ID_REQUIRED"),
        	"mac.required" => config("exceptions.MAC_REQUIRED"),
        	"username.required" => config("exceptions.DEV_USERNAME_REQUIRED"),
        	"password.required" => config("exceptions.DEV_PASSWORD_REQUIRED"),
            "gid.required" => config("exceptions.GID_REQUIRED"),
            "status.in" => config("exceptions.NO_STATUS_IN"),
            "sortkey.in" => config("exceptions.UNSUPPORT_SORTKEY"),
            "sort.in" => config("exceptions.UNSUPPORT_SORT"),
            "pageIndex.numeric" => config("exceptions.PAGEINDEX_NUMERIC"),
            "pageOffset.numeric" => config("exceptions.PAGEOFFSET_NUMERIC"),
            "type.required" => config("exceptions.DEV_TYPEINFO_REQUIRED"),
            "type.array" => config("exceptions.DEV_TYPEINFO_ARRAY"),
            "type.*.in" => config("exceptions.DEV_TYPEINFO_IN"),
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