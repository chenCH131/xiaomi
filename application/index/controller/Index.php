<?php
    namespace app\index\controller;
    
    use think\Controller;
    use think\Session;
    use app\index\model\Dengmodel;
    use app\index\model\Notemodel;
    
    class Index extends Controller
    {
        //默认进入页面
        public function index()
        {   
            $phone = Session::get('phone');
            
            $this->assign('phone',$phone);
            return $this->fetch('index/index');
        }
        
        //登录操作
        /*
         *  作者：陈欢
         *  审：李昊枕
         *  时间：2020-09-01
         */
        public function deng()
        {
            if (request()->isAjax()){
                $vicode = input('ajcode');  //验证码
                
    			if(!captcha_check($vicode) ){
    			    return Json(['code' => -3 , 'data' => '验证码错误']);   //验证码错误
    			}else{
    			    $naDuser = input('ajname');  //账号
    			    $naDpass = input('ajpass');  //密码
    			    
    			    $where = [
    			        'username' => $naDuser
    			    ];
    			    
    			    $whereht = [
    			        'username' => trim($naDuser),
    			        'password' => trim(md5($naDpass.config('salt')))
    			    ];
    			    
    			    
    			    $sent= new Dengmodel();
    			    $res = $sent->mylogin($where);		//判断用户名是否存在
    			    $res2 = $sent->mylogin($whereht);	//判断用户名或密码是否正确
    			    
    			    if(empty($res)){
    			        return Json(['code' => -1 , 'data' => '用户名不存在']);                  //用户名不存在
    			    }else if(empty($res2)){
    			        return Json(['code' => -2 , 'data' => '用户名或密码不正确，请重新输入']);    //用户名或密码不正确，请重新输入
    			    }else{
    			        Session('phone',$naDuser);      //写入seesion
    			        return Json(['code' => 1 , 'data' => '登录成功！']);   //登录成功！
    			    }
    			}
            }
            return $this->fetch('login/login');
        }
        
        //注册操作
        /*
         *  作者：张佳鹏
         *  审：李昊枕
         *  时间：2020-09-03
         */
        public function note()
        {
            if( request()->isAjax() ){
                $naDuser = input('naDuser');
                $naDpass = input('naDpass');
                $naDphone = input('naDtel');
                $vicode = input('vicode');
                
                if(!captcha_check($vicode)){
                    return Json(['code' => -3 , 'data' => '验证码错误']);
                }
                
                $register = new Notemodel();
                
                //判断用户名是否存在的
                $where = [
                    'username' => $naDuser
                ];
                //注册成功需要插入用户表中的参数
                $charu = [
                    'id' => '',
                    'username' => $naDuser,
                    'password' => trim(md5($naDpass.config('salt'))),
                    'phone' => $naDphone,
                    'date' => date("Y-m-d h:i:s"),
                    'state' => '1'
                ];
           
                $res = $register->is_zai($where);
                
                
                if( $res ){
                    return Json(['code' => -1 , 'data' => '该账号已经被注册，请重新注册']);
                }else{
                    $reg = $register->addDenglu($charu);
                    if(!empty($reg)){
                        return Json(['code' => 1 , 'data' => '注册成功']);
                    }else{
                        return Json(['code' => -2 , 'data' => '注册失败']);
                    }
                }
                
            }
            return $this->fetch('note/register');
        }
    }
