<?php

namespace App\Controllers;

use App\Lib\Http\Controller;
use App\Lib\Http\Request;
use App\Lib\Http\RestController;
use App\Models\Email;
use App\Models\User;

class EmailsController extends Controller implements RestController
{
    public function index(Request $request)
    {
        $searchParams = $request->all();

        if (count($searchParams) > 0)
        {
            $searchCriteria = [];
            foreach ($searchParams as $key => $val)
            {
                $searchCriteria[] = [$key, 'LIKE', "%$val%"];
            }

            $emails = Email::findWhereMany($searchCriteria, 'OR',false);
        }
        else
        {
            $emails = Email::findAll();
        }

        return $this->json($emails);
    }

    public function show(Request $request)
    {
        $requiredParams = ['email_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (number, user_id)'], 400);
        }

        $email = Email::find($request->get('email_id'));

        if ($email === null)
        {
            return $this->json(['Email not found'], 404);
        }

        return $this->json($email);
    }

    public function store(Request $request)
    {
        $requiredParams = ['email','user_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (email, user_id)'], 400);
        }

        $email = new Email();

        $email->email = $request->get('email');

        $user = User::find($request->get('user_id'));

        if ($user === null)
        {
            return $this->json(['message' => 'Requested user not found'], 404);
        }

        $email->user_id = (int) $user->id;

        if ($email->save())
        {
            return $this->json($email, 201);
        }

        return $this->json(['message' => 'An error occurred while storing the email'], 500);
    }

    public function update(Request $request)
    {
        $requiredParams = ['email', 'email_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (email, user_id)'], 400);
        }

        $email = Email::find($request->get('email_id'));

        if ($email === null)
        {
            return $this->json(['message' => 'Email record not found'], 404);
        }

        $email->email = $request->get('email');


        if ($email->save())
        {
            return $this->json($email, 200);
        }

        return $this->json(['message' => 'An error occurred while storing the email'], 500);
    }

    public function destroy(Request $request)
    {
        $requiredParams = ['email_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (email_id)'], 400);
        }

        $email = Email::find($request->get('email_id'));

        if ($email->delete())
        {
            return $this->json([]);
        }
        else
        {
            return $this->json(['message' => 'Something went wrong while deleting the email'], 500);
        }
    }
}