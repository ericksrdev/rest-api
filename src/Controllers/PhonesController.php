<?php

namespace App\Controllers;

use App\Lib\Http\Controller;
use App\Lib\Http\Request;
use App\Lib\Http\RestController;
use App\Models\Phone;
use App\Models\User;

class PhonesController extends Controller implements RestController
{
    public function index(Request $request)
    {
        $phones = Phone::findAll();

        return $this->json($phones);
    }

    public function show(Request $request)
    {
        $requiredParams = ['phone_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (number, user_id)'], 400);
        }

        $phone = Phone::find($request->get('phone_id'));

        if ($phone === null)
        {
            return $this->json(['Phone not found'], 404);
        }

        return $this->json($phone);
    }

    public function store(Request $request)
    {
        $requiredParams = ['number', 'user_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (number, user_id)'], 400);
        }

        $phone = new Phone();

        $phone->country_code = $request->has('country_code') ? $request->get('country_code') : '1';

        $phone->number = $request->get('number');

        $user = User::find($request->get('user_id'));

        if ($user === null)
        {
            return $this->json(['message' => 'Requested user not found'], 404);
        }

        $phone->user_id = (int) $user->id;

        if ($phone->save())
        {
            return $this->json($phone, 201);
        }

        return $this->json(['message' => 'An error occurred while storing the phone'], 500);
    }

    public function update(Request $request)
    {
        $requiredParams = ['number', 'phone_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (number, user_id)'], 400);
        }

        $phone = Phone::find($request->get('phone_id'));

        if ($phone === null)
        {
            return $this->json(['message' => 'Phone record not found'], 404);
        }

        $phone->number       = $request->get('number');
        $phone->country_code = $request->has('country_code') ? $request->get('country_code') : $phone->country_code;

        if ($phone->save())
        {
            return $this->json($phone, 200);
        }

        return $this->json(['message' => 'An error occurred while updating the phone'], 500);
    }

    public function destroy(Request $request)
    {
        $requiredParams = ['phone_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (phone_id)'], 400);
        }

        $phone = Phone::find($request->get('phone_id'));

        if ($phone->delete())
        {
            return $this->json([]);
        }
        else
        {
            return $this->json(['message' => 'Something went wrong while deleting the phone'], 500);
        }
    }

}