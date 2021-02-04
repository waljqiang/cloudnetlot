<?php
namespace Modules\Home\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest{
    private $rules = [
    	"register" => [
    		"username" => "required|alpha_dash|between:3,20|unique:users",
    		"nickname" => "regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{1,20}$/iuD",
    		"password" => "required|alpha_num|between:6,20|confirmed",
            "phonecode" => "required|exists:country_code,phonecode",
    		"phone" => "required|phone:phonecode",
    		"email" => "required|email",
    		"area" => "nullable|exists:area,code",
    		"address" => "required|max:100"
    	],
        "password/sendmail" => [
            "username" => "required|exists:users,username",
            "email" => "required|email",
            "lang" => "in:zh-cn,en-us"
        ],
        "password/checkmail" => [
            "content" => "required"
        ],
        "password/reset" => [
            "content" => "required",
            "password" => "required|alpha_num|between:6,20|confirmed"
        ],
        "save" => [
            "nickname" => "required_without:phonecode,phone,email|regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{1,20}$/iuD",
            "phonecode" => "required_without:nickname,phone,email|required_with:phone|exists:country_code,phonecode",
            "phone" => "required_without:nickname,phonecode,email|required_with:phonecode|phone:phonecode",
            "email" => "required_without:nickname,phonecode,phone|email",
        ],
        "password/save" => [
            "old_password" => "required",
            "new_password" => "required|alpha_num|between:6,20|different:old_password|confirmed"
        ],
        "child/list" => [
            "pageIndex" => "numeric",
            "pageOffset" => "numeric",
            "sortkey" => "in:username,nickname,role,status,created_at",
            "sort" => "in:asc,desc",
            "status" => "in:0,1,2",
            "role" => "nullable|in:1,2"
        ],
        "child/info" => [
            "uid" => "required"
        ],
        "child/add" => [
            "username" => "required|alpha_dash|between:3,20|unique:users",
            "nickname" => "regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{1,20}$/iuD",
            "password" => "required|alpha_num|between:6,20",
            "phonecode" => "required|exists:country_code,phonecode",
            "phone" => "required|phone:phonecode",
            "email" => "required|email",
            "role" => "required|in:1,2",
            "enable" => "required|in:0,1",
            "gids" => "required|array",
            "gids.*" => "distinct"
        ],
        "child/save" => [
            "uid" => "required",
            "nickname" => "regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{1,20}$/iuD",
            "phonecode" => "exists:country_code,phonecode",
            "phone" => "phone:phonecode",
            "email" => "email",
            "role" => "in:1,2",
            "enable" => "in:0,1",
            "gids" => "array",
            "gids.*" => "distinct"
        ],
        "child/resetspassword" => [
            "uids" => "required|array",
            "password" => "alpha_num|between:6,20"
        ],
        "child/deletes" => [
            "uids" => "required|array"
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
        	"username.required" => config("exceptions.USER_USERNAME_REQUIRED"),
        	"username.alpha_dash" => config("exceptions.USER_USERNAME_ALPHA_DASH"),
        	"username.between" => config("exceptions.USER_USERNAME_BETWEEN"),
        	"username.unique" => config("exceptions.USER_EXISTS"),
            "username.exists" => config("exceptions.USER_NO_EXISTS"),
            "nickname.required_without" => config("exceptions.PARAMS_INVALID"),
        	"nickname.regex" => config("exceptions.USER_NICKNAME_REGEX"),
        	"password.required" => config("exceptions.USER_PASSWORD_REQUIRED"),
        	"password.alpha_num" => config("exceptions.USER_PASSWORD_ALPHA_DASH"),
        	"password.between" => config("exceptions.USER_PASSWORD_BETWEEN"),
            "password.confirmed" => config("exceptions.USER_PASSWORD_CONFIRMED"),
            "phonecode.required" => config("exceptions.COUNTRY_PHONECODE_REQUIRED"),
            "phonecode.required_without" => config("exceptions.PARAMS_INVALID"),
            "phonecode.required_with" => config("exceptions.PHONE_PHONECODE_REQUIRED"),
            "phonecode.exists" => config("exceptions.COUNTRY_PHONECODE_EXISTS"),
            "phone.required" => config("exceptions.PHONE_REQUIRED"),
            "phone.required_without" => config("exceptions.PARAMS_INVALID"),
            "phone.required_with" => config("exceptions.PHONE_PHONECODE_REQUIRED"),
        	"phone.phone" => config("exceptions.PHONE"),
            "email.required" => config("exceptions.EMAIL_REQUIRED"),
            "email.required_without" => config("exceptions.PARAMS_INVALID"),
        	"email.email" => config("exceptions.EMAIL"),
        	"area.exists" => config("exceptions.AREA_EXISTS"),
            "address.required" => config("exceptions.ADDRESS_REQUIRED"),
        	"address.max" => config("exceptions.ADDRESS_MAX"),
            "content.required" => config("exceptions.PARAMS_INVALID"),
            "old_password.required" => config("exceptions.USER_PASSWORD_REQUIRED"),
            "new_password.required" => config("exceptions.USER_PASSWORD_REQUIRED"),
            "new_password.alpha_num" => config("exceptions.USER_PASSWORD_ALPHA_DASH"),
            "new_password.between" => config("exceptions.USER_PASSWORD_BETWEEN"),
            "new_password.different" => config("exceptions.USER_PASSWORD_DIFFERENT"),
            "new_password.confirmed" => config("exceptions.USER_PASSWORD_CONFIRMED"),
            "role.required" => config("exceptions.USER_ROLE_REQUIRED"),
            "role.in" => config("exceptions.USER_ROLE_IN"),
            "enable.required" => config("exceptions.ENABLE_REQUIRED"),
            "enable.in" => config("exceptions.ENABLE_IN"),
            "gids.required" => config("exceptions.GID_REQUIRED"),
            "gids.array" => config("exceptions.WORKGROUP_GIDS_ARRAY"),
            "gids.*.distinct" => config("exceptions.WORKGROUP_GIDS_DISTINCT"),
            "pageIndex.numeric" => config("exceptions.PAGEINDEX_NUMERIC"),
            "pageOffset.numeric" => config("exceptions.PAGEOFFSET_NUMERIC"),
            "sortkey.in" => config("exceptions.UNSUPPORT_SORTKEY"),
            "sort.in" => config("exceptions.UNSUPPORT_SORT"),
            "status.in" => config("exceptions.USER_STATUS_IN"),
            "role.in" => config("exceptions.USER_ROLE_IN"),
            "uid.required" => config("exceptions.USER_UID_REQURIED"),
            "uids.required" => config("exceptions.USER_UID_REQURIED"),
            "uids.array" => config("exceptions.USER_UID_ARRAY"),
            "lang.in" => config("exceptions.LANG_IN"),
            "name.required" => config("exceptions.DEV_NAME_REQUIRED"),
            "name.regex" => config("exceptions.DEV_NAME_REGEX"),
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