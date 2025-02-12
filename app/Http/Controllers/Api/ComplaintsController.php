<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintsController extends Controller
{
    public function index(Request $request)
{

    $query = Complaint::query();

    if (!empty($request->search)) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    if (!empty($request->factory)) {
        $query->where('unique_id', 'like', '%' . $request->factory . '%');
    }
    if (!empty($request->status)) {
        $query->where('status', $request->status);
    }
    if (!empty($request->year)) {
        $query->whereYear('date', $request->year);
    }

    // Order by latest created_at
    $query->orderBy('date', 'desc');

    // Paginate with a default value of 10 per page
    $perPage = $request->input('limit', 10);
    $data = $query->paginate($perPage);

    // Append the `limit` parameter to the pagination URLs
    $data->appends(['limit' => $perPage]);

    // Transform the collection to include formatted date
    $data->getCollection()->transform(function ($item) {
        $item->formatted_created_at = \Carbon\Carbon::parse($item->date)->format('d M Y');
        return $item;
    });


    return response()->json($data);
}}
