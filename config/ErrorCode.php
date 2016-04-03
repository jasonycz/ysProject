<?php

/**
 * APP接口错误码定义
 * @author yangping
 * @date 2016-03-01
 */
class ErrorCode {
	const COMMON_OK                     = 0; //成功
	const COMMON_PARAM_ERROR            = 1; //参数错误
   	const COMMON_USER_LOGIN_ERROR       = 100010; //用户不存在或者密码错误
   	const COMMON_USER_CHECKPHONE_ERROR  = 100011; //手机格式不正确
      const    COMMON_PASSWD_ERROR        = 100012; //密码错误
      const  COMMON_RESET_ERROR           = 100013; //重置密码失败
   	const COMMON_NOT_LOGIN				= 100012; //未登录
      const COMMON_USER_EXISTS         = 100013; //用户名已存在
      const COMMON_USER_EMPTY         = 100014; //用户名已存在
      const COMMON_USER_LOGIN_MODIFY  = 100015; //用户首次登陆，需要修改密码
   	const COMMON_STUDIO_ID_ERROR        = 200010; //工作室id非数字
   	const COMMON_STUDIO_EXIST           = 200011; //工作室已经存在
	const COMMON_FAIL_ADD_ARTICLE       = 400010; // 雕件文章发布失败     
   	const COMMON_EMPTY_DATA             = 500010; //查看发布后的作品数据为空
	const COMMON_GETVERTIFY_ERROR       = 600010; //获取验证码失败
	const COMMON_VERTIFY_ERROR          = 600011; //验证码错误
   const COMMON_CRAFT_DEL_ERROR        = 700010;  //删除玉雕失败
   const COMMON_CRAFT_ZERO             = 700011; //雕件数为0
}
?>

