<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * 个人资料控制器
 */
class ProfileController extends Controller
{
    /**
     * 显示个人资料编辑页
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * 更新个人资料
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:50'],
            'email'     => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'signature' => ['nullable', 'string', 'max:255'],
            'avatar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            // 删除旧头像
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars/' . $user->id, 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', '个人资料已更新。');
    }
}
