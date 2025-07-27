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
    return $user->update($data);
  }

  public function delete(User $user)
  {
    return $user->update(['is_active' => false]);
  }

  public function restore(User $user)
  {
    return $user->update(['is_active' => true]);
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

  public function getUserOrder($email)
  {
    return User::where('email', $email)->with('orders')->first();
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
