<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\FirebaseNotify;
use Kreait\Firebase\Factory;

class FirebaseService extends Controller
{
    function newFactory(): Factory
    {
        return (new Factory)->withServiceAccount(storage_path('app/firebase-adminsdk.json'));
    }

    function sendOneUser(User $user, $title, $body, $data = [])
    {
        $user->notify(new FirebaseNotify(['title' => $title, 'body' => $body, 'data' => $data]));
    }

    function sendManyUsers($users, $title, $body, $data = [])
    {
        foreach ($users as $user) {
            $this->sendOneUser($user, $title, $body, $data);
        }
    }
}
