<?php
namespace Modules\Home\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeRequest extends FormRequest{
    private $rules = [
        "upload" => [
            "file" => "file",
            "type" => "required"
        ],
        "localpackages" => [
            "macs" => "required|array",
            "macs.*" => "required|distinct"
        ],
        "deletelocalpackages" => [
            "fids" => "required|array",
            "fids.*" => "required|distinct" 
        ],
        "upgrades" => [
            "macs" => "required|array",
            "macs.*" => "required|distinct",
            "fid" => "required",
            "time" => "nullable|date"
        ],
        "getorders" => [
            "status" => "in:0,1,2,3,4",
            "start" => "required_with:end|date|before:end",
            "end" => "required_with:start|date|after:start"
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
        	"file.file" => config("exceptions.FILE_FILE"),
            "type.required" => config("exceptions.DEV_TYPE_REQUIRED"),
            "fids.required" => config("exceptions.FID_REQURIED"),
            "fids.array" => config("exceptions.FIDS_ARRAY"),
            "fids.*.required" => config("exceptions.FID_REQURIED"),
            "fids.*.distinct" => config("exceptions.FID_DISTINCT"),
            "macs.required" => config("exceptions.MAC_REQUIRED"),
            "macs.array" => config("exceptions.MACS_ARRAY"),
            "macs.*.required" => config("exceptions.MAC_REQUIRED"),
            "macs.*.distinct" => config("exceptions.MAC_DISTINCT"),
            "fid.required" => config("exceptions.FID_REQURIED"),
            "time.date" => config("exceptions.DATE_FORMAT"),
            "start.required_with" => config("exceptions.START_REQUIRED"),
            "start.date" => config("exceptions.DATE_NO"),
            "start.before" => config("exceptions.DATE_START_LET_END"),
            "end.required_with" => config("exceptions.END_REQUIRED"),
            "end.date" => config("exceptions.DATE_NO"),
            "end.after" => config("exceptions.DATE_START_LET_END"),
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