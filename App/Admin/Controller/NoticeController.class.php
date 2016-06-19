<?php
namespace Admin\Controller;
class NoticeController extends AdminController {
	public function sendtestmail() {

//				$result=send_mail(array(
		//			'to'=>'735579768@qq.com',
		//			'toname'=>'赵克立',
		//			'subject'=>'邮件主题',//主题标题
		//			'fromname'=>'我是赵克立你好哦',//发件人
		//			'body'=>'邮件成功'.date('Y/m/d H:i:s'),//邮件内容
		//			//'attachment'=>'E:\SVN\frame\DataBak\20141126-003119-1.sql.gz'
		//
		//		));
		$conf = array(
			'host'      => C('MAIL_SMTP_HOST'),
			'port'      => C('MAIL_SMTP_PORT'),
			'username'  => C('MAIL_SMTP_USER'),
			'password'  => C('MAIL_SMTP_PASS'),

			'fromemail' => C('MAIL_SMTP_FROMEMAIL'),
			'to'        => C('MAIL_SMTP_TESTEMAIL'),
			'toname'    => C('MAIL_SMTP_TESTEMAIL'),
			'subject'   => C('MAIL_SMTP_EMAILSUBJECT'), //主题标题
			'fromname'  => C('MAIL_SMTP_FROMNAME'), //发件人
			'body'      => C('MAIL_SMTP_CE'), //邮件内容
		);
		$sendmaillock = S('sendmaillock');
		if (empty($sendmaillock)) {
			$result = send_mail($conf);
			if ($result === true) {
				//设置邮件锁60秒后才可以再发送
				S('sendmaillock', true, 60);
				$this->success('邮件发送成功');
			} else {
				$this->error($result);
			}
		} else {
			$this->error('请60秒后再发送');
		}
	}
}