<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\post;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Exists;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function home(){
        $data= post::all();
        return view('welcome', compact('data'));
    }

    public function storeImage(Request $request){

        if($request->file('image')){
            $file= $request->file('image');
            $filename= $file->getClientOriginalName();
            $file-> move(public_path('/storage/users'), $filename);
            $filename;
        }
        return redirect('/register')->with('filename',$filename);
    }
    public function viewServices(){
        $data= post::all();
        return view('services', compact('data'));

    }
    // public function callImage(){
    //     $images = DB::table('posts')
    //             ->select(DB::raw("
    //             image")
    //             );
    //     if(!(public_path('public/Img'), $filename)){
    //         $file= $request->file('image');
    //         $filename= $file->getClientOriginalName();
    //         $file-> move(public_path('public/Img'), $filename);
    //         $filename;
    //     }
    //     return redirect('/register')->with('filename',$filename);
    // }
    public function viewvolunteer($service_id){
              $user_id=Auth::user()->id;
              return redirect('/volunteer/'.$service_id.'/user/'.$user_id);
    }
    public function volunteer($service_id,$user_id){
        DB::UPDATE('UPDATE users SET hold=1, services=? WHERE id=?',[$service_id,$user_id]);
        // $edit= User::find($user_id);
        // $edit->hold = 1;
        // $edit-> service = $service_id;
        $posts= post::find($service_id);
        // echo $posts['title'];
        $users= User::all();
        // dd($user_id);
        $data = array(
            'name' => $users[$user_id-1]['name'],
            'user_id' => $users[$user_id-1]['id'],
            'email' => $users[$user_id-1]['email'],
            'phone' => $users[$user_id-1]['phone'],
            'title' => $posts['title'],
            'service_id' => $posts['id']
        );
        mail::send('mail.volunteer', $data, function($message) use($data){
        $message->to('ayaalsawa279@gmail.com');
        $message->from($data['email']);
        $message->subject('volunteering');});
        return redirect('/services')->with('message','Your Application sent successfully,please wait for admin approval');
    }
    public function edituser(Request $request,$id){
        $file= $request->file('image');
        $path= "";
        if (!empty($file)) {
            $filename= $file->getClientOriginalName();
            $file-> move(public_path('/storage/users/June2022'), $filename);
            $path='users/June2022/'.$filename;
        }
        $path;
        // dd($path);
        $user= User::find($id);
        if(!empty($file)){
            $user->avatar=$path;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->city = $request->input('city');
            // $user->password = Hash::make($request->input('password'));
            $user->update();
            return redirect('/home')->with('status','data edited Successfully');
        }
        else{
            // $user->avatar='/users/default.png';
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->city = $request->input('city');
            // $user->password = $request->input('password');
            $user->update();
            return redirect('/home')->with('status','data edited Successfully');
        }

    }

    public function news(Request $request){
        $data=[
            'name' => 'Sir/Madam',
            'email' => $request->input('subscribe'),
        ];
        mail::send('mail.subscribe', $data, function($message) use($data){
            $message->to($data['email']);
            $message->from('greenland@support.com');
            $message->subject('Subscribe to Newsletter');});
            return redirect('/')->with('message','You subscribed to our newsletter successfully');
    }

    //contact function
    public function contact(Request $request){
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'subject' => $request->input('name'),
            'messages' => $request->input('message'),
        ];
        mail::send('mail.contact', $data,function($message) use($data){
            $message->to('ayaalsawa279@gmail.com');
            $message->from($data['email']);
            $message->subject($data['subject']);
        });
        // dd($data);
        return redirect('/contact')->with('message', 'Thank you for contacting us, we will respond to you soon');
    }
    public function render(){
        return view('approve');
    }

    public function aprrove(Request $request){
        $callData = DB::select('SELECT * from users where hold = 1');
        $user=$request->input('user_id');
        $service=$request->input('service_id');
        // dd($service);

        if (empty($calldata)) {
            foreach($callData as $data){
                // dd($data);
                if($user == $data->id && $service == $data->services){
                    $id=$data->id;
                    DB::insert('INSERT INTO user_services (user_id,service_id) VALUES (?,?)',[$id,$service]);
                    $rollback= User::find($user);
                    DB::update('UPDATE users SET hold = 0,services="null" WHERE id = ?',[$id]);
                    $Data=[
                        'name' => $data->name,
                        'email' => $data->email,
                    ];
                    mail::send('mail.approved', $Data, function($message) use($Data){
                        $message->to($Data['email']);
                        $message->from('greenland@support.com');
                        $message->subject('Approved!');
                    });
                    return redirect('/admin/approve')->with('message','Approved Successfully');
                }
                else {
                    // return redirect('/admin/approve')->with('message','Wrong Data input');
                }
            }
        }
        else{
            return redirect('/admin/approve')->with('message','No Applications with these values found');
        }
    }

    public function dashboard(){
        return view('dashboard');
    }
}
