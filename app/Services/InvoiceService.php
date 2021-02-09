<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

/**
 * Class SearchService
 * @package App\Services
 */
class InvoiceService
{
    /**
     * 入金情報のチェック
     *
     * @return void
     */
    public function check()
    {
        $mf_headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('MF_ACCESS_TOKEN')
        ];
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $client = new Client();
        $response = $client->request(
            'GET',
            "https://invoice.moneyforward.com/api/v1/billings/search?excise_type=boolean?range_key=created_at&from=".date("Y-m-01")."&to=".date("Y-m-t"),
            [
                'headers' => $mf_headers,
                // 'json' => $form_param1,
            ]
        );
        //請求書情報
        $billings = json_decode((string) $response->getBody(),1)["billings"];
        $conditions = [];

        foreach($billings as $billing){
            // $id[] = (int)filter_var($billing["billing_number"], FILTER_SANITIZE_NUMBER_INT);
            if( $billing["status"]["payment"] !== "未設定"){
                $conditions[] =[
                    "billing_number" => $billing["billing_number"],//請求書ID
                    "payment_status" => $billing["status"]["payment"],//入金ステータス
                ];
            }
        }
        // var_dump($conditions);
        foreach($conditions as $condition){

            $get_deal_uri = "https://api.hubapi.com/crm/v3/objects/deals/search?hapikey=" . env("HS_API_KEY");
            $eventsOptions["request"] = "POST";
            $query = [
            "filterGroups" => [
                [
                    "filters" => [
                        [
                        "propertyName" => "mf_invoice_id",
                        "operator" => "EQ",
                        "value" =>  $condition["billing_number"]
                        ],
                    ]
                ]
            ],
            "limit" => 5,
            "properties" => [ "mf_invoice_id", "payment_status"]
            ];

            $response = $client->request(
                'POST',
                $get_deal_uri,
                [
                    'headers' => $headers,
                    'json' => $query
                ]
            );
            $result_deals =  json_decode($response->getBody(), 1)["results"];
            // var_dump($result_deals);
            if(!empty($result_deals)){
                $status = $condition["payment_status"] === "入金済み" ? env('HS_PIPELINE_PAID_STAGE_ID') : env('HS_PIPELINE_PUBLISHED_STAGE_ID');

                $update_deal_uri = "https://api.hubapi.com/crm/v3/objects/deals/" . $result_deals[0]["id"] . "?hapikey=" . env("HS_API_KEY");

                $update_deal_form = [
                    "properties" => [

                        "dealstage" => $status

                    ]
                ];
                $response = $client->request(
                    'PATCH',
                    $update_deal_uri,
                    [
                        'headers' => $headers,
                        'json' => $update_deal_form
                    ]
                );
                $result_deals =  json_decode($response->getBody(), 1);
            }
        }
    }

    /**
     * @param
     * @return int
     */
    public function publish(): int
    {
        $client = new \GuzzleHttp\Client();
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $mf_headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            // 'Content-Type'=> 'application/x-www-form-urlencoded; charset=utf-8',
            // 'X-CSRF-TOKEN'=> 'csrf',
            'Authorization' => 'Bearer ' . env('MF_ACCESS_TOKEN')
        ];
        $mf_headers2 = [
            'Accept' => 'application/json',
            // 'Content-Type'=> 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            'X-CSRF-TOKEN' => 'csrf',
            'Authorization' => 'Bearer ' . env('MF_ACCESS_TOKEN')
        ];
        $deals = [];
        $id = [];
        //[ MoneyForward ]請求書IDの最後の番号を取得する
        $client = new Client();
        $response = $client->request(
            'GET',
            "https://invoice.moneyforward.com/api/v1/billings/search?excise_type=boolean?range_key=created_at&from=".date("Y-m-01")."&to=".date("Y-m-t"),
            [
                'headers' => $mf_headers,
                // 'json' => $form_param1,
            ]
        );
        //請求書情報
        $billings = json_decode((string) $response->getBody(),1)["billings"];
        $conditions = [];

        foreach($billings as $billing){
            $id[] = (int)filter_var($billing["billing_number"], FILTER_SANITIZE_NUMBER_INT);
        }
        $last_number = !empty($id) ? max($id) : 0;
        //[ Hubspot ]取引情報を取得する***********
        $form_params = [
            "filterGroups" => [
                [
                    "filters" => [

                        [
                            "propertyName" => "dealstage",
                            "operator" => "EQ",
                            "value" => env("HS_PIPELINE_PUBLISH_STAGE_ID")
                        ],

                    ]
                ]
            ],
            "limit" => 20,
            "properties" => ["dealname", "initial_billing_date", "mf_due_date","mf_invoice_note","mf_bank_account"]
        ];

        $client = new Client();
        // echo env("HS_API_KEY");
        $path = "https://api.hubapi.com/crm/v3/objects/deals/search?hapikey=" . env("HS_API_KEY");

        $response = $client->request(
            'POST',
            $path,
            [
                'headers' => $headers,
                'json' => $form_params
            ]
        );
        $result_deals = json_decode($response->getBody(), 1);

        foreach ($result_deals["results"] as $key => $deal) {
            $deal_properties = $deal["properties"];

            $deals[$key] = [
                "id" => $deal_properties["hs_object_id"],
                "name" => $deal_properties["dealname"],
                "billing_date" => array_key_exists("initial_billing_date", $deal_properties )? $deal_properties["initial_billing_date"] : "",
                "due_date" => array_key_exists("mf_due_date", $deal_properties )? $deal_properties["mf_due_date"] : "",
                "mf_invoice_note" => array_key_exists("mf_invoice_note", $deal_properties )? $deal["properties"]["mf_invoice_note"] : "",
                "bank_account" => array_key_exists("mf_bank_account", $deal_properties )? $deal["properties"]["mf_bank_account"] : "",

            ];
        }

        //[ Hubspot ] 取引情報に紐づく会社IDを取得する***********
        foreach ($deals as $deal) {

            $billing_number = sprintf('%09d',$last_number + 1);
            //End Point 設定
            $get_company_id_path = "https://api.hubapi.com/crm/v3/objects/deals/" . $deal["id"] . "/associations/company?paginateAssociations=false&limit=500&hapikey=" . env("HS_API_KEY");

            //実行
            $response = $client->request(
                'GET',
                $get_company_id_path,
                [
                    'headers' => $headers,
                ]

            );
            // var_dump( json_decode( $response->getBody(),1));
            $companies = json_decode($response->getBody(), 1);

            $company_id = $companies["results"][0]["id"];

            //[ Hubspot ] 会社の詳細情報を取得************
            $get_company_path =  "https://api.hubapi.com/companies/v2/companies/" . $company_id . "?hapikey=" . env("HS_API_KEY") . "&includePropertyVersions=true";

            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];
            $response = $client->request(
                'GET',
                $get_company_path,
                [
                    'headers' => $headers,
                ]
            );
            $result_companies =  json_decode($response->getBody(), 1);

            // DepartmentIDがすでに登録されている場合 ********
            if (array_key_exists("mf_department_id", $result_companies["properties"]) && $result_companies["properties"]["mf_department_id"]["value"] !== "") {

                $registered_mf_department_id = $result_companies["properties"]["mf_department_id"]["value"];
                // var_dump($registered_mf_department_id);
                // echo $registered_mf_department_id;
                $publish_info_param = [
                    "billing" => [
                        "department_id" => $registered_mf_department_id,
                        "title" => $deal["name"],//件名
                        "payment_condition" => $deal["bank_account"] ?? "",//支払い状況
                        "note" => $deal["mf_invoice_note"] ?? "", //備忘
                        "billing_date" => $deal["billing_date"] ?? date("Y/m/t"), //請求日
                        "due_date" => $deal["due_date"] ?? date("Y/m/t", strtotime("+1 month")), //支払い期限
                        "billing_number"=> "IN".$billing_number,

                        "items" => [
                            [
                                "name" => $deal["name"],
                                "code" => "",
                                "unit_price" =>  0,
                                "unit" => "円",
                                "quantity" =>  1,
                                "deduct" =>  false,
                                "excise" =>  true,
                                "_destroy" =>  false,
                                "disp_order" =>  0
                            ]
                        ]
                    ]
                ];

                // var_dump($publish_info_param);
                $publish_invoice_response = $client->request(
                    'POST',
                    "https://invoice.moneyforward.com/api/v1/billings?excise_type=boolean",
                    [
                        'headers' => $mf_headers,
                        'json' => $publish_info_param,
                    ]
                );
                //請求書情報
                $billing_number  = json_decode((string) $publish_invoice_response->getBody(),1)["billing_number"];

            } else { // DepartmentIDがまだ登録されていない場合 ********

                $company = [];
                $company["name"] = array_key_exists("name", $result_companies["properties"]) ? $result_companies["properties"]["name"]["value"] : "";
                $company["zip"] =  array_key_exists("zip", $result_companies["properties"]) ? $result_companies["properties"]["zip"]["value"]  : "";
                $company["phone"] =  array_key_exists("phone", $result_companies["properties"]) ? $result_companies["properties"]["phone"]["value"]  : "";
                $company["state"] =  array_key_exists("state", $result_companies["properties"]) ? $result_companies["properties"]["state"]["value"]  : "";
                $city =  array_key_exists("city", $result_companies["properties"]) ? $result_companies["properties"]["city"]["value"]  : "";
                $address =  array_key_exists("address", $result_companies["properties"]) ? $result_companies["properties"]["address"]["value"]  : "";
                $company["address"] = $city . $address; // array_key_exists("address",$result_companies["properties"])? $result_companies["properties"]["address"]["value"]  : "";
                $company["address2"] =  array_key_exists("address2", $result_companies["properties"]) ? $result_companies["properties"]["address2"]["value"]  : "";

                //[ Money Forward ] 会社情報を取引先として登録する*********
                $register_company_param = [
                    "partner" => [
                        "name" => $company["name"],
                        "name_suffix"=> "御中",
                        "zip" => $company["zip"],
                        "tel" => $company["phone"],
                        "prefecture" => $company["state"],
                        "address1" => $company["address"],
                        "address2" => $company["address2"]
                    ]
                ];

                $mf_register_company_response = $client->request(
                    'POST',
                    "https://invoice.moneyforward.com/api/v1/partners",
                    [
                        'headers' => $mf_headers2,
                        'form_params'     => $register_company_param,
                    ]
                );
                $response_body = json_decode($mf_register_company_response->getBody(), 1);
                $new_mf_department_id = $response_body["departments"][0]["id"];

                //[ Money Forward ] 作成された取引先の「部門ID」を利用して請求書を発行する********
                $publish_info_param = [
                    "billing" => [
                        "department_id" => $new_mf_department_id,
                        "title" => $deal["name"],
                        "payment_condition" => $deal["bank_account"] ?? "",
                        "note" => $deal["mf_invoice_note"] ?? "",
                        "billing_date" => $deal["billing_date"] ?? date("Y/m/t"), //請求日
                        "due_date" => $deal["due_date"] ?? date("Y/m/t", strtotime("+1 month")), //支払い期限
                        "billing_number"=> "IN".$billing_number,
                        "items" => [
                            [
                                "name" => $deal["name"],
                                "code" => "",
                                "unit_price" =>  0,
                                "unit" => "円",
                                "quantity" =>  1,
                                "deduct" =>  false,
                                "excise" =>  true,
                                "_destroy" =>  false,
                                "disp_order" =>  0
                            ]
                        ]
                    ]
                ];

                $publish_invoice_response = $client->request(
                    'POST',
                    "https://invoice.moneyforward.com/api/v1/billings?excise_type=boolean",
                    [
                        'headers' => $mf_headers,
                        'json'     => $publish_info_param,
                    ]
                );

                //請求書情報
                $billing_number  = json_decode((string) $publish_invoice_response->getBody(),1)["billing_number"];
                // (string) $publish_invoice_response->getBody(),1)["billing_number"];

                //[ Hubspot ] 請求書を発行した会社の部門IDを更新する********
                $update_company_uri = "https://api.hubapi.com/crm/v3/objects/companies/" . $company_id . "?hapikey=" . env("HS_API_KEY");
                $update_form = [
                    "properties" => [
                        "mf_department_id" => $new_mf_department_id
                    ]
                ];
                $headers = [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ];
                $response = $client->request(
                    'PATCH',
                    $update_company_uri,
                    [
                        'headers' => $headers,
                        'json' => $update_form
                    ]
                );
                $result_companies =  json_decode($response->getBody(), 1);

            }
            //[ Hubspot ] 請求書を発行した取引上のステージ/入金ステータス/請求書No を変更する********

            $update_deal_uri = "https://api.hubapi.com/crm/v3/objects/deals/" . $deal["id"] . "?hapikey=" . env("HS_API_KEY");
            $billing = !empty($deal["billing_date"]) ? $deal["billing_date"] : date("Y/m/t");

            $due = !empty($deal["due_date"]) ? $deal["due_date"] : date("Y/m/t", strtotime("+1 month"));
            $update_deal_form = [
                "properties" => [
                    "dealstage"=>env("HS_PIPELINE_PUBLISHED_STAGE_ID"),
                    "mf_invoice_id" => $billing_number,
                    // "payment_status" => "not_yet",
                    "initial_billing_date" => date("Y-m-d",strtotime($billing)),
                    "mf_due_date" => date("Y-m-d", strtotime($due)),
                ]
            ];
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];
            $response = $client->request(
                'PATCH',
                $update_deal_uri,
                [
                    'headers' => $headers,
                    'json' => $update_deal_form
                ]
            );
            $last_number++;
            $result_deals =  json_decode($response->getBody(), 1);

        }
        return count($deals);

    }
}
