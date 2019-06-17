<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\System\Data\User;
use App\System\Services\UserService;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Outlet;


class LoginApiController extends Controller {
    
    public function index(Request $request) {
		try {
			if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
				$user = Auth::user();
				if ($user->is_admin) {
					throw new \Exception('Administrators are not yet allowed to login via mobile app.');
				}
				$userArr = $user->toArray();
				$userArr['default_outlet'] = $user->defaultOutlet == null ? [] : $user->defaultOutlet->toArray();
				$userArr['default_outlet']['outlet_name'] = $user->defaultOutlet == null ? "" : $user->defaultOutlet->outlet->name;

				$str = [
						'status' => true,
						'response_time' => microtime(true) - LARAVEL_START,
						'user' => $userArr,
						'outlet_id' => $user->defaultOutlet == null ? 0 : $user->defaultOutlet->toArray()['outlet_id']
					];
				$service = new UserService();
				$service->recordUserLogin($user, "Login via mobile");
			} else {
				$str = [
					'success' => false,
					'response_time' => microtime(true) - LARAVEL_START,
					'error' => 'Wrong email or password',
					'request' => $request->all()
				];	
			}
		} catch (\Exception $e) {
			$str = [
			    'success' => false,
			    'response_time' => microtime(true) - LARAVEL_START,
			    'error' => 'Internal server error. Please contact administrator. Error: ' . $e->getMessage(),
			    'request' => $request->all()
			];	
		}
        
		return response()->json($str);
	}

	public function updatePassword() {
		try {

			$inputJSON = file_get_contents('php://input');
			$data = json_decode($inputJSON, TRUE); 
			$oldPassword = $data['old_password'];
			$newPassword = $data['new_password'];
			$newPasswordConfirm = $data['new_password_confirm'];

			$authUser = auth()->user();
			$userData = new User($authUser);
            $userService = new UserService();
            $userService->updatePassword($oldPassword, $newPassword, $newPasswordConfirm);
			return response()->json(['success'=>true]);
                
        } catch (\Exception $e) {
			return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }
	}

	public function logUser() {
		try {

			$inputJSON = file_get_contents('php://input');
			$data = json_decode($inputJSON, TRUE); 
			$description = $data['log'];
			$action = $data['action'];
			
			$authUser = auth()->user();
			$userData = new User($authUser);
            $userService = new UserService();
			
			if ($action=='login') {
				$userService->recordUserLogin($authUser, $description);
			} else if ($action=='logout') {
				$userService->recordUserLogout($authUser, $description);
			} else {
				throw new \Exception('Invalid user log action');
			}

			return response()->json(['success'=>true]);
                
        } catch (\Exception $e) {
			return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }

	}
	
	public function showCase()
	{
	    return response()->json([
	        'api_author_name' => 'Joshua Paylaga',
	        'api_author_email' => 'joshuapaylaga@gmail.com',
	        'api_purpose' => 'To enable mobile and tablet devices to connect and to input bets on the go, catering hard to reach areas.',
	    ]);
	}

}
