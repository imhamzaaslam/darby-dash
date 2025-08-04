<?php

namespace App\Http\Controllers;

use App\Models\User;

class TestController extends Controller
{
    public function testUpdateDeletedUserMails()
    {
        $users = User::onlyTrashed()->get();

        foreach ($users as $user) {
            $user->email = 'deleted_' . $user->email;
            $user->save();
        }

        return 'Deleted user emails updated successfully.';
    }
}