<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GBGCategory;
use App\Models\GBGPriceBrand;
use App\Models\GBGProduct;
use App\Models\GBGProductCategory;
use App\Models\GBGProductAttribute;
use App\Models\GBGProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;


class GBGProductController extends CommonController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

  
    public function list(Request $request){

        if($this->checkPermission('product','list') == false && $this->checkSuperPermission('product','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $where = $orWhere = [];

        //DB::enableQueryLog();
            
        $result = GBGProduct::select('id', 'product_name', 'fnid', 'is_block', 'has_attribute', 'created_at')->orderBy('id', 'DESC')->get();

        //dd($result[0]);
        //dd(DB::getQueryLog());

        // $result = DB::table('products')
        //          ->join('product_images','products.id','=','product_images.product_id')
        //          ->join('product_attributes'.'products.id','=','product_attributes.product_id')
        //          ->join('product_categories','product.id','=','product_categories.product_id')
        //          ->select(['products.*','product_attributes.*','product_images.*'])
        //          ->get();
       
        //$result = GBGProduct::join('product_categories','products.id','=','product_categories.product_id')->get(["product_categories.*","products.*"]);
        //$result1 = GBGProductAttribute::all();
        //$result2 = GBGProductImage::all();
        return view('admin.GBG.product.list', ['result' => $result, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    
    public function add(Request $request){

        if($this->checkPermission('product','list') == false && $this->checkSuperPermission('product','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';
        

        $obj = new GBGProduct();

        if($request->isMethod('POST')){
            //dd($request);
            $request->validate([
                'product_title'=>'required',
                'categories_id'=>'required',
                'product_description'=>'required',
                'has_attribute'=>'required',
                'delivery_delay_days'=>'required',
                'alt_keyword'=>'required',
                'meta_title'=>'required',
                'meta_description'=>'required',
                'fnid'=>'required',
                'extra_field'=>'required',
            ]);

            // DB::enableQueryLog();
            // dd(DB::getQueryLog());

            if($request->has_attribute == 'N'){
                $price = $request->product_price;
            }else{
                $price = 0;
            }

            $product_data = $obj->create([
                    'product_name' => $request->product_title,
                    'description' => $request->product_description,
                    'content' => $request->content,
                    'delivery_info' => $request->shipping,
                    'has_attribute' => $request->has_attribute,
                    'price' => $price,
                    'actual_price' => $request->product_actual_price,
                    'delivery_delay_days' => $request->delivery_delay_days, 
                    'alt_key' => $request->alt_keyword,
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'slug'=> GBGProduct::getUniqueSlug($request->product_title),
                    'search_tag' => $request->search_tag,
                    'fnid' => $request->fnid,
                    'extra_field' => $request->extra_field
            ]);

            if($product_data){

                $sku_generate_array = array();
                $sku_generate_array['product_type'] = 'A';
                $sku_generate_array['delivery_by']  = 'C';
                $sku_generate_array['product_id']   = $product_data->id;
                $sku_generate_array['country_id']   = 99;
                $sku = @$this->generate_sku($sku_generate_array);
                /* Update product sku */
                GBGProduct::where('id',$product_data->id)->update(['sku'=>$sku]);

                if(isset($request->has_attribute) && $request->has_attribute == 'Y'){
                    if((isset($request->attr_title) && count($request->attr_title)>0) && (isset($request->attr_price) && count($request->attr_price)>0) && (isset($request->attr_actual_price) && count($request->attr_actual_price)>0)){
                        foreach ($request->attr_title as $attr_key => $attribute_title) {
                            $product_attribute = [];
                            $product_attribute['product_id'] = $product_data->id;
                            $product_attribute['title']      = $attribute_title;
                            $product_attribute['price']      = $request->attr_price[$attr_key];
                            $product_attribute['actual_price'] = $request->attr_actual_price[$attr_key];
                            $product_attribute['sl_no']      = $attr_key;
                            
                            GBGProductAttribute::create($product_attribute);

                            if( $attr_key == 0 ){
                                GBGProduct::where('id',$product_data->id)->update(['price' => $request->attr_price[$attr_key]]);
                            }
                        }
                    }
                }

                if((isset($request->categories_id) && count($request->categories_id)>0) && $product_data){
                    foreach( $request->categories_id as $cat_id ) {
                        $categories_data['product_id'] = $product_data->id;
                        $categories_data['category_id']  = $cat_id;
                        GBGProductCategory::create($categories_data);
                    }
                }
    
                $product_image['product_id'] = $product_data->id;
                $product_image['name']  = $request->fnid.'.webp';
                $product_image['thumb']  = $request->fnid.'_mob.webp';
                $product_image['default_image']  = 'Y';
                $product_image['created_at'] = date('Y-m-d H:i:s');
                $product_image['updated_at'] = date('Y-m-d H:i:s');
                GBGProductImage::create($product_image);
 
            
                $request->session()->flash('alert-success', 'Product successfully added.');
                return redirect()->route('admin.gbg.product.add');
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back()->with($request->except(['_method', '_token']));
            }
            
        }
        $catdata = GBGCategory::all();
        return view('admin.gbg.product.add', ['request' => $request, 'catdata' => $catdata, 'websiteShortCode' => $websiteShortCode]);
    }

    // public function edit($id = null, Request $request){

    //     if($this->checkPermission('category','list') == false && $this->checkSuperPermission('category','list') == false ){
    //         $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
    //         return redirect()->route('admin.home');
    //     }

    //     $websiteShortCode = 'gbg';
    //     $pricedtl = $imageName = '';

    //     $id = base64_decode($id);
	// 	$dataDetails  = GBGCategory::where('id',$id)->first();

    //     $dataPriceBrand = GBGPriceBrand::where('category_id', $id)->first();

    //     if($dataPriceBrand){
    //         $pricedtl = $dataPriceBrand;
    //     }else{
    //         $pricedtl = '';
    //     }
        
    //     $obj = new GBGCategory;

    //     if($request->isMethod('POST')){
    //         //dd($request);
    //         $request->validate([
    //             'ctitle'=>'required',
    //             'chead'=>'required',
    //             'cbannerhead'=>'required',
    //             'ccontenttop'=>'required',
    //             'ccontentbottom'=>'required',
    //             'cmeta_title'=>'required',
    //             'cmeta_description'=>'required',
    //             'ctagline'=>'required',
    //             'ctophead'=>'required'
    //         ]);
            
    //         $imagefile=$request->file("cimage");
    //         if (isset($imagefile)){
    //             $imageName = strtotime(now()).rand(11111,99999).'-banner.'.$imagefile->getClientOriginalExtension();
    //             $imagefile->move(public_path() . '/uploads/banner/', $imageName);
    //         }

    //         $formid=$request->formid;

    //         $update_arr['name'] = $request->ctitle;
    //         $update_arr['page_head'] = $request->chead;
    //         if(isset($imagefile)){
    //             $update_arr['image'] = $imageName;
    //         }
    //         $update_arr['banner_heading'] = $request->cbannerhead;
    //         $update_arr['menu_head_only'] = $request->cmenuhead;
    //         $update_arr['cat_section'] = $request->ctype;
    //         $update_arr['content_top'] = $request->ccontenttop;
    //         $update_arr['content_bottom'] = $request->ccontentbottom;
    //         $update_arr['meta_title'] = $request->cmeta_title;
    //         $update_arr['meta_description'] = $request->cmeta_description;
    //         $update_arr['tag_line'] = $request->ctagline;
    //         $update_arr['tophead'] = $request->ctophead;  
            
    //         $dataPriceBrandUpd= GBGPriceBrand::where('category_id', $formid)->first();

    //         if ($request->ctype=="P"){
    //             if($dataPriceBrandUpd){
    //                 $dataPriceBrandUpd->from_price=$request->from_price;
    //                 $dataPriceBrandUpd->to_price=$request->to_price;
    //                 $dataPriceBrandUpd->equation=$request->equation;
    //                 $dataPriceBrandUpd->update();
    //             }else{
    //                 $obj2 = new GBGPriceBrand();
    //                 $obj2->category_id=$formid;
    //                 $obj2->from_price=$request->from_price;
    //                 $obj2->to_price=$request->to_price;
    //                 $obj2->equation=$request->equation;
    //                 $obj2->save();
    //             }
    //         }else{
    //             if($dataPriceBrandUpd){
    //                $object=GBGPriceBrand::where('id', $dataPriceBrandUpd->id)->delete();
    //             }
    //         }
            
    //         if(GBGCategory::where(['id' => $request->formid])->update($update_arr)){
                   
    //             $request->session()->flash('alert-success', 'Catgory successfully updated.');
    //             return redirect()->route('admin.gbg.category.list');
    //         }else{
    //             $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
    //             return redirect()->back()->with($request->except(['_method', '_token']));
    //         }
    //     }
    //      return view('admin.GBG.category.edit', ['dataDetails' => $dataDetails, 'request' => $request, 'websiteShortCode' => $websiteShortCode, 'pricedtl' => $pricedtl]);
    // }

    public function delete($id = null, Request $request)
    {
        if($this->checkPermission('product','list') == false && $this->checkSuperPermission('product','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }
        
        $websiteShortCode = 'gbg'; 

        if($id == null){
            return redirect()->route('admin.home');
        }
        $id = base64_decode($id);
        $delobject = GBGProduct::where(['id' => $id])->first();
        $delimage = GBGProductImage::where(['product_id' => $id])->first();
        //dd($delimage);
        $delcatagory = GBGProductCategory::where(['product_id' => $id])->get();
        //dd($delcatagory);
         if ($delobject->has_attribute == "Y"){
            $delattribute = GBGProductAttribute::where(['product_id' => $id])->get();
            //dd($delattribute);
            $delattribute->delete();
        }
        $delimage->delete();
        $delcatagory->delete();
        if(GBGProduct::where(['id' => $id])->delete()){
            $request->session()->flash('alert-success', 'Product deleted successfully.');
            return redirect()->back();
        }else{
            $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
            return redirect()->back();
        }
    }

    // public function status(Request $request)
    // {
    //     if($request->id == null || $request->status == null){
    //         return redirect()->route('admin.dashboard');
    //     }
    //     $id = base64_decode($request->id);
    //     //$block = 'N';
    //     //$blockText = 'blocked';
    //     switch($request->status){
    //         case 'N':
    //             $block = 'Y';
    //             $blockText = 'Block';
    //             break;
    //         case 'Y':
    //             $block = 'N';
    //             $blockText = 'Unblock';
    //             break;
    //         default:
    //             $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
    //             return redirect()->back();
    //     }
    //     if(GBGCategory::where(['id' => $id])->update(['is_block' => $block])){
    //         //$request->session()->flash('alert-success', 'Category successfully '.$blockText);
    //         //return redirect()->back();
    //         $prompt = array('status' => 1, 'id' => $id, 'event' => $blockText);

    //         echo json_encode($prompt);
    //     }else{
    //         $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
    //         return redirect()->back();
    //     }
    // }

    // public function deleteimage($id = null, Request $request){
    //     if($this->checkPermission('category','list') == false && $this->checkSuperPermission('category','list') == false ){
    //         $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
    //         return redirect()->route('admin.home');
    //     }
        
    //     $websiteShortCode = 'gbg';

    //     if($id == null){
    //         return redirect()->route('admin.home');
    //     }
    //     $id = base64_decode($id);
    //     $dataDetails  = GBGCategory::find($id);
    //     unlink(public_path() . '/uploads/banner/'.$dataDetails->image);
    //     //$dataDetails->image='';
    //     //$dataDetails->update();

    //     if($dataDetails->update(['image' => ''])){
    //         $request->session()->flash('alert-success', 'Category image deleted successfully.');
    //         return redirect()->back();
    //     }else{
    //         $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
    //         return redirect()->back();
    //     }
        
    // }

    public function generate_sku($request){
        $formated_number = '';
        $total_digit_number = '00000';
        $product_id = $request['product_id'];
        $product_id_length = strlen((string)$product_id);
        $concate_digit_and_pid = $total_digit_number.$product_id;
        $formated_product_number = substr($concate_digit_and_pid, $product_id_length);
        
        
        $country_sort_code = "GBG";
        $webside_code = 1;
        $formated_number = $request['product_type'].$request['delivery_by'].$formated_product_number.$country_sort_code;
        return $formated_number;
    }
}