<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GBGCms;
use App\Models\GBGAddon;
use App\Models\GBGProductImage;
use App\Models\GBGProduct;
use Illuminate\Http\Request;
use Auth;

class GBGAddonController extends CommonController
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

  
    // public function list(Request $request){

    //     if($this->checkPermission('cms','list') == false && $this->checkSuperPermission('cms','list') == false ){
    //         $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
    //         return redirect()->route('admin.home');
    //     }

    //     $websiteShortCode = 'gbg';

    //     $where = $orWhere = [];

    //     $result = GBGCms::orderBy('created_at', 'desc')->get();

    //     return view('admin.GBG.cms.list', ['result' => $result, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    // }

    
    public function add(Request $request){

        if($this->checkPermission('addon','list') == false && $this->checkSuperPermission('addon','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $obj = new GBGAddon;

        $obj2 = new GBGProductImage;

        if($request->isMethod('POST')){
            //dd($request);
            $request->validate([
                'image'=>'required',
                'product_extra_title'=>'required',
                'product_extra_price'=>'required',
            ]);
            
            $imageName = '';

            $imagefile=$request->file("image");
            if (isset($imagefile)){

                if(!is_dir(public_path() . '/uploads/addon/')){
                    mkdir(public_path() . '/uploads/addon/', 0777, true);
                }

                $imageName = 'AD-'.strtotime(now()).rand(11111,99999).'.'.$imagefile->getClientOriginalExtension();
                //$imagefile->move(public_path(). \uploads\banner\, $imageName);
                $imagefile->move(public_path() . '/uploads/addon/', $imageName);
            }

            $addon = $obj->create([
                    'product_name' => $request->product_extra_title, 
                    'price' => $request->product_extra_price, 
                    'product_type' => 'A',
                    'slug'=> str_replace(' ', '-', strtolower($request->product_extra_title)),
                    'description' => $request->product_extra_title,
                    'alt_key' => $request->product_extra_title,
                    'meta_title' => $request->product_extra_title,
                    'meta_description' => $request->product_extra_title,
                    'created_at' => Auth::guard('admin')->user()->id
            ]);


            if($addon)
            {
                $addonproductimage = $obj2->create([
                    'product_id' => $addon->id, 
                    'name' => $imageName, 
                    'status' => 'A',
                    'default_image' => 'Y',
                    'created_at' => Auth::guard('admin')->user()->id
                ]);

                $sku_generate_array = array();
                $sku_generate_array['product_type'] = 'A';
                $sku_generate_array['delivery_by']  = 'C';
                $sku_generate_array['product_id']   = $addon->id;
                $sku_generate_array['country_id']   = 99;
                $sku = @$this->generate_sku($sku_generate_array);
                /* Update product sku */
                GBGProduct::where('id',$addon->id)->update(['sku'=>$sku]);

                $request->session()->flash('alert-success', 'Addon successfully added.');
                return redirect()->route('admin.gbg.cms.list');
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back()->with($request->except(['_method', '_token']));
            }
            
        }

        return view('admin.gbg.addon.add', ['request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    // public function edit($id = null, Request $request){

    //     if($this->checkPermission('cms','list') == false && $this->checkSuperPermission('cms','list') == false ){
    //         $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
    //         return redirect()->route('admin.home');
    //     }

    //     $websiteShortCode = 'gbg';

    //     $id = base64_decode($id);
	// 	$dataDetails  = GBGCms::where('id',$id)->first();

    //     $obj = new GBGCms;

    //     if($request->isMethod('POST')){
    //         //dd($request);
    //         $request->validate([
    //             'title'=>'required',
    //             'content'=>'required',
    //             'meta_title'=>'required',
    //             'meta_description'=>'required'
    //         ]);
            
    //         $update_arr['title'] = $request->title;
    //         $update_arr['content'] = $request->content;
    //         $update_arr['slug'] = $request->slug;
    //         $update_arr['meta_title'] = $request->meta_title;
    //         $update_arr['meta_description'] = $request->meta_description;

    //         if(GBGCms::where(['id' => $request->formid])->update($update_arr)){
                   
    //             $request->session()->flash('alert-success', 'CMS successfully updated.');
    //             return redirect()->route('admin.gbg.cms.list');
    //         }else{
    //             $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
    //             return redirect()->back()->with($request->except(['_method', '_token']));
    //         }
    //     }

    //     return view('admin.GBG.cms.edit', ['dataDetails' => $dataDetails, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    // }

    // public function delete($id = null, Request $request)
    // {
    //     if($this->checkPermission('cms','list') == false && $this->checkSuperPermission('cms','list') == false ){
    //         $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
    //         return redirect()->route('admin.home');
    //     }

    //     if($id == null){
    //         return redirect()->route('admin.home');
    //     }
    //     $id = base64_decode($id);

    //     //$event_details = HomeNews::find($id);

    //     //@unlink(public_path() . '/uploaded/event_images/' . $event_details->eimg);

    //     if( GBGCms::where(['id' => $id])->delete()){
    //         $request->session()->flash('alert-success', 'CMS deleted successfully.');
    //         return redirect()->back();
    //     }else{
    //         $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
    //         return redirect()->back();
    //     }
    // }

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
    //     if(GBGCms::where(['id' => $id])->update(['is_block' => $block])){
    //         //$request->session()->flash('alert-success', 'Category successfully '.$blockText);
    //         //return redirect()->back();
    //         $prompt = array('status' => 1, 'id' => $id, 'event' => $blockText);

    //         echo json_encode($prompt);
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