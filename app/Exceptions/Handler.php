<?php

namespace App\Exceptions;

use Auth;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Maknz\Slack\Client;
use Request;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if (true === env('SLACK_NOTIFY') && $e) {
            $settings = [
                'username'   => env('SLACK_USERNAME'),
                'channel'    => env('SLACK_CHANNEL'),
                'link_names' => true
            ];

            $client = new Client(env('SLACK_WEBHOOKS'), $settings);

            $msgArr = [
                'From'     => env('APP_ENV'),
                'url'      => Request::url(),
                'Type'     => get_class($e),
                'ip'       => Request::ip(),
                'Username' => urlencode(Auth::check() ? Auth::user()->username : 'guest'),
                'Message'  => $e->getMessage(),
                'File'     => $e->getFile(),
                'Line'     => $e->getLine()
            ];

            $client->send(urldecode(json_encode($msgArr, JSON_PRETTY_PRINT)));
        }

        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        return parent::render($request, $e);
    }
}
