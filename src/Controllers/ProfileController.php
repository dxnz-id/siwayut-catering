<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Services\ProfileService;

class ProfileController extends BaseController
{
    public function __construct(
        private ProfileService $profileService
    ) {
        parent::__construct();
    }

    public function edit(Request $request): void
    {
        $user = $this->currentUser();
        $profile = $this->profileService->getProfile((int) $user['id']);

        $this->render('profile/edit', [
            'title' => __('profile'),
            'profile' => $profile,
        ]);
    }

    public function update(Request $request): void
    {
        $user = $this->currentUser();
        $userId = (int) $user['id'];

        $data = $request->only(['name', 'email', 'phone', 'address']);

        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = reset($errors);
            $this->withOldInput($data);
            $this->redirectWithFlash('/profile', 'error', $firstError);
        }

        $this->profileService->updateProfile($userId, $data);

        Session::set('user', array_merge(Session::get('user'), [
            'name' => $data['name'],
            'email' => $data['email'],
        ]));

        $this->redirectWithFlash('/profile', 'success', __('profile_updated'));
    }

    public function changePassword(Request $request): void
    {
        $user = $this->currentUser();
        $userId = (int) $user['id'];

        $validator = new Validator(Database::getInstance());
        $validator->validate($request->only(['current_password', 'new_password', 'new_password_confirmation']), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = reset($errors);
            $this->redirectWithFlash('/profile', 'error', $firstError);
        }

        $changed = $this->profileService->changePassword($userId, $request->input('current_password'), $request->input('new_password'));

        if (!$changed) {
            $this->redirectWithFlash('/profile', 'error', __('incorrect_password'));
        }

        $this->redirectWithFlash('/profile', 'success', __('password_changed'));
    }
}
