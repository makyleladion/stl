<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Nexmo\Laravel\Facade\Nexmo;
use App\System\Services\SmsService;
use Nexmo\Message\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\MobileNumber;
use Nexmo\Client\Exception\Exception;
use App\System\Utils\GeneratorUtils;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }

        return view('admin.settings');
    }

    public function smsNotification()
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        $mobileNumbers = MobileNumber::where('role', \App\System\Data\MobileNumber::ROLE_ADMIN)->get();

        return view('admin.settings.smsNotification', [
            'mobile_numbers' => $mobileNumbers,
        ]);
    }

    public function betReactivation()
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }

        return view('admin.settings.betReactivation');
    }
    
    public function postSaveAdminPhoneNumber()
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        $mobile = \request()->input('mobile_number');
        
        try {
            $service = new SmsService($mobile);
            $objs = $service->sendAdminSmsRegistration();
            $toArr = [];
            foreach ($objs as $obj) {
                $toArr[] = $obj->getTo();
            }
            $toStr = GeneratorUtils::generateStringWithDelimiter($toArr, ', ');
            
            DB::beginTransaction();
            $user = auth()->user();
            MobileNumber::create([
                'user_id' => $user->id,
                'mobile_number' => $service->filter($mobile),
                'do_not_send' => false,
                'role' => \App\System\Data\MobileNumber::ROLE_ADMIN,
            ]);
            DB::commit();
            
            $message = sprintf("The mobile number %s has been added to the list of numbers to be sent with STL admin notifications.", $toStr);
            Session::flash('sms-form-success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Session::flash('error-flash', $e->getMessage());
            report($e);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Session::flash('error-flash', "The mobile number might have a duplicate or an internal error has occured. Please contact tech support.");
        }
        
        return redirect()->route('sms-notification');
    }
    
    public function processDeleteAdminPhoneNumber($mobile_id)
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        try {
            DB::beginTransaction();
            $mobileNumber = MobileNumber::findOrFail($mobile_id);
            
            $numberToDelete = $mobileNumber->mobile_number;
            $mobileNumber->forceDelete();
            DB::commit();
            
            $service = new SmsService($numberToDelete);
            $service->sendAdminDeleteMessage();
            
            $message = sprintf("The mobile number %s has been deleted successfully.", $numberToDelete);
            Session::flash('sms-form-success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Session::flash('error-flash', "An internal error occured. Please contact tech support.");
        }
        
        return redirect()->route('sms-notification');
    }
}
