<?php

namespace App\Helper;

use App\Models\Menu;
use App\Models\MenuRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Exception;
use JWTAuth;

class Helper
{
  /**
   * Helper for greeting message
   * based on hour time
   * @return string
   */
  public static function greeting()
  {
    $carbon = Carbon::now('Asia/Jakarta');
    $hour = $carbon->format('H');
    if ($hour < 12) {
      return 'Selamat Pagi';
    } elseif ($hour < 17) {
      return 'Selamat Siang';
    }
    return 'Selamat Malam';
  }

  public static function GenerateReportNumber($length = 20)
  {
    $characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  public static function menu()
  {
    $role_id = Auth::user()->roles->first()->id;
    $role_menus = MenuRole::select('menu_id')->where('role_id', $role_id)->get();
    $menu =  Menu::whereIn('id', $role_menus)->where('parent_id', '=', 0)->with('childs')->orderBy('order')->get();
    return response()->json($menu);
  }
}
