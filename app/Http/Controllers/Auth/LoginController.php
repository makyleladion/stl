<?php

namespace App\Http\Controllers\Auth;

use App\DefaultOutlet;
use App\Http\Controllers\Controller;
use App\Outlet;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\System\Data\User;
use App\System\Services\UserService;
use App\System\Utils\ConfigUtils;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotification;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    protected function credentials(Request $request)
    {
        $isOffline = boolval(env('IS_OFFLINE', false));
        if ($isOffline) {
            return array_merge($request->only($this->username(), 'password'), ['status' => User::STATUS_ACTIVE, 'is_admin' => false]);
        }
        
        return array_merge($request->only($this->username(), 'password'), ['status' => User::STATUS_ACTIVE]);
    }

    protected function redirectTo()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // If Admin
        if ($user->is_admin) {
            return route('dashboard');
        }

        // If Owner
        $outlet = $user->outlets()->first();
        if (!$outlet) {

            // If Teller
            $defaultOutlet = $user->defaultOutlet()->first();
            $outlet = Outlet::find($defaultOutlet->outlet_id);
        }

        if ($outlet) {
            return route('outlet-dashboard', ['outlet_id' => $outlet->id]);
        }

        throw new \Exception('Failed to login.');
    }
    
    protected function authenticated(Request $request, $user)
    {
        $service = new UserService();
        $service->recordUserLogin($user);
    }
}
