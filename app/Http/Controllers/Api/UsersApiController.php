<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\System\Data\User;
use App\System\Data\Timeslot;
use App\System\Utils\TimeslotUtils;
use App\System\Services\UserService;
use App\Outlet;
use App\User as UserModel;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;

class UsersApiController  extends Controller {

    public function index(){
        // TODO
    }


    public function getMemos(Request $request) {

        $msgs = [
            'unread' => [],
            'read' => []
        ];
        $err = "";
        $unreadCount = 0;
        $fetchedUnreadCount = 0;
        $isSuccess = true;

        try {
            $start = $request->get('datefrom');
            $end = $request->get('dateto');
            $limit = $request->get('limit');
            if (!is_int($limit)) {
                $limit = 0;
            }
            
            $user = auth()->user();
            if (count($user->unreadNotifications)>0) {
                $unreadCount = count($user->unreadNotifications);
            }
            $compareDate = false;
            if (TimeslotUtils::validateDate($start)  && TimeslotUtils::validateDate($end)) {
                $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $start . " 00:00:00");
                $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $end . " 00:00:00")->addDays(1);
                $compareDate = true;
            }


            $currentCount = 0;
            foreach(auth()->user()->notifications->sortByDesc('created_at') as $notification) {
                $announcer = $notification->data['announcer']['name'];
                $message = $notification->data['memo']['message'];
                $date = $notification->data['memo']['datetime']['date'];
                $notifId = $notification->id;
                $isUnread = $notification->unread();
                $key = $isUnread ? "unread" : "read";
                $allowAdd = true;
                $continue = true;
                
                if ($compareDate) {
                    $cleanDate = explode(".", $date);
                    if (count($cleanDate)) {
                        $cleanDate = $cleanDate[0];
                    }
                    $createTime = Carbon::createFromFormat('Y-m-d H:i:s', $cleanDate, env('APP_TIMEZONE'));  
                    $allowAdd = $startTime->lte($createTime) && $endTime->gt($createTime);
                }
                
                if ($allowAdd) {
                    $msgs[$key][] = [
                        "id" => $notifId,
                        "date" => $date,
                        "announcer" => $announcer,
                        "message" => $message,
                        "is_unread" => $isUnread
                    ];

                    if ($isUnread) {
                        $fetchedUnreadCount++;
                    }
    
                    if ($limit) {
                        $currentCount++;
                        $continue = $currentCount<$limit;
                    }
                }

                if (!$continue) {
                    break;
                }
            }
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $isSuccess = false;
        }

        $output = [
            "messages" => $msgs,
            "unread_count" => $unreadCount,
            "unread_count_fetched" => $fetchedUnreadCount,
            "error" => $err,
            "success" => $isSuccess
        ];

        return response()->json($output);
        
    }

    public function getMemo(Request $request) {
        $err = "";
        $isSuccess = true;
        $announcer = "";
        $message = "";
        $datetime = "";
        $isUnread = false;

        try {
            $id = $request->get('id');
            $mark = $request->get('mark_read');

            if(!is_bool($mark)) {
                $mark = false;
            }

            $memo = auth()->user()->notifications->where('id',$id)->toArray();

            if (count($memo)) {
                $announcer = $memo[0]['data']['announcer']['name'];
                $message = $memo[0]['data']['memo']['message'];
                $datetime = $memo[0]['created_at'];
                $isUnread = $memo[0]['read_at'] == NULL;
                if ($mark) {
                    $notification = auth()->user()->notifications()->where('id',$id)->first();
                    if ($notification) {
                        $notification->markAsRead();
                        $isUnread = false;
                    }
                }
            } else {
                throw new \Exception("No memo found");
            }

        } catch (\Exception $e) {
            $err = $e->getMessage();
            $isSuccess = false;
        }
        $output = [
            "announcer" => $announcer,
            "message" => $message,
            "datetime" => $datetime,
            "is_unread" => $isUnread,
            "error" => $err,
            "success" => $isSuccess
        ];
        return response()->json($output);
    }

    public function setUserBettingStatus(Request $request) {
        
        $err = "";
        $isSuccess = true;
        
        try {
            $enable = $request->get('enable');
            if(!is_bool($enable)) {
                throw new \Exception('Invalid enable value. Must be a boolean');
            }
            $service = new UserService();
            $service->setAllowBetting($enable, auth()->user());
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $isSuccess = false;
        }
        $output = [
            "error" => $err,
            "success" => $isSuccess
        ];
        return response()->json($output);
    }
}