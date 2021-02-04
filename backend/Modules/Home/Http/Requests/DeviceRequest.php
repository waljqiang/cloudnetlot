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
            "status" => "nullable|in:0,1",
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
        "restart" => [
            "mac" => "required"
        ],
        "restarts" => [
            "macs" => "array",
            "macs.*" => "required|distinct"
        ],
        "statistics/clients/onlines" => [
            "start" => "required|date|before_or_equal:end",
            "end" => "required|date|after_or_equal:start"
        ],
        "timerestart" => [
            "mac" => "required",
            "enable" => "required|in:0,1",
            "time" => [
                'required_if:enable,1',
                'regex:/^w[1-7]m([0-9]|1[0-9]|2[0-3])$|^([0-9]|1[0-9]|2[0-3])$|^d[1-9]$|^d10$/D'
            ]
        ],
        "transgroup" => [
            "macs" => "array",
            "macs.*" => "required|distinct",
            "gid" => "required"
        ],
        "list/export" => [
            "gid" => "required",
            "lang" => "in:zh-cn,en-us"
        ],
        "wifi/info" => [
            "mac" => "required",
            "radio" => "required|numeric"
        ],
        "setwifi" => [
            "mac" => "required",
            "radio" => "required|numeric",
            "vaps" => "required",
            "vaps.*" => "required |numeric|distinct",
            "options" => "required",
            "options.*" => "required",
            "options.ssid" => "bit_between:1,32",
            "options.enable" => "in:0,1",
            "options.ssid_hide" => "in:0,1",
            "options.encode" => "in:1,2,3,4,5,6,7,8,9",
            "options.password" => "between:8,64",
            "options.timer_enable" => "in:0,1",
            "options.timer_start" => "required_if:options.timer_enable,1 | in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 | lte:options.timer_end",
            "options.timer_end" => "required_if:options.timer_enable,1 | in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 | lge:options.timer_start",
            "options.phymode" => "in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30",
            "options.user_isolate" => "in:0,1",
            "options.frag_threshold" => "numeric",
            "options.rts_threshold" => "numeric",
            "options.beacon_interval" => "numeric",
            "options.shortgi" => "in:0,1"
        ],
        "setwifis" => [
            "macs" => "required|array",
            "macs.*" => "required|distinct",
            "radio" => "required|numeric",
            "vaps" => "required",
            "vaps.*" => "required |numeric|distinct",
            "options" => "required",
            "options.*" => "required",
            "options.ssid" => "bit_between:1,32",
            "options.enable" => "in:0,1",
            "options.ssid_hide" => "in:0,1",
            "options.encode" => "in:1,2,3,4,5,6,7,8,9",
            "options.password" => "between:8,64",
            "options.timer_enable" => "in:0,1",
            "options.timer_start" => "required_if:options.timer_enable,1 | in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 | lte:options.timer_end",
            "options.timer_end" => "required_if:options.timer_enable,1 | in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 | lge:options.timer_start",
            "options.phymode" => "in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30",
            "options.user_isolate" => "in:0,1",
            "options.frag_threshold" => "numeric",
            "options.rts_threshold" => "numeric",
            "options.beacon_interval" => "numeric",
            "options.shortgi" => "in:0,1"
        ],
        "getwifioptions" => [
            "mac" => "required",
            "radio" => "required|numeric"
        ],
        "reports" => [
            "macs" => "required|array",
            "macs.*" => "required|distinct",
            "type" => "array",
            "type.*" => "in:2,3,4,5,6"
        ],
        "deletes" => [
            "macs" => "required|array",
            "macs.*" => "required|distinct"
        ],
        "setname" => [
            "mac" => "required",
            "name" => "required|regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,10}$/iuD"
        ],
        "types" => [
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
            "macs.array" => config("exceptions.MACS_ARRAY"),
            "macs.*.required" => config("exceptions.MAC_REQUIRED"),
            "macs.*.distinct" => config("exceptions.MAC_DISTINCT"),
            "start.required" => config("exceptions.START_REQUIRED"),
            "start.date" => config("exceptions.DATE_NO"),
            "start.before" => config("exceptions.DATE_START_LET_END"),
            "end.required" => config("exceptions.END_REQUIRED"),
            "end.date" => config("exceptions.DATE_NO"),
            "end.after" => config("exceptions.DATE_START_LET_END"),
            "lang.in" => config("exceptions.LANG_IN"),
            "radio.required" => config("exceptions.RADIO_REQUIRED"),
            "radio.numeric" => config("exceptions.RADIO_NUMERIC"),
            "vaps.required" => config("exceptions.VAP_REQUIRED"),
            "vaps.*.required" => config("exceptions.VAP_REQUIRED"),
            "vaps.*.numeric" => config("exceptions.VAP_NUMERIC"),
            "vaps.*.distinct" => config("exceptions.VAP_DISTINCT"),
            "options" => config("exceptions.VAP_OPTIONS_REQUIRED"),
            "options.*.required" => config("exceptions.VAP_OPTIONS_REQUIRED"),
            "options.ssid.bit_between" => config("exceptions.VAP_SSID_INVALID"),
            "options.enable.in" => config("exceptions.VAP_ENABLE"),
            "options.encode.in" => config("exceptions.VAP_ENCODE_IN"),
            "options.password.between" => config("exceptions.VAP_PASSWORD_INVALID"),
            "options.timer_enable.in" => config("exceptions.VAP_TIMER_ENABLE_IN"),
            "options.timer_start.required_if" => config("exceptions.VAP_TIMER_START_END_REQUIRED_IF"),
            "options.timer_start.in" => config("exceptions.VAP_TIMER_START_END_INVALID"),
            "options.timer_start.lte" => config("exceptions.VAP_TIMER_START_END_INVALID"),
            "options.timer_end.required_if" => config("exceptions.VAP_TIMER_START_END_REQUIRED_IF"),
            "options.timer_end.in" => config("exceptions.VAP_TIMER_START_END_INVALID"),
            "options.timer_end.lge" => config("exceptions.VAP_TIMER_START_END_INVALID"),
            "options.phymode.in" => config("exceptions.VAP_PHYMODE_INVALID"),
            "options.user_isolate.in" => config("exceptions.VAP_USER_ISOLATE_IN"),
            "options.frag_threshold.numeric" => config("exceptions.VAP_FRAG_THRESHOLD_INVALID"),
            "options.rts_threshold.numeric" => config("exceptions.VAP_RTS_THRESHOLD_INFVALID"),
            "options.beacon_interval.numeric" => config("exceptions.VAP_BEACON_INTERVAL_INVALID"),
            "options.shortgi.in" => config("exceptions.VAP_SHORTGI_IN"),
            "options.ssid_hide.in" => config("exceptions.VAP_HIDE_SSID_IN"),
            "enable.required" => config("exceptions.ENABLE_REQUIRED"),
            "enable.in" => config("exceptions.ENABLE_IN"),
            "time.rquired_if" => config("exceptions.TIME_REQUIRED_IF"),
            "time.regex" => config("exceptions.TIME_REGEX"),
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