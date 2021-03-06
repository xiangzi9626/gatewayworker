<?php
namespace App\Http\Controllers\user;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class UserLoginController extends Controller
{
    public function captcha(){
        return include public_path("/common/captcha/captcha_EN.php");
    }
    public function login(){
        //$data=file_get_contents("php://input");
        //$data=json_decode($data,true);
        $username=trim($_POST["username"]);
        $password=md5($_POST["password"]);
        //$captcha=trim($data["captcha"]);
        /*if (strcasecmp($captcha,$_SESSION["captcha"])!==0){
            echo "验证码错误";
            exit();
        }*/
        $user=DB::select("select * from user where username=? and password=?",[$username,$password]);
        if (count($user)==0){
            echo "账号或密码错误";
        }else{
            session(["user"=>""]);
            session(["user"=>$user[0]]);
            return "ok";
        }
    }
    public function logout(){
        session(["user"=>""]);
    }
}
