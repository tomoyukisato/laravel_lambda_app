<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use App\Services\InvoiceService;


class MoneyforwardController extends Controller
{
    public function __construct()
    {
        $this->invoiceService = new InvoiceService();
    }
    /**
     * 未入金の請求書を取得する
     *
     * @return void
     */
    public function checkNotYetInvoice()
    {
        $this->invoiceService->check();
        // $client = new Client();
        // $mf_headers = [
        //     'Accept' => 'application/json',
        //     'Content-Type' => 'application/json',
        //     // 'Content-Type'=> 'application/x-www-form-urlencoded; charset=utf-8',
        //     // 'X-CSRF-TOKEN'=> 'csrf',
        //     'Authorization' => 'Bearer ' . env('MF_ACCESS_TOKEN')
        // ];
        // $response = $client->request(
        //     'GET',
        //     "https://invoice.moneyforward.com/api/v1/billings/search?excise_type=boolean?range_key=created_at&from=2020-11-01&to=2020-11-30",
        //     [
        //         'headers' => $mf_headers,
        //         // 'json' => $form_param1,
        //     ]
        // );
        // //請求書情報
        // $billings = json_decode((string) $response->getBody(),1)["billings"];
        // $conditions = [];

        // foreach($billings as $billing){
        //     // $id[] = (int)filter_var($billing["billing_number"], FILTER_SANITIZE_NUMBER_INT);
        //     $conditions[] =[
        //         "billing_number" => $billing["billing_number"],//請求書ID
        //         "payment_status" => $billing["status"]["payment"],//入金ステータス
        //     ];
        // }
        // var_dump($conditions);
        // foreach($conditions as $condition){

        //     $get_deal_uri = "https://api.hubapi.com/crm/v3/objects/deals/search?hapikey=" . env("HS_API_KEY");
        //     $eventsOptions["request"] = "POST";
        //     $query = [
        //     "filterGroups" => [
        //         [
        //             "filters" => [
        //                 [
        //                 "propertyName" => "mf_invoice_id",
        //                 "operator" => "EQ",
        //                 "value" =>  $condition["billing_number"]
        //                 ],
        //             ]
        //         ]
        //     ],
        //     "limit" => 5,
        //     "properties" => [ "mf_invoice_id", "payment_status"]
        //     ];
        //     $headers = [
        //         'Accept' => 'application/json',
        //         'Content-Type' => 'application/json',
        //     ];
        //     $response = $client->request(
        //         'POST',
        //         $get_deal_uri,
        //         [
        //             'headers' => $headers,
        //             'json' => $query
        //         ]
        //     );
        //     $result_deals =  json_decode($response->getBody(), 1)["results"];
        //     if(!empty($result_deals)){
        //         $status = $condition["payment_status"] === "入金済み" ? "already" : 'not_yet';

        //         $update_deal_uri = "https://api.hubapi.com/crm/v3/objects/deals/" . $result_deals[0]["id"] . "?hapikey=" . env("HS_API_KEY");

        //         $update_deal_form = [
        //             "properties" => [

        //                 "payment_status" => $status

        //             ]
        //         ];
        //         $headers = [
        //             'Accept' => 'application/json',
        //             'Content-Type' => 'application/json',
        //         ];
        //         $response = $client->request(
        //             'PATCH',
        //             $update_deal_uri,
        //             [
        //                 'headers' => $headers,
        //                 'json' => $update_deal_form
        //             ]
        //         );
        //         $result_deals =  json_decode($response->getBody(), 1);
        //     }
        // }
    }
    /**
     * 請求書を発行する
     *
     * @return void
     */
    public function issueInvoice()
    {
        try {
            $this->invoiceService->publish();

            // $curlOptions["url"] = "https://api.hubapi.com/crm/v3/objects/deals/search?hapikey=" . env("HS_API_KEY");
            // $curlOptions["request"] = "POST";
            // echo env("PIPELINE_OPEN_STAGE_ID");
            // $query = [
            //   "filterGroups" => [

            //     [
            //       "filters" => [

            //         [
            //           "propertyName" => "dealstage",
            //           "operator" => "EQ",
            //           "value" =>  env("HS_PIPELINE_OPEN_STAGE_ID")
            //         ],

            //       ]
            //     ]
            //   ],
            //   "limit" => 20,
            //   "properties" => ["jointly_held", "dealname", "event_capacity", "place", "orientation_address"]
            // ];
            // $curlOptions["postfield"] = json_encode($query);

            // $events = json_decode($this->exec($curlOptions), 1);
            // var_dump($events);

            //取引情報を取得する
            // $form_params = [
            //     "filterGroups" => [

            //         [
            //             "filters" => [

            //                 [
            //                     "propertyName" => "dealstage",
            //                     "operator" => "EQ",
            //                     "value" => env("HS_PIPELINE_PUBLISH_STAGE_ID")
            //                 ],

            //             ]
            //         ]
            //     ],
            //     "limit" => 20,
            //     "properties" => ["dealname", "billing_date", "due_date","mf_invoice_note","bank_account"]
            // ];

            // $client = new Client();
            // echo env("HS_API_KEY");
            // $path = "https://api.hubapi.com/crm/v3/objects/deals/search?hapikey=" . env("HS_API_KEY");
            // // $path = "https://api.hubapi.com/deals/v1/deal/paged?hapikey=".env("HS_API_KEY");

            // // $path = 'https://invoice.moneyforward.com/api/v1/office';
            // $headers = [
            //     'Accept' => 'application/json',
            //     'Content-Type' => 'application/json',
            //     // 'Content-Type'=> 'application/x-www-form-urlencoded; charset=utf-8',
            //     // 'X-CSRF-TOKEN'=> 'csrf',
            // ];
            // $response = $client->request(
            //     'POST',
            //     $path,
            //     [
            //         'headers' => $headers,
            //         'json' => $form_params
            //     ]
            // );
            // $deals = [];
            // // var_dump( json_decode( $response->getBody(),1));
            // $result_deals = json_decode($response->getBody(), 1);
            // foreach ($result_deals["results"] as $key => $deal) {
            //     $deals[$key] = [
            //         "id" => $deal["properties"]["hs_object_id"],
            //         "name" => $deal["properties"]["dealname"],
            //         "billing_date" => $deal["properties"]["billing_date"],
            //         "due_date" => $deal["properties"]["due_date"],
            //         "mf_invoice_note" => $deal["properties"]["mf_invoice_note"],
            //         "bank_account" => $deal["properties"]["bank_account"],

            //     ];
            //     // var_dump($deal["properties"]);
            // }

            // $mf_headers = [
            //     'Accept' => 'application/json',
            //     'Content-Type' => 'application/json',
            //     // 'Content-Type'=> 'application/x-www-form-urlencoded; charset=utf-8',
            //     // 'X-CSRF-TOKEN'=> 'csrf',
            //     'Authorization' => 'Bearer ' . env('MF_ACCESS_TOKEN')
            // ];
            // $mf_headers2 = [
            //     'Accept' => 'application/json',
            //     // 'Content-Type'=> 'application/json',
            //     'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            //     'X-CSRF-TOKEN' => 'csrf',
            //     'Authorization' => 'Bearer ' . env('MF_ACCESS_TOKEN')
            // ];

            // foreach ($deals as $deal) {
            //     $billing_number = "";
            //     //取引情報に紐づく会社IDを取得する
            //     $get_company_id_path = "https://api.hubapi.com/crm/v3/objects/deals/" . $deal["id"] . "/associations/company?paginateAssociations=false&limit=500&hapikey=" . env("HS_API_KEY");
            //     // $path = "https://api.hubapi.com/deals/v1/deal/paged?hapikey=".env("HS_API_KEY");

            //     // $path = 'https://invoice.moneyforward.com/api/v1/office';
            //     $headers = [
            //         'Accept' => 'application/json',
            //         'Content-Type' => 'application/json',
            //         // 'Content-Type'=> 'application/x-www-form-urlencoded; charset=utf-8',
            //         // 'X-CSRF-TOKEN'=> 'csrf',
            //     ];
            //     $response = $client->request(
            //         'GET',
            //         $get_company_id_path,
            //         [
            //             'headers' => $headers,
            //             // 'json'=> $form_params
            //         ]
            //     );
            //     // var_dump( json_decode( $response->getBody(),1));
            //     $companies = json_decode($response->getBody(), 1);

            //     //ループ会社情報取得
            //     // foreach($companies["results"][0] as $company){
            //     echo "compay";
            //     // var_dump($company);

            //     $company_id = $companies["results"][0]["id"];
            //     //会社の詳細情報を取得
            //     $get_company_path =  "https://api.hubapi.com/companies/v2/companies/" . $company_id . "?hapikey=" . env("HS_API_KEY") . "&includePropertyVersions=true";
            //     // var_dump($company);
            //     $headers = [
            //         'Accept' => 'application/json',
            //         'Content-Type' => 'application/json',
            //     ];
            //     $response = $client->request(
            //         'GET',
            //         $get_company_path,
            //         [
            //             'headers' => $headers,
            //             // 'json'=> $form_params
            //         ]
            //     );
            //     $result_companies =  json_decode($response->getBody(), 1);

            //     //MFに品目を登録する
            //     // $regiseter_item_path =  "https://invoice.moneyforward.com/api/v1/items";
            //     // // var_dump($company);
            //     // $res = $client->request(
            //     //     'POST',$regiseter_item_path,
            //     //     [
            //     //         'headers'=> $mf_headers,
            //     //         'form_params' => [
            //     //             "item"=>
            //     //                     [
            //     //                         "name"=> $deal["name"],
            //     //                         "excise"=>"ten_percent"
            //     //                     ]

            //     //         ]
            //     //     ]
            //     // );
            //     // var_dump($res);
            //     // echo "<pre>";

            //     // var_dump( $result_companies );
            //     // echo $companies["name"] = $result_companies["name"]["value"];
            //     // echo $companies["department_id"]
            //     //DepartmentID　有り
            //     if (array_key_exists("mf_department_id", $result_companies["properties"]) && $result_companies["properties"]["mf_department_id"]["value"] !== "") {

            //         $registered_mf_department_id = $result_companies["properties"]["mf_department_id"]["value"];
            //         // echo $deal["name"];
            //         // echo $registered_mf_department_id;
            //         $form_param1 = [
            //             "billing" => [
            //                 "department_id" => $registered_mf_department_id,
            //                 "title" => $deal["name"],
            //                 // "billing_number" => 15,
            //                 // "tags" => "hoge,fuga",
            //                 "payment_condition" => $deal["bank_account"] ?? "",
            //                 "note" => $deal["mf_invoice_note"] ?? "",
            //                 // "document_name"=> "string",
            //                 // "total_price"=>10,//総計
            //                 "billing_date" => $deal["billing_date"] ?? "", //請求日
            //                 "due_date" => $deal["due_date"] ?? "", //支払い期限
            //                 // "sales_date" => "2020-11-15",
            //                 // "subtotal"=> 10,//小計

            //                 "items" => [
            //                     [
            //                         "name" => $deal["name"],
            //                         "code" => "",
            //                         "unit_price" =>  0,
            //                         "unit" => "円",
            //                         "quantity" =>  1,
            //                         "deduct" =>  false,
            //                         "excise" =>  true,
            //                         "_destroy" =>  false,
            //                         "disp_order" =>  0
            //                     ]
            //                 ]

            //             ]

            //         ];

            //         // var_dump($form_param1);
            //         $response1 = $client->request(
            //             'POST',
            //             "https://invoice.moneyforward.com/api/v1/billings?excise_type=boolean",
            //             [
            //                 'headers' => $mf_headers,
            //                 'json' => $form_param1,
            //             ]
            //         );
            //         //請求書情報
            //         echo (string) $response1->getBody();
            //         $billing_number  = json_decode((string) $response1->getBody(),1)["billing_number"];

            //     } else { //DepartmentID　無し

            //         $company = [];
            //         $company["name"] = array_key_exists("name", $result_companies["properties"]) ? $result_companies["properties"]["name"]["value"] : "";
            //         $company["zip"] =  array_key_exists("zip", $result_companies["properties"]) ? $result_companies["properties"]["zip"]["value"]  : "";
            //         $company["phone"] =  array_key_exists("phone", $result_companies["properties"]) ? $result_companies["properties"]["phone"]["value"]  : "";
            //         $company["state"] =  array_key_exists("state", $result_companies["properties"]) ? $result_companies["properties"]["state"]["value"]  : "";
            //         $city =  array_key_exists("city", $result_companies["properties"]) ? $result_companies["properties"]["city"]["value"]  : "";
            //         $address =  array_key_exists("address", $result_companies["properties"]) ? $result_companies["properties"]["address"]["value"]  : "";
            //         $company["address"] = $city . $address; // array_key_exists("address",$result_companies["properties"])? $result_companies["properties"]["address"]["value"]  : "";
            //         $company["address2"] =  array_key_exists("address2", $result_companies["properties"]) ? $result_companies["properties"]["address2"]["value"]  : "";

            //         //会社情報を取引先として登録する
            //         $form_param0 = [
            //             "partner" => [
            //                 "name" => $company["name"],
            //                 "name_suffix"=> "御中",
            //                 "zip" => $company["zip"],
            //                 "tel" => $company["phone"],
            //                 "prefecture" => $company["state"],
            //                 "address1" => $company["address"],
            //                 "address2" => $company["address2"]
            //             ]
            //         ];

            //         $response0 = $client->request(
            //             'POST',
            //             "https://invoice.moneyforward.com/api/v1/partners",
            //             [
            //                 'headers' => $mf_headers2,
            //                 'form_params'     => $form_param0,
            //             ]
            //         );
            //         $response_body = json_decode($response0->getBody(), 1);

            //         $new_mf_department_id = $response_body["departments"][0]["id"];

            //         // 作成された取引先の「部門ID」を利用して請求書を発行する
            //         $new_form_param = [
            //             "billing" => [
            //                 "department_id" => $new_mf_department_id,
            //                 "title" => $deal["name"],
            //                 "payment_condition" => $deal["bank_account"] ?? "",
            //                 "note" => $deal["mf_invoice_note"] ?? "",
            //                 "billing_date" => $deal["billing_date"] ?? "",//請求日
            //                 "due_date" => $deal["due_date"] ?? "", //支払い期限

            //                 "items" => [
            //                     [
            //                         "name" => $deal["name"],
            //                         "code" => "",
            //                         "unit_price" =>  0,
            //                         "unit" => "円",
            //                         "quantity" =>  1,
            //                         "deduct" =>  false,
            //                         "excise" =>  true,
            //                         "_destroy" =>  false,
            //                         "disp_order" =>  0
            //                     ]
            //                 ]
            //             ]
            //         ];

            //         $response1 = $client->request(
            //             'POST',
            //             "https://invoice.moneyforward.com/api/v1/billings?excise_type=boolean",
            //             [
            //                 'headers' => $mf_headers,
            //                 'json'     => $new_form_param,
            //             ]
            //         );
            //         //請求書情報
            //         echo (string) $response1->getBody();
            //         $billing_number  = json_decode((string) $response1->getBody(),1)["billing_number"];

            //         //Hubspot 側で請求書を発行した会社の部門IDを更新する

            //         $update_company_uri = "https://api.hubapi.com/crm/v3/objects/companies/" . $company_id . "?hapikey=" . env("HS_API_KEY");
            //         $update_form = [
            //             "properties" => [
            //                 "mf_department_id" => $new_mf_department_id
            //             ]
            //         ];
            //         $headers = [
            //             'Accept' => 'application/json',
            //             'Content-Type' => 'application/json',
            //         ];
            //         $response = $client->request(
            //             'PATCH',
            //             $update_company_uri,
            //             [
            //                 'headers' => $headers,
            //                 'json' => $update_form
            //             ]
            //         );
            //         $result_companies =  json_decode($response->getBody(), 1);

            //     }
            //     //Hubspot 側で請求書を発行した取引上のステージを変更する
            //     $update_deal_uri = "https://api.hubapi.com/crm/v3/objects/deals/" . $deal["id"] . "?hapikey=" . env("HS_API_KEY");

            //     $update_deal_form = [
            //         "properties" => [
            //             // "mf_invoice_id"=>env("HS_PIPELINE_PUBLISHED_STAGE_ID"),
            //             "mf_invoice_id" => $billing_number,
            //             "payment_status" => "not_yet"
            //         ]
            //     ];
            //     $headers = [
            //         'Accept' => 'application/json',
            //         'Content-Type' => 'application/json',
            //     ];
            //     $response = $client->request(
            //         'PATCH',
            //         $update_deal_uri,
            //         [
            //             'headers' => $headers,
            //             'json' => $update_deal_form
            //         ]
            //     );
            //     $result_deals =  json_decode($response->getBody(), 1);

            // }
            // var_dump($companies);


            // var_dump($componies);

            // $base_url = 'https://invoice.moneyforward.com';

            // $client = new Client([
            //     'base_uri' => $base_url,
            // ]);

            // $path = '/api/v1/office';
            // $headers = [
            //     'Accept'=> 'application/json',
            //     'Content-Type'=> 'application/x-www-form-urlencoded; charset=utf-8',
            //     'X-CSRF-TOKEN'=> 'csrf',
            //     'Authorization'=> 'Bearer '.env('MF_ACCESS_TOKEN')
            // ];
            // $response = $client->request(
            //     'GET',
            //     $path,
            //     [
            //         // 'allow_redirects' => true,
            //         // 'http_errors' => false,
            //         'headers'=> $headers,
            //         //'form_params'     => $form_params,
            //     ]
            // );
            // var_dump($response);
            // $response_body = (string) $response->getBody();
            // echo $response_body;

            //取引先登録
            // $form_param0 = [ "partner" => [ "name" => "サンプル取引先1" ]];
            // $response0 = $client->request(
            //     'POST',
            //     "/api/v1/partners",
            //     [
            //         'headers'=> $headers,
            //         'form_params'     => $form_param0,
            //     ]
            // );
            // $response_body = (string) $response0->getBody();
            // echo $response_body;
            //請求書発行
            // $form_param1 = [ "billing" => [ "department_id" => "ZcfWBfIOqZcTiIemkYTc4w" ]];

            // $response1 = $client->request(
            //     'POST',
            //     "/api/v1/billings",
            //     [
            //         'headers'=> $headers,
            //         'form_params'     => $form_param1,
            //     ]
            // );

        } catch (ClientException $e) {
            //do some thing here
            // echo $e;
        }
    }
}
