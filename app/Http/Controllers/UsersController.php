<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Auth;
use Mail;
class UsersController extends Controller
{
    //用户列表
    public function index()
    {
        // $users = User::all();
        $users = User::paginate(5);
        return view('users.index', compact('users'));
    }
    //使用中间键-权限
    public function __construct()
    {
        $this->middleware('auth',[
            'except' => ['show','create','store', 'index','confirmEmail']
        ]);
        //未登录用户可访问注册页面
        $this->middleware('guest',[
                'only' => ['create']
        ]);
    }
    //注册表单
    public function create()
    {
        return view('users.create');
    }

    //展示个人信息
    public function show(User $user)
    {

        return view('users.show', compact('user'));
    }

    //添加
    public function store(Request $request)
    {
        $this->validate($request,[
                'name'      => 'bail|required|min:3|max:50',
                'email'     => 'bail|required|email|unique:users|max:255',
                'password'  => 'bail|required|confirmed|min:6'
        ]);

        $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password),
        ]);

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    //修改表单展示
    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    //修改操作
    public function update(User $user, Request $request)
    {
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);

        $data = [];

        $data['name'] = $request -> name;
        if($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user -> update($data);

        session()->flash('success','个人资料修改成功@@');

        return redirect()->route('users.show',$user->id);
    }

    //删除
    public function destroy(User $user)
    {
        $this->authorize('destory',$user);
        $user->delete();
        session()->flash('success',"删除用户 {$user->name} 成功@！！");
        return back();
    }

    //发送邮件
     protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'yangping0607@163.com';
        $name = 'young';
        $to = $user->email;
        $subject = "感谢注册MicroBlog 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    //激活
      public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}
