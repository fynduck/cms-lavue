<?php

namespace Modules\Redirect\Http\Controllers;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Modules\Redirect\Entities\Redirect;
use Modules\Redirect\Http\Requests\RedirectRequest;
use Modules\Redirect\Transformers\RedirectFormResource;
use Modules\Redirect\Transformers\RedirectListResource;

class RedirectController extends AdminController
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $redirects = Redirect::filter($request)->paginate(25);

        return RedirectListResource::collection($redirects);
    }

    /**
     * Store a newly created resource in storage.
     * @param RedirectRequest $request
     * @return bool
     */
    public function store(RedirectRequest $request)
    {
        Redirect::create(
            [
                'from'        => $request->get('from'),
                'to'          => $request->get('to'),
                'status_code' => $request->get('status_code'),
                'status'      => $request->get('status') ?? 0
            ]
        );

        return true;
    }

    public function show($id)
    {
        $item = Redirect::find($id);

        if (!$item) {
            $item = new Redirect();
        }

        return (new RedirectFormResource($item));
    }

    /**
     * Update the specified resource in storage.
     * @param RedirectRequest $request
     * @param Redirect $redirect
     * @return bool
     */
    public function update(RedirectRequest $request, Redirect $redirect)
    {
        $redirect->from = $request->get('from');
        $redirect->to = $request->get('to');
        $redirect->status_code = $request->get('status_code');
        $redirect->active = $request->get('active');
        $redirect->save();

        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return int
     */
    public function destroy($id)
    {
        return Redirect::destroy($id);
    }
}
