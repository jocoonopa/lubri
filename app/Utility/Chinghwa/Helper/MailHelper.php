<?php

namespace App\Utility\Chinghwa\Helper;

use Mail;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailHelper 
{
	/**
	 * [
	 *  'template' => $var
	 * 	'subject' => $var,
	 * 	'title' => $var,
	 * 	'filepath' => ['$path1', '$path2'],
	 * 	'cc' => []{'$email' => '$name'}
	 * 	'to' => []{'$email' => '$name'}
	 * ] 
	 * 
	 * @var array
	 */
	protected $initConfig;

	protected $m;

	public function __construct(array $initConfig)
	{
		$this->setInitConfig($initConfig);
	}

	public function mail($message = 'Mail send complete')
	{
		if (Mail::send($this->initConfig['template'], ['title' => $this->initConfig['title']], $this->getCallbackFun())) {
			return $message;
		}
	}

	public function setInitConfig(array $initConfig)
	{
		$this->initConfig = $initConfig;

		return $this;
	}

	public function getInitConfig()
	{
		return $this->initConfig;
	}

	/**
	 * 看一下 resolver 那邊的文件
	 * 
	 * @param  array   $initConfig
	 * @return boolean            
	 */
	protected function isValid(array $initConfig)
	{
		return true;
	}

	protected function setM($m)
	{
		$this->m = $m;

		return $this;
	}

	protected function getCallbackFun()
	{
		return function ($m) {
            $this
            	->setM($m)
            	->subject()
            	->attach()
            	->cc()
            	->to()
            ;
        };
	}

	protected function subject()
	{
		$this->m->subject($this->initConfig['subject']);

		return $this;
	}

	protected function attach()
	{
		foreach ($this->initConfig['filepath'] as $path) {
            $this->m->attach($path);
        }

        return $this;
	}

	protected function cc()
	{
		foreach ($this->initConfig['cc'] as $email => $name) {
            $this->m->cc($email, $name);
        }

        return $this;
	}

	protected function to()
	{
		foreach ($this->initConfig['to'] as $email => $name) {
            $this->m->to($email, $name);
        }

        return $this;
	}
}