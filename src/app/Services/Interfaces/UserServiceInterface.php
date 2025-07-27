<?php

namespace App\Services\Interfaces;

use App\Models\User;

interface UserServiceInterface
{
  public function create(array $data);
  public function update(User $user, array $data);
  public function delete(User $user);
  public function restore(User $user);
  public function find($id);
  public function all();
  public function verify(User $user);
  public function findByEmail($email);
  public function getUserOrder($email);
  public function getMyOrders(User $user);
}
