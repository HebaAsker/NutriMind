<?php

namespace App\Lib;

use Pusher\Pusher;


class PusherFactory
{
    //Return a new instance of pusher to be used by other classes to subscribe and fire events
    public static function create()
    {
        return new Pusher(
            env("PUSHER_APP_KEY"), // public key
            env("PUSHER_APP_SECRET"), // Secret
            env("PUSHER_APP_ID"), // App_id
            array(
                'cluster' => env("PUSHER_APP_CLUSTER"), // Cluster
                'encrypted' => true,
            )
        );
    }
}
