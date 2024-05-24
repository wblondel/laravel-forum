<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();

        // TODO: deactivate user before deletion
        //  The user will be called "Forum User" throughout the app, and his/her profile won't be accessible.
        //  Proceed with the deletion after 72 hours.
        //  User can reactivate his/her account at any time before the deletion by logging into the forum.
        //  Send an email confirming the deactivation.
        //  User will be given a link to track the deletion request.It will show deactivation date,
        //  start date of the deletion process, and end date of the deletion process.
        //  Send an email confirming the initiation of the deleting process.

        // TODO: anonymise user data upon deletion
        //  Posts will be kept.
        //  Private messages will be kept, so that they can still be read by his/her friends.
        //  If this user has private messages with another deleted user, the messages will be deleted.
        //  THe user will be called "Forum user" throughout the app, and the profile information will be deleted.

        $user->delete();
    }
}
