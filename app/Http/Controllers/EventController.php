<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Auth\Events\Validated;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $event = Event::orderBy('created_at', 'DESC')->get();

        $respone = [
            'status' => Response::HTTP_OK,
            'message' => "List Event Order By Created Date",
            'data' => $event

        ];
        return response()->json($respone, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event' => ['required'],
            'date' => ['required'],
            'location' => ['required'],
            'description' => ['required'],
            'city' => ['required'],
            'province' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $event = Event::create($request->all());
            $respone = [
                'status' => Response::HTTP_CREATED,
                'message' => "Event was created",
                'data' => $event
            ];

            return response()->json($respone, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);
        $respone = [
            'status' => Response::HTTP_OK,
            'message' => "Show event with id " . $id,
            'data' => $event
        ];

        return response()->json($respone);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'event' => ['required'],
            'date' => ['required'],
            'location' => ['required'],
            'description' => ['required'],
            'city' => ['required'],
            'province' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $event->update($request->all());
            $respone = [
                'status' => Response::HTTP_OK,
                'message' => "Event Updated",
                'data' => $event
            ];

            return response()->json($respone, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        try {
            $event->delete();
            $respone = [
                'status' => Response::HTTP_OK,
                'message' => "Event was deleted"
            ];

            return response()->json($respone, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->errorInfo
            ]);
        }
    }

    /**
     * Search the specified resource from storage.
     *
     * @param  str  $value
     * @return \Illuminate\Http\Response
     */
    public function search($value)
    {
        $event = Event::where('event', 'like', '%' . $value . '%')
            ->orWhere('city', 'like', '%' . $value . '%')
            ->orWhere('province', 'like', '%' . $value . '%')
            ->orWhere('location', 'like', '%' . $value . '%')
            ->orWhere('date', 'like', '%' . $value . '%')
            ->get();

        try {
            $respone = [
                'status' => Response::HTTP_OK,
                'message' => "search result",
                'data' => $event
            ];

            return response()->json($respone, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->errorInfo
            ]);
        }
    }
}
