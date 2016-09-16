<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Balance;
use App\Bookroom;
use App\Room;
use App\Roomguest;
use App\Guest;
use View;
use DB;
use Session;
class HotelmanagesystemController extends Controller
{

    public function __construct(Request $request){
        //$this->middleware('auth');
        if(Session::has('SESSIONID')){
            return Session::get('SESSIONID');
        }else{
           return "2"; 
        }
    }
    public function checkLogin(Request $request){
        if(Session::has('SESSIONID')){
            $session=Session::get('SESSIONID');
            $permession=Session::get('PERMESSION');
            $data=array('session'=>$session,"permession"=>$permession);
            return json_encode($data);
        }else{
           return "2"; 
        }

    }
    public function login(Request $request){
        $this->validate($request,[
            'username'=>'required',
            'password'=>'required',
        ]);
        $res=User::where('user_id',$request->username)->where('password',$request->password)->get();
         if($res->isEmpty()){
             return ;
         }else{
             $perm=$res->first();
             Session::put('SESSIONID',$request->username);
             Session::put('PERMESSION',$perm->permession);
          //   Session::save();
            return $request->username;
             
         }

    }

    public function logout(Request $request){

         Session::flush();

         return  redirect("http://localhost/hotelms/public/binguan/login/login.html");
    }
    //1,添加订房信息 接口
    public function bookroomin(Request $request)
    {
          $this->validate($request,[
              'name'=>'required|max:6',
              'telephone'=>'required|max:15',
              'booktime'=>'required',
              'room_id'=>'required|string',
         ]);
         $bookroom=new Bookroom;
         $bookroom->name=$request->name;
         $bookroom->telephone=$request->telephone;
         $bookroom->booktime=$request->booktime;
         $bookroom->room_id=$request->room_id;
         $bookroom->save();
        DB::update('update room set status=1 where room_id =?',[$bookroom->room_id]);

         return "预定成功";
      
    } 

    //2.添加客房标准 接口
    public function roomsave(Request $request){
          $this->validate($request,[
              'room_id'=>'required|unique:room',
              'type'=>'required',
              'location'=>'required|string',
              'price'=>'required',
              'remark'=>'required|max:255|string',
          ]);
             $newroom=new Room;
             $newroom->room_id=$request->room_id;
             $newroom->type=$request->type;
             $newroom->location=$request->location;
             $newroom->price=$request->price;
             $newroom->remark=$request->remark;
             $newroom->status='0';
             $newroom->save();

           return '添加成功！';
    } 

