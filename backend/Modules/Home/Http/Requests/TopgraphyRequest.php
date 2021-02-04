<?php
namespace Modules\Home\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopgraphyRequest extends FormRequest{
    private $rules = [
        "rebuild" => [
            "gid" => "required"
        ],
    	"info" => [
    		"gid" => "required"
    	],
        "save" => [
            "gid" => "required",
            "data.*.mac" => "required|distinct",
            "data.*.pid" => "required",
            "data.*.name" => "regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,10}$/iuD",
            "data.*.is_virture" => "required|in:0,1",
            "data.*.mode" => "required|in:0,1,2,3,4,100,101,102,103,104",//0 网关 1中继 2 WISP 3 AP 4 WDS,100：网络根节点,101:交换机,102:摄像头,103:移动设备,104：PC
            "data.*.status" => "required|in:0,1,2",
            "data.*.point_x" => "required|numeric",
            "data.*.point_y" => "required|numeric"
        ],
        "dynamics" => [
            "gid" => "required"
        ],
        "unedits" => [
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
            "gid.required" => config("exceptions.GID_REQUIRED"),
            "data.*.mac.required" => config("exceptions.MAC_REQUIRED"),
            "data.*.mac.distinct" => config("exceptions.MAC_DISTINCT"),
            "data.*.pid.required" => config("exceptions.DEV_PID_REQUIRED"),
            "data.*.name.regex" => config("exceptions.DEV_NAME_REGEX"),
            "data.*.is_virture.required" => config("exceptions.IS_VIRTURE_REQUIRED"),
            "data.*.is_virture.in" => config("exceptions.IS_VIRTURE_IN"),
            "data.*.mode.required" => config("exceptions.DEV_MODE_REQUIRED"),
            "data.*.mode.in" => config("exceptions.DEV_MODE_IN"),
            "data.*.status.required" => config("exceptions.DEV_STATUS_REQUIRED"),
            "data.*.status.in" => config("exceptions.NO_STATUS_IN"),
            "data.*.point_x.required" => config("exceptions.DEVICE_POINT_REQUIRED"),
            "data.*.point_x.numeric" => config("exceptions.DEVICE_POINT_NUMERIC"),
            "data.*.point_y.required" => config("exceptions.DEVICE_POINT_REQUIRED"),
            "data.*.point_y.numeric" => config("exceptions.DEVICE_POINT_NUMERIC")
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