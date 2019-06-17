<?php

namespace App\Http\Controllers;

use App\User;
use App\Outlet;
use App\Memo;
use App\System\Data\Outlet as OutletData;
use App\Notifications\OutletMemo;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class MemoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function all() {
      if (!auth()->user()->is_admin) {
          abort(404, 'Only privileged users are allowed.');
      }
      $memos = Memo::where('user_id','=',auth()->user()->id)->orderBy('datetime', 'desc')->get()->toArray();

      return view('admin.memos',['memos' => $memos]);

    }

    public function getMemo($memoId) {
      $memo = Memo::where('id','=',$memoId)->get()->toArray();
      $notifid = \request()->input('notifid');

      $notification = auth()->user()->notifications()->where('id',$notifid)->first();
      if ($notification)
      {
          $notification->markAsRead();
      }

      $memoData = [
        'announcer' => '',
        'message' => '',
        'date' => ''
      ];
      if (count($memo)) {
        $announcer = User::where('id', $memo[0]['user_id'])->get(['name'])->toArray();
        if (count($announcer)) {
          $memoData['announcer'] = $announcer[0]['name'];
        }
        $memoData['message'] = $memo[0]['message'];
        $memoData['date'] = Carbon::parse($memo[0]['datetime'])->toDayDateTimeString();
      }

      return response()->json($memoData);
    }


    /**
     * Show memo creation form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }

        return view('admin.memo');
    }

    public function postCreate() {
      if (!auth()->user()->is_admin) {
          abort(404, 'Only privileged users are allowed.');
      }
      $id = auth()->user()->id;
      $announcer = auth()->user();
      $outlets = Outlet::where('user_id', '=', $id)->get();

      $memo = new Memo();

      $memo->user_id = $id;
      $memo->message = Input::get('message');
      $memo->datetime = Carbon::now();
      $memo->save();

      foreach($outlets as $outlet) {
        $oData = new OutletData($outlet);
        foreach ($oData->assignedTellers() as $teller) {
          $tUser = User::find($teller->id());
          $tUser->notify(new OutletMemo(['message' => $memo->message, 'id' => $memo->id, 'datetime' => $memo->datetime], $announcer));
        }
      }
      Session::flash('memo-success',
          "Memo successfully created.");
      return redirect()->route('all-memos');
    }

}
