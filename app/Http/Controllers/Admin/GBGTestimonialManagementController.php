<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GBGCms;
use App\Models\GBGTestimonialManagement;
use Illuminate\Http\Request;
use Auth;


class GBGTestimonialManagementController extends CommonController
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

        if($this->checkPermission('testimonialmanagement','list') == false && $this->checkSuperPermission('testimonialmanagement','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $where = $orWhere = [];

        $result = GBGTestimonialManagement::orderBy('created_at', 'desc')->get();

        return view('admin.GBG.testimonialmanagement.list', ['result' => $result, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    
    public function add(Request $request){

        if($this->checkPermission('testimonialmanagement','list') == false && $this->checkSuperPermission('testimonialmanagement','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $obj = new GBGTestimonialManagement;

        if($request->isMethod('POST')){
            //dd($request);
            $request->validate([
                'name'=>'required',
                'send_place'=>'required',
                'from_place'=>'required',
                'content'=>'required',
                'rating' =>'required'
            ]);

            if($testimonal = $obj->create([
                    'name' => $request->name, 
                    'send_place' => $request->send_place, 
                    'place' => $request->from_place,
                    'content' => $request->content, 
                    'rating' => $request->rating,
                    'created_by' => Auth::guard('admin')->user()->id
                ]))
            {
                $request->session()->flash('alert-success', 'testimonialmanagement successfully added.');
                return redirect()->route('admin.gbg.testimonialmanagement.list');
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back()->with($request->except(['_method', '_token']));
            }
            
        }

        return view('admin.gbg.testimonialmanagement.add', ['request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    public function edit($id = null, Request $request){

        if($this->checkPermission('testimonialmanagement','list') == false && $this->checkSuperPermission('testimonialmanagement','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        $websiteShortCode = 'gbg';

        $id = base64_decode($id);
		$dataDetails  = GBGTestimonialManagement::where('id',$id)->first();

        if($request->isMethod('POST')){
            $request->validate([
                'name'=>'required',
                'send_place'=>'required',
                'from_place'=>'required',
                'content'=>'required',
                'rating' =>'required'
            ]);
            
            $update_arr['name'] = $request->name;
            $update_arr['send_place'] = $request->send_place;
            $update_arr['place'] = $request->from_place;
            $update_arr['content'] = $request->content;
            $update_arr['rating'] = $request->rating;

            if(GBGTestimonialManagement::where(['id' => $request->formid])->update($update_arr)){
                   
                $request->session()->flash('alert-success', 'TestimonialManagement successfully updated.');
                return redirect()->route('admin.gbg.testimonialmanagement.list');
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back()->with($request->except(['_method', '_token']));
            }
        }

        return view('admin.GBG.testimonialmanagement.edit', ['dataDetails' => $dataDetails, 'request' => $request, 'websiteShortCode' => $websiteShortCode]);
    }

    public function delete($id = null, Request $request)
    {
        if($this->checkPermission('testimonialmanagement','list') == false && $this->checkSuperPermission('testimonialmanagement','list') == false ){
            $request->session()->flash('alert-danger', "You don't have permissions to access this page.");
            return redirect()->route('admin.home');
        }

        if($id == null){
            return redirect()->route('admin.home');
        }
        $id = base64_decode($id);

        //$event_details = HomeNews::find($id);

        //@unlink(public_path() . '/uploaded/event_images/' . $event_details->eimg);

        if( GBGTestimonialManagement::where(['id' => $id])->delete()){
            $request->session()->flash('alert-success', 'TestimonialManagement deleted successfully.');
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
        if(GBGTestimonialManagement::where(['id' => $id])->update(['is_block' => $block])){
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