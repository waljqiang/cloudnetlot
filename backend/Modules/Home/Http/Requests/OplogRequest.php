<?php
namespace Modules\Home\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OplogRequest extends FormRequest{
    private $rules = [
        "list" => [
        	"pageIndex" => "numeric",
        	"pageOffset" => "numeric",
        	"status" => "in:0,1,2,3,4",
        	"date" => "nullable|date_format:Y-m-d",
        	"sortkey" => "in:created_at,dev_mac,status",
        	"sort" => "in:asc,desc"
        ],
        "readed" => [
            "comm_id" => "array",
            "comm_id.*" => "distinct"
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

    public function messages()
    {
        return [
        	"pageIndex.numeric" => config("exceptions.PAGEINDEX_NUMERIC"),
        	"pageOffset.numeric" => config("exceptions.PAGEOFFSET_NUMERIC"),
        	"status.in" => config("exceptions.STATUS_IN"),
        	"date.date_format" => config("exceptions.DATE_FORMAT"),
        	"sortkey.in" => config("exceptions.UNSUPPORT_SORTKEY"),
        	"sort.in" => config("exceptions.UNSUPPORT_SORT"),
            "comm_id.array" => config("exceptions.COMMID_ARRAY"),
            "comm_id.*.distinct" => config("exceptions.COMMID_DISTINCT"),
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