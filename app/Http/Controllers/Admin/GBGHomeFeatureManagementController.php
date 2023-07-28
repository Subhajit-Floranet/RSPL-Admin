<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GBGCms;
use App\Models\GBGHomeFeatureManagement;
use App\Models\GBGHomePageProduct;
use App\Models\GBGProductCategory;
use App\Models\GBGCategory;
use App\Models\GBGProduct;
use Illuminate\Http\Request;
use Auth;
use DB;

class GBGHomeFeatureManagementController extends CommonController
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

        if($this->checkPermission('homefeaturemanagement','list') == false && $this->checkSuperPermission('homefeaturemanagement','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $where = $orWhere = [];

        $category = GBGCategory::all();

        $result = GBGHomeFeatureManagement::orderBy('created_at', 'desc')->get();

        return view('admin.GBG.homefeaturemanagement.list', ['result' => $result, 'category' => $category, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    
    public function add(Request $request){

        if($this->checkPermission('homefeaturemanagement','list') == false && $this->checkSuperPermission('homefeaturemanagement','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $obj = new GBGHomeFeatureManagement;
        $category = GBGCategory::all();

        if($request->isMethod('POST')){
            //dd($request);
            $request->validate([
                'title'=>'required',
                'description'=>'required',
                'category'=>'required',
                'data_limit'=>'required'
            ]);
            
            $homepage = $obj->create([
                    'title' => $request->title, 
                    'description' => $request->description, 
                    'slug' => $request->slug, 
                    'category_id' => $request->category, 
                    'data_limit' => $request->data_limit,
                    'sort' => 10
            ]);
            if($homepage)
            {
                if((isset($request->product_id) && count($request->product_id)>0) && $homepage){
                    foreach($request->product_id as $pid){
                        $home_page_categories['cat_id'] = $request->category;
                        $home_page_categories['product_id'] = $pid;
                       // $home_page_categories['sort'] = $a;
                        GBGHomePageProduct::create($home_page_categories);
                    }
                }
                $request->session()->flash('alert-success', 'HomePageFeatureProduct successfully added.');
                return redirect()->route('admin.gbg.homefeaturemanagement.list');
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back()->with($request->except(['_method', '_token']));
            }
            
        }

        return view('admin.gbg.homefeaturemanagement.add', ['request' => $request, 'category' => $category, 'websiteShortCode' => $websiteShortCode]);
    }

    public function categoryproduct(Request $request){
        if($request->catid){
            $html = '';
            $catid = $request->catid;

            $getproductidfromCategory = GBGProductCategory::where('category_id',$catid)->get();
          

            foreach($getproductidfromCategory as $key => $getproduct){
                $getProducts = GBGProduct::wherein('id',[$getproduct->product_id])->get();
                
                foreach($getProducts as $key => $product){
                    $html .= '<option value="'.$product->id.'">'.$product->product_name.'</option>';
                }
            }
        return json_encode(['type' => 'json', 'status' => 'success', 'data' => $html]);
        }else{
            return json_encode(['type' => 'json', 'status' => 'error']);
        }
    }

    public function edit($id = null, Request $request){

        if($this->checkPermission('homefeaturemanagement','list') == false && $this->checkSuperPermission('homefeaturemanagement','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $html = '';

        $id = base64_decode($id);
		$dataDetails  = GBGHomeFeatureManagement::where('id',$id)->first();

        $product_selected = GBGHomePageProduct::where(['cat_id' => $dataDetails->category_id])->pluck('product_id');
        $product_selected = $product_selected->toarray();

        $category = GBGCategory::all();


        if($request->isMethod('POST')){
            //dd($request);
            $request->validate([
                'title'=>'required',
                'description'=>'required',
                'category'=>'required',
                'data_limit'=>'required'
            ]);

            $category_id = GBGHomeFeatureManagement::where('id',$id)->get(['category_id']);
            foreach($category_id as $key => $cat_id){
                $deletecategoryproduct = GBGHomePageProduct::where(['cat_id' => $cat_id->category_id])->delete();
            }
            

            $update_arr['title'] = $request->title;
            $update_arr['description'] = $request->description;
            $update_arr['slug'] = $request->slug;
            $update_arr['category_id'] = $request->category;
            $update_arr['data_limit'] = $request->data_limit;

            if((isset($request->product_id) && count($request->product_id)>0) && $update_arr){
                foreach($request->product_id as $pid){
                    $home_page_categories['cat_id'] = $request->category;
                    $home_page_categories['product_id'] = $pid;
                   // $home_page_categories['sort'] = $a;
                    GBGHomePageProduct::create($home_page_categories);
                }
            }
           

            
            if(GBGHomeFeatureManagement::where(['id' => $request->formid])->update($update_arr)){
                   
                $request->session()->flash('alert-success', 'GBGHomeFeatureManagement successfully updated.');

                return redirect()->route('admin.gbg.homefeaturemanagement.list');
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back()->with($request->except(['_method', '_token']));
            }

        }

        return view('admin.GBG.homefeaturemanagement.edit', ['product_selected' => $product_selected, 'dataDetails' => $dataDetails, 'category' => $category, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    public function delete($id = null, Request $request)
    {
        if($this->checkPermission('homefeaturemanagement','list') == false && $this->checkSuperPermission('homefeaturemanagement','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        if($id == null){
            return redirect()->route('admin.home');
        }
        $id = base64_decode($id);

        //$event_details = HomeNews::find($id);

        //@unlink(public_path() . '/uploaded/event_images/' . $event_details->eimg);

        $Homefeature = GBGHomeFeatureManagement::select('category_id')->where('id',$id)->get();
        
        foreach($Homefeature as $key => $homefeature){
            GBGHomePageProduct::where('cat_id',$homefeature->category_id)->delete();
        }
      

        if( GBGHomeFeatureManagement::where(['id' => $id])->delete()){
            $request->session()->flash('alert-success', 'Homefeaturemanagement deleted successfully.');
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
        // echo $id;
        // die;
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
        if(GBGHomeFeatureManagement::where(['id' => $id])->update(['is_block' => $block])){
            //$request->session()->flash('alert-success', 'Category successfully '.$blockText);
            //return redirect()->back();
            $prompt = array('status' => 1, 'id' => $id, 'event' => $blockText);
            echo json_encode($prompt);
        }else{
            $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
            return redirect()->back();
        }
    }
}