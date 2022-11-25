<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Plan;
use App\Projects;
use App\User;
use App\Utility;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;
        }
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if($user->delete_status == 0)
        {
            auth()->logout();
        }

        if($user->type == 'company')
        {
            $free_plan = Plan::where('price', '=', '0.0')->first();
            if($user->plan != $free_plan->id)
            {
                if(date('Y-m-d') > $user->plan_expire_date)
                {
                    $user->plan             = $free_plan->id;
                    $user->plan_expire_date = null;
                    $user->save();

                    $projects = Projects::where('created_by', '=', \Auth::user()->creatorId())->get();
                    $users    = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->get();
                    $clients  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', 'client')->get();

                    $projectCount = 0;
                    foreach($projects as $project)
                    {
                        $projectCount++;
                        if($projectCount <= $free_plan->max_projects)
                        {
                            $project->is_active = 1;
                            $project->save();
                        }
                        else
                        {
                            $project->is_active = 0;
                            $project->save();
                        }
                    }

                    $userCount = 0;
                    foreach($users as $user)
                    {
                        $userCount++;
                        if($userCount <= $free_plan->max_users)
                        {
                            $user->is_active = 1;
                            $user->save();
                        }
                        else
                        {
                            $user->is_active = 0;
                            $user->save();
                        }
                    }
                    $clientCount = 0;
                    foreach($clients as $client)
                    {
                        $clientCount++;
                        if($clientCount <= $free_plan->max_clients)
                        {
                            $client->is_active = 1;
                            $client->save();
                        }
                        else
                        {
                            $client->is_active = 0;
                            $client->save();
                        }
                    }

                    return redirect()->route('dashboard')->with('error', 'Your plan expired limit is over, please upgrade your plan');
                }
            }
        }
    }

    // Login Form
    public function showLoginForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.login', compact('lang'));
    }

    public function showLinkRequestForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.passwords.email', compact('lang'));
    }
}