    //3.添加客人入住信息 接口
    public function addguestin(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'id_number'=>'required|min:18|max:18',
            'telephone'=>'required|min:11|max:15',
            'sex'=>'required',
            'type'=>'required',
            'room_id'=>'required',
            'in_time'=>'required',
            'out_time'=>'required',
            'nights'=>'required',
            'deposit'=>'required',
           ]);
          
           
            $user=Guest::where('id_number',$request->id_number)->get();
            if($user->isEmpty()){
                $guest=new Guest;
                $guest->name=$request->name;
                $guest->telephone=$request->telephone;
                $guest->id_number=$request->id_number;
                $guest->sex=$request->sex;
                $guest->type=$request->type;
                $guest->vip='0';
                $guest->save(); 
            }
           
           $roomguest1=Roomguest::where('room_id',$request->room_id)->where('status',1)->get();
           if($roomguest1->isEmpty()){
               $roomguest=new Roomguest;
               $roomguest->room_id=$request->room_id;
               $roomguest->id_number=$request->id_number;
               $roomguest->in_time=$request->in_time;
               $roomguest->out_time=$request->out_time;
               $roomguest->nights=$request->nights;
               $roomguest->deposit=$request->deposit;
               $roomguest->status='1';
               $roomguest->order_number=date('YmdHis');
               $roomguest->save();
           }else{
                return "已经录入过啦！";
           }
          
           
           DB::update('update room set status=1 where room_id =?',[$roomguest->room_id]);
           //删除bookroom表单里的数据
           DB::delete('delete from bookroom where telephone=?',[$request->telephone]);
           return '办理成功';
           
           
    }

    //4.添加vip信息 接口
    public function addvip(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'id_number'=>'required|min:18|max:18',
            'telephone'=>'required|min:11|max:15',
            'sex'=>'required',
           
          ]);
          $vip=Guest::where('id_number',$request->id_number);
          if($vip->get()->isEmpty()){
           $guest=new Guest;
           $guest->name=$request->name;
           $guest->telephone=$request->telephone;
           $guest->id_number=$request->id_number;
           $guest->sex=$request->sex;
           $guest->type='散客';
           $guest->vip=1;
           $guest->save();
           return  "VIP添加成功！";
          }else{
               $re = $vip->first();
               $re->vip='1';
               $re->save();
              return "VIP办理成功！";

          }
   
       }
       //5.查询所有客房
       public function checkRoom(){
           
           $result=DB::table('room')->orderBy('status','desc')->get();
           return json_encode($result);
       }
       //获得客房状态
        public function getRoomStatus(){
            $result=DB::select('select room_id,type,status from room where status=?',['0']);
            return json_encode($result);
        }
       //6 查询客户是否已经预定
       public function checkbook(Request $request){
            $telephone=$request->telephone;
            $result=DB::select('select * from bookroom where telephone = ?',[$telephone]);
            return json_encode($result);
      }
      //7 修改客房信息 
      public function changeRoomMes(Request $request){
          $do=$request->oper;
          if($do=='edit'){
             $this->validate($request,[
              'room_id'=>'required',
              'type'=>'required',
              'location'=>'required|string',
              'price'=>'required',
              'remark'=>'required|max:255|string',
             ]);
             $id=$request->id;
             $room_id=$request->room_id;
             $type=$request->type;
             $location=$request->location;
             $price=$request->price;
             $remark=$request->remark;
             $result=DB::update('update room set room_id=?,type=?,location=?,price=?,remark=? where id=?',
                                      [$room_id,$type,$location,$price,$remark,$id]);
             
            if($result) return "修改成功！";
         }elseif($do=='del'){
            $id=$request->id;
            DB::table('room')->whereIn('id',[$id])->delete();
            return "删除成功！";

        
        }
      }
      //8 删除客房信息
      public function delRoom(Request $request){
          $id=$request->id;
          $result=DB::delete('delete from room where id =?  ',[$id]);
          if($result) return "1";
      }
      //9 客户换房 接口
      public function changeRoom(Request $request){
          $id=$request->id;
          $newRoomid=$request->room_id;
          $result=DB::update('update roomguest set room_id=? where id=?',[$newRoomid,$id]);
          if($result) return "1";

      }
      //10 办理结算时获得订单信息 接口
      public function getBalRoomGuest(Request $request){
            //need order_number
            $room_id=$request->room_id;
            $res=DB::table('roomguest')
                         ->join('room','roomguest.room_id','=','room.room_id')
                         ->join('guest','roomguest.id_number','=','guest.id_number')
                         ->select('roomguest.*','guest.name','guest.vip','guest.type','guest.telephone','guest.sex' ,'room.type as room_type','room.location','room.price','room.remark')
                         ->where('roomguest.status','1') ->where('roomguest.room_id',$room_id)   
                         ->get();
           
            return json_encode($res);
      }
      public function doBalance(Request $request){
               $this->validate($request,[
                   'order_number'=>'required',
                   'type'=>'required',
                   'deposit'=>'required|integer',
                   'balance1'=>'required|integer',
                   'balance2'=>'required|integer',
               ]);
                $bal=Balance::where('order_number',$request->order_number); 
               if($bal->get()->isEmpty()){
                    $balance=new Balance;
                    $balance->order_number=$request->order_number;
                    $balance->type=$request->type;
                    $balance->deposit=$request->deposit;
                    $balance->balance1=$request->balance1;
                    $balance->balance2=$request->balance2;
                    $balance->time=date("Y-m-d H:i:s");
                    $balance->save();

                    $status=Roomguest::where('order_number',$request->order_number)->get();
                    $star=$status->first();
                    $star->status='0';
                    $star->save();
//修改房间状态
                   // $status2=Room::where('room_id',$request);
                    
                    return "结算成功！";
               }else {
                   return "重复结算！";
                   }
              
                           
      }
      //11 查询客户是否为VIP 接口
      public function isVip(Request $request){
          // need id_number
          $id_number=$request->id_number;
          $res=Guest::where('id_number',$id_number)->get();
          $ans=$res->vip;
          if($ans=='1'){
              return '1';
          }elseif($ans=='0') return '0';

      }
      public function getGuestMs(Request $request){
         $id_number=$request->id_number;
          $res=Guest::where('id_number',$id_number)->where('vip','0')->get();
          return json_encode($res);
      }
      public function alterroomguest(Request $request){
          $do=$request->oper;
          if($do=='edit'){
                $this->validate($request,[
                    'name'=>'required',
                    'id_number'=>'required|min:18|max:18',
                    'room_id'=>'required',
                    'in_time'=>'required',
                    'out_time'=>'required',
                    'deposit'=>'required',
             ]);
           //只允许修改房间编号 入住时间 退房时间
             $id=$request->id;
             $alter=Roomguest::find($id);
             $oldroomid=$alter->room_id;

             $alter->room_id=$request->room_id;
             $alter->in_time=$request->in_time;
             $alter->out_time=$request->out_time;
             $alter->save();
             $alterroom=Room::where('room_id',$request->room_id)->first();
             $alterroom->status='1';
             $alterroom->save();
             
             $alterroom1=Room::where('room_id',$oldroomid)->first();
             $alterroom1->status='0';
             $alterroom1->save();
             
            return '修改成功！';
          }elseif($do=='del'){
            DB::table('roomguest')->where('id',$id)->delete();
            return '删除成功！';
          }
      }
      //12 所有订单信息查询
      public function allroomguest(){
          $res=DB::table('roomguest')
                   ->join('room','roomguest.room_id','=','room.room_id')
                   ->join('guest','roomguest.id_number','=','guest.id_number')
                   ->select('roomguest.*','room.type','guest.name')
                   ->orderBy('order_number','desc')
                   ->get();
          return json_encode($res); 
      }

 
    public function create(Request $request){
        $this->validate($request,[
            'user_id'=>'required',
            'password'=>'required|min:6',
            
        ]);
        $find=User::where('user_id',$request->user_id)->get();
        if($find->isEmpty()){
           $user=new User;
           $user->user_id=$request->user_id;
           $user->password=$request->password;
           $user->permession='2';
           $user->save();
           return "1";
        }else return "2";
     

    }

}