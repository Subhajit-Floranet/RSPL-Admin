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

  
    public function list(Request $request){

        if($this->checkPermission('addon','list') == false && $this->checkSuperPermission('addon','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $where = $orWhere = [];

        $result = GBGAddon::where(['product_type' => 'A'])->orderBy('created_at', 'desc')->get();

        return view('admin.GBG.addon.list', ['result' => $result, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    
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

                $imageName = 'AD-.'.strtotime(now()).rand(11111,99999).'.'.$imagefile->getClientOriginalExtension();
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
                return redirect()->route('admin.gbg.addon.list');
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back()->with($request->except(['_method', '_token']));
            }
            
        }

        return view('admin.gbg.addon.add', ['request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    public function edit($id = null, Request $request){

        if($this->checkPermission('addon','list') == false && $this->checkSuperPermission('addon','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $id = base64_decode($id);
		$dataDetails  = GBGAddon::where(['id' => $id])->first();
        $dataDetailsimage = GBGProductImage::where(['product_id' => $id])->first();
       

        if($request->isMethod('POST')){
            //dd($request);
            $request->validate([
                'product_extra_title'=>'required',
                'product_extra_price'=>'required',
            ]);
            // echo $dataDetailsimage->name;
            // die;
            
            $update_arr['product_name'] = $request->product_extra_title;
            $update_arr['price'] = $request->product_extra_price;
            $update_arr['slug'] = str_replace(' ', '-', strtolower($request->product_extra_title));
            $update_arr['description'] = $request->product_extra_title;
            $update_arr['alt_key'] = $request->product_extra_title;
            $update_arr['meta_title'] = $request->product_extra_title;
            $update_arr['meta_description'] = $request->product_extra_title;
           
    
            $imagefile=$request->file("image");
            if (isset($imagefile)){
                unlink(public_path() . '/uploads/addon/' . $dataDetailsimage->name);
                $imageName = 'AD-.'.strtotime(now()).rand(11111,99999).'.'.$imagefile->getClientOriginalExtension();
                $imagefile->move(public_path() . '/uploads/addon/', $imageName);
                $update_image['name'] = $imageName;
                GBGProductImage::where(['product_id' => $id])->update($update_image);
            }
            
        
            if(GBGAddon::where(['id' => $request->formid])->update($update_arr)){
                   
                $request->session()->flash('alert-success', 'Addon successfully updated.');
                return redirect()->route('admin.gbg.addon.list');
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back()->with($request->except(['_method', '_token']));
            }
        }

        return view('admin.GBG.addon.edit', ['dataDetails' => $dataDetails, 'dataDetailsimage' => $dataDetailsimage, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    public function delete($id = null, Request $request)
    {
        if($this->checkPermission('addon','list') == false && $this->checkSuperPermission('addon','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        if($id == null){
            return redirect()->route('admin.home');
        }
        $id = base64_decode($id);

        $product_image = GBGProductImage::where('product_id',$id)->get('name');
        foreach($product_image as $key => $pimage){
          if($pimage->name != ''){
            unlink(public_path() . '/uploads/addon/'.$pimage->name);
          }
          GBGProductImage::where(['product_id' => $id])->delete();
        }
        //$event_details = HomeNews::find($id);

        //@unlink(public_path() . '/uploaded/event_images/' . $event_details->eimg);

        if( GBGAddon::where(['id' => $id])->delete()){
            $request->session()->flash('alert-success', 'Addon deleted successfully.');
            return redirect()->back();
        }else{
            $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
            return redirect()->back();
        }
    }

    public function status(Request $request)
    {
        if($request->id == null || $request->status == null){
            return redirect()->route('admin.dashboard');
        }
        $id = base64_decode($request->id);
        //$block = 'N';
        //$blockText = 'blocked';
        switch($request->status){
            case 'N':
                $block = 'Y';
                $blockText = 'Block';
                break;
            case 'Y':
                $block = 'N';
                $blockText = 'Unblock';
                break;
            default:
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back();
        }
        if(GBGAddon::where(['id' => $id])->update(['is_block' => $block])){
            //$request->session()->flash('alert-success', 'Category successfully '.$blockText);
            //return redirect()->back();
            $prompt = array('status' => 1, 'id' => $id, 'event' => $blockText);

            echo json_encode($prompt);
        }else{
            $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
            return redirect()->back();
        }
    }
    
    public function deleteimage($id = null, Request $request){
        if($this->checkPermission('addon','list') == false && $this->checkSuperPermission('addon','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }
        
        $websiteShortCode = 'gbg';

        if($id == null){
            return redirect()->route('admin.home');
        }
        $id = base64_decode($id);
        $dataDetails  = GBGProductImage::find($id);
        unlink(public_path() . '/uploads/addon/'.$dataDetails->name);

        if(GBGProductImage::where(['id' => $id])->delete()){
            $request->session()->flash('alert-success', 'Addon image deleted successfully.');
            return redirect()->back();
        }else{
            $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
            return redirect()->back();
        }
        
    }

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