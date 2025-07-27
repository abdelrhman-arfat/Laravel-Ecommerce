<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
  public function create(array $data)
  {
    $data['password'] = Hash::make($data['password']);
    return User::create($data);
  }

  public function update(User $user, array $data)
  {
    $user->update($data);
    return $user->fresh();
  }

  public function delete(User $user)
  {
    $user->is_active = false;
    $user->save();
    return $user;
  }

  public function restore(User $user)
  {
    $user->is_active = true;
    $user->save();
    return $user;
  }

  public function find($id)
  {
    return User::find($id);
  }

  public function all()
  {
    return User::all();
  }

  public function findByEmail($email)
  {
    return User::where('email', $email)->first();
  }

  public function getUserOrder($userId)
  {
    return User::with('orders')->findOrFail($userId)->orders;
  }


  public function getMyOrders(User $user)
  {
    return $user->orders;
  }

  public function verify(User $user)
  {
    $user->email_verified_at = now();
    return $user->save();
  }
}
