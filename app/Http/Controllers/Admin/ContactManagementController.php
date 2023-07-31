<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GBGCms;
use App\Models\GBGContact;
use App\Models\GBSContact;
<<<<<<< HEAD
=======
use App\Models\GBSContactConversation;
use App\Models\GBGContactConversation;
>>>>>>> 9e0afbddd72e1ec9dc02e9ba5d4e145cae1dc05b
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;


class ContactManagementController extends CommonController
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

    
    public function allcontact(Request $request){

        if($request->isMethod('POST')){
           $val = $request->contact;
           if($val == 1){
            $gbg = DB::table('gbg.contacts')->select('name','id','email','query_related','ticket_id','sitename','is_block','created_at');
            $gbs = DB::table('gbs.contacts')->select('name','id','email','query_related','ticket_id','sitename','is_block','created_at');
    
            $res = $gbg->unionAll($gbs)->orderBy('created_at', 'desc')->get();
            return view('admin.Contact.allcontactlist',["result" => $res, 'request' => $request]);
           }
           elseif($val==2){
            $gbg = GBGContact::all();
            return view('admin.Contact.gbgcontactlist',["result" => $gbg, 'request' => $request]);
           }
           else{
            $gbs = GBSContact::all();
            return view('admin.Contact.gbscontactlist',["result" => $gbs, 'request' => $request]);
           }
        }
    }

    public function contact(Request $request){
        return view('admin.Contact.list');
    }
    
    public function status(Request $request)
    {
        if($request->id == null || $request->status == null){
            return redirect()->route('admin.dashboard');
        }
        $id = base64_decode($request->id);
        $sitename = $request->sitename;
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
        if($sitename == 'gbg'){
            if(GBGContact::where(['id' => $id])->update(['is_block' => $block])){
                //$request->session()->flash('alert-success', 'Category successfully '.$blockText);
                //return redirect()->back();
                $prompt = array('status' => 1, 'id' => $id, 'event' => $blockText);
    
                echo json_encode($prompt);
            }else{
                $request->session()->flash('alert-danger', 'Sorry! There was an unexpected error. Try again!');
                return redirect()->back();
            }
        }
        else{
            if(GBSContact::where(['id' => $id])->update(['is_block' => $block])){
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
<<<<<<< HEAD
=======

    public function edit(Request $request){
        $id = $request->id;
        $sitename = $request->sitename;
        if($sitename == "gbg"){
            $result1 = GBGContact::where(['id' => $id])->get();
            $result2 = GBGContactConversation::where(['contact_id' => $id])->first();
            foreach($result1 as $result){
                return view('admin.Contact.allcontactedit',['result' => $result, 'result2' => $result2, 'request' => $request]);
            }
           
        }
        else{
            $result1 = GBSContact::where(['id' => $id])->get();
            $result2 = GBGContactConversation::where(['contact_id' => $id])->get();
            foreach($result1 as $result){
                return view('admin.Contact.allcontactedit',['result' => $result, 'result2' => $result2, 'request' => $request]);
            }
        }
        
        
    }
>>>>>>> 9e0afbddd72e1ec9dc02e9ba5d4e145cae1dc05b
}