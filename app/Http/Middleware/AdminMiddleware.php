<?php
/**
 * Created by PhpStorm.
 * User: Stas
 * Date: 27.01.2016
 * Time: 10:31
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $right
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $right = 'view')
    {
        $ajaxRoutes = [
            'admin-live-select-list',
            'trans-slug-list'
        ];
        $currentRouteName = $request->route()->getName();

        if (!auth()->check()) {
            if ($request->ajax()) {
                return response('Permission denied', 401);
            }

            abort(401);
        } elseif (auth()->user()->isAdmin() || in_array($currentRouteName, $ajaxRoutes)) {
            return $next($request);
        } elseif (auth()->user()->roles->groupPermission && $right) {
            $explodeWithLine = explode('-', $currentRouteName);
            $explodeRoute = explode('.', $currentRouteName);
            $listPermission = null;
            if (array_key_exists(1, $explodeRoute)) {
                switch ($explodeRoute[1]) {
                    case 'create':
                    case 'store':
                        $right = $right != 'view' ? $right : 'create';
                        break;
                    case 'update':
                    case 'edit':
                        $right = $right != 'view' ? $right : 'edit';
                        break;
                    case 'show':
                        $right = 'index';
                        break;
                    case 'destroy':
                        $right = $right != 'view' ? $right : 'destroy';
                        break;
                    default:
                        $right = count($explodeRoute) > 2 ? $explodeRoute[2] : $explodeRoute[1];
                        break;
                }
            } elseif (count($explodeWithLine)) {
                $isList = array_pop($explodeWithLine);

                if ($isList === 'list') {
                    $listPermission = implode('-', $explodeWithLine);
                }
            }

            if (!$listPermission) {
                if (auth()->user()->roles->groupPermission->where('name', $explodeRoute[0])->where('rights', $right)->count()) {
                    return $next($request);
                }
//            } else {
//                return $next($request);
//                if (auth()->user()->roles->groupPermission->where('name', $listPermission)->where('rights', 'index')->count())
//                    return $next($request);
            }
        }

//        if ($request->ajax())
        return response('Permission denied', 403);
    }
}
