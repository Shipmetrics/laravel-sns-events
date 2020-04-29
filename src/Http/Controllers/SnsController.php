<?php

namespace Rennokki\LaravelSnsEvents\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Rennokki\LaravelSnsEvents\Events\SnsEvent;
use Rennokki\LaravelSnsEvents\Events\SnsSubscriptionConfirmation;

class SnsController extends Controller
{
    /**
     * Handle the incoming SNS event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle(Request $request)
    {
        $message = json_decode($this->getContent($request), true);

        if (isset($message['Type'])) {
            if ($message['Type'] === 'SubscriptionConfirmation') {
                file_get_contents($message['SubscribeURL']);

                event(new SnsSubscriptionConfirmation(
                    $request->headers->all()
                ));
            }

            if ($message['Type'] === 'Notification') {
                event(new SnsEvent(
                    $message, $request->headers->all()
                ));
            }
        }

        return response('OK', 200);
    }

    /**
     * Get the payload content from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return null|string
     */
    protected function getContent(Request $request)
    {
        return $request->getContent() ?: file_get_contents('php://input');
    }
}
