<?php

namespace App\Controllers;

use App\Lib\Http\Controller;
use App\Lib\Http\Request;
use App\Lib\Http\Storage;
use App\Models\User;

class UsersController extends Controller
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

            $users = User::findWhereMany($searchCriteria, 'OR');
        }
        else
        {
            $users = User::findAll();
        }

        return $this->json($users);
    }

    public function show(Request $request)
    {
        $user_id = $request->get('user_id');

        if ($user_id === null)
        {
            throw new \Exception('Undefined user id value');
        }

        $user = User::find($user_id);

        if ($user === null)
        {
            return $this->json(['message' => 'User not found'], 404);
        }

        return $this->json($user);
    }

    public function store(Request $request)
    {
        $requiredParams = ['first_name', 'surname'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (first_name, surname)'], 400);
        }

        $user = new User();

        $user->first_name = $request->get('first_name');
        $user->surname    = $request->get('surname');

        if ($request->has('profile_picture') && $request->isValidFile('profile_picture'))
        {
            $profilePicturePath = $this->handleProfilePictureUpload($request->get('profile_picture'));

            if ($profilePicturePath === null)
            {
                $this->json(['message' => 'An error occurred while processing your uploaded file, try again'], 500);
            }

            $user->profile_picture_uri = $profilePicturePath;
        }

        if ($user->save())
        {
            return $this->json($user, 201);
        }
        else
        {
            return $this->json(['message' => 'An error occurred while storing the user'], 500);
        }
    }

    public function update(Request $request)
    {
        $requiredParams = ['first_name', 'surname', 'user_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (first_name, surname, user_id)'], 400);
        }

        $user = User::find($request->get('user_id'));

        if ($user === null)
        {
            return $this->json(['message' => 'The requested user is not found'], 404);
        }

        $user->first_name = $request->get('first_name');
        $user->surname    = $request->get('surname');

        if ($request->has('profile_picture') && $request->isValidFile('profile_picture'))
        {
            $profilePicturePath = $this->handleProfilePictureUpload($request->get('profile_picture'), true);

            if ($profilePicturePath === null)
            {
                $this->json(['message' => 'An error occurred while processing your uploaded file, try again'], 500);
            }

            $user->profile_picture_uri = $profilePicturePath;
        }

        if ($user->save())
        {
            return $this->json($user);
        }
        else
        {
            return $this->json(['message' => 'An error occurred while updating the user'], 500);
        }

    }

    public function destroy(Request $request)
    {
        $requiredParams = ['user_id'];

        if ( ! $request->validateRequired($requiredParams))
        {
            return $this->json(['message' => 'Required parameters not found (user_id)'], 400);
        }

        $user = User::find($request->get('user_id'));

        if ($user === null)
        {
            return $this->json(['message' => 'The requested user is not found'], 404);
        }

        if ($user->delete())
        {
            return $this->json([]);
        }
        else
        {
            return $this->json(['message' => 'Something went wrong while deleting the user'], 500);
        }
    }

    private function handleProfilePictureUpload(array $file, $copy = false)
    {
        if (Storage::uploadIsImage($file))
        {
            $uploadedFilePath = Storage::storeInLocalDisk($file, $copy);

            if ($uploadedFilePath === null)
            {
                return null;
            }

            return $uploadedFilePath;
        }

        return null;
    }

}