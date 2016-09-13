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
class HotelmanagesystemController extends Controller
{

    public function login(Request $request){
           

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
    public function showBookRoomPage(){
        View::addExtension('html','php');
        return view('binguan/reserveInfMa/blank');
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

     public function showRoomin(){
         return view('binguan/addRoomStd/blank');

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
          
           return '办理成功';
           
           
    }

    //4.添加vip信息 接口
    public function addvip(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'id_number'=>'required|min:18|max:18',
            'telephone'=>'required|min:11|max:11',
            'sex'=>'required',
            'type'=>'required',
          ]);
           $guest=new Guest;
           $guest->name=$request->name;
           $guest->telephone=$request->telephone;
           $guest->id_number=$request->id_number;
           $guest->sex=$requst->sex;
           $guest->type=$request->type;
           $guest->vip=1;
           $guest->save();
           
           return "1";
       }
       //5.查询所有客房
       public function checkRoom(){
           
           $result=DB::table('room')->orderBy('status','desc')->get();
           return json_encode($result);
       }
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
              'room_id'=>'required|unique:room',
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
      //10 退房之后结算 接口
      public function dobalance(Request $request){
            //need order_number
            $order_number=$request->order_number;
            $res=App\Roomguest::where('order_number',$order_number)->get();
            return json_encode($res);
      }
      //11 查询客户是否为VIP 接口
      public function isVip(Request $request){
          // need id_number
          $id_number=$request->id_number;
          $res=App/Guest::where('id_number',$id_number)->get();
          $ans=$res->vip;
          if($ans=='1'){
              return '1';
          }elseif($ans=='0') return '0';

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

}